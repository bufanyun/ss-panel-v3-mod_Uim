<?php

namespace App\Models;

use App\Services\Auth;
use App\Models\AgentLevel;
use App\Models\Shop;
use App\Models\InviteCode;
use App\Models\User;
use App\Models\AgentIncome;
use App\Models\AgentMoneyLog;
use App\Utils\Tools;
use App\Utils\Common;

use Ozdemir\Datatables\Datatables;
use App\Utils\DatatablesHelper;
use App\Services\Config;
use function React\Promise\Stream\first;

/**
 * AgentConfig Model
 */
class AgentConfig extends Model
{
    protected $connection = 'default';
    protected $table = 'agent_config';


    /**
     * @Notes:
     * 获取收款码
     * @Interface getPaymentCode
     * @Author: 133814250@qq.com MengShuai
     * @Date: 2020/3/13   15:31
     * @param $uid
     * @return mixed
     */
    public static function getPaymentCode($uid){
        $result = self::where(['uid' => $uid])->select('cash')->first();
        $cash = json_decode($result->cash,true);
        return $cash['img'];
    }


    /**
     * @Notes:
     * 获取指定代理的成本价
     * @Interface getAgentCost
     * @Author: 133814250@qq.com MengShuai
     * @Date: 2020/3/2   11:41
     * @param $uid
     */
    public static function getAgentCost($uid){
        $level = AgentLevel::where([
            'id' => self::where(['uid' => $uid])->value('level')
            ])->first();
        if($level == null) return false;

        $shop = json_decode(self::where(['uid' => $level->uid])->value('shop'),true);
        $shops = [];
        foreach ($shop as $k => $v){
            $shops[$k] = $v["p{$level->level}"];
        }
        return $shops;
    }

    /**
     * @Notes:
     * 根据套餐id获取代理对应成本价及上级代理id
     * $type=income 获取超管成本价
     * @Interface getCost
     * @Author: 133814250@qq.com MengShuai
     * @Date: 2020/2/27   17:51
     * @param $id
     * @param $level
     * @param $shopid
     */
    public static function getCost($level,$shopid,$income_uid=null){
        $boss  = AgentLevel::where('id','=',$level)->first();
        $agent = self::where('uid','=',$boss->uid)->first();
        $shop  =  json_decode($agent->shop, true);

        $data = [
            'cost' => sprintf("%.2f",$shop["id{$shopid}"]["p{$boss->level}"]),
            'uid' => $agent->uid,
            'level' => $agent->level,
        ];

        //成本价最低限制
        if($data['cost'] < 0.1){
            return false;
        }

        //超管无成本
        if(($boss->uid <= 1) && ($income_uid == 1)){
            $data = [
                'cost' => 0.00,
                'uid' => $agent->uid,
                'level' => $agent->level,
            ];
        }
        return $data;
    }


    /**
     * @Notes:
     * 返佣上级
     * @Interface addIncome
     * @Author: 133814250@qq.com MengShuai
     * @Date: 2020/2/27   19:28
     */
    public static function addIncome($uid,$user_id,$level,$shopid,$price,$remark){
        $time = time();
        $boss = self::getCost($level,$shopid,$uid);
        if($boss == false)return false;
        //购买价格大于成本价&&上级代理身份存在
        if ((bccomp($price, $boss['cost'], 2) == 1)
            && ($user = User::where(['id' => $uid, 'is_admin' => 1])->first())) {
            //如果是超管 则不需要计算成本差价
            if($uid!=1) {
                $income = sprintf("%.2f",$price-$boss['cost']);
            }else{
                $income = $price;
            }

            //资金变动
            AgentMoneyLog::Newly($uid, $income,
                $user->money,($user->money+$income),"用户(#{$user_id})购买套餐：{$remark}");
            $user->money += $income;
            $user->save();

            //插入收益记录
            $AgentIncome = new AgentIncome();
            $AgentIncome->uid       = $uid;
            $AgentIncome->user_id   = $user_id;
            $AgentIncome->type      = 'shop';
            $AgentIncome->type_id   = $shopid;
            $AgentIncome->money     = $price;
            $AgentIncome->income    = $income;
            $AgentIncome->addtime   = $time;
            $AgentIncome->remark    = $remark;
            $AgentIncome->save();
        }
        /**
         * 代理id大于1（超管），无限回调返佣
         */
        if($uid > 1) {
            $getSuperior = self::getAgentSuperior($uid);
            if ($getSuperior != false) {
                return self::addIncome($getSuperior, $uid, $boss['level'], $shopid, $boss['cost'], '下级收益');
            }
        }
        return true;
    }

    /**
     * @Notes:
     * 获取代理的上级代理
     * @Interface getAgentSuperior
     * @Author: 133814250@qq.com MengShuai
     * @Date: 2020/2/27   20:41
     * @param $uid
     * @return bool
     */
    public static function getAgentSuperior($uid){
        $Superior = User::where(['id'=>$uid])->value('ref_by');
        $Superior = ($Superior==0) ? 1 : $Superior;
        return  $Superior;
    }

    /**
     * @Notes:
     * 根据域名获取代理商配置
     * @Interface domain
     * @Author: 133814250@qq.com MengShuai
     * @Date: 2020/2/26   21:49
     * @return bool
     */
    public static function domain($host=null){
        $domain = ($host!=null) ? $host : $_SERVER['HTTP_HOST'];
        $lists = self::where('domain', 'LIKE', "%{$domain}%")->get();
        if($lists == null) return false;
        //判断域名是否完全匹配
        $exist = false;
        foreach ($lists as $k => $v){
            $domain_array = explode(',',$v['domain']);
            //存在匹配直接返回
            if(in_array(trim($domain),$domain_array)){
                $kk = $k;
                $exist = true;
                break;//终止循环
            }
        }
        if($exist == false) return false;

        $agent = $lists[$kk];

        /**
         * 用户已登录 无视域名刷新上级代理配置
         *
         */
        $user = Auth::getUser();
        if($user->isLogin != null) {
            $getSuperior = self::getSuperior($user->ref_by,true);
            if($getSuperior != false){
                $agent = $getSuperior;
            }
        }

        $baseUrl = explode(',',$agent->domain)[0];
        if (Common::is_domain($baseUrl) == false) {
            $baseUrl = Config::get('defaultUrl');
        }
        $agent->domain= $baseUrl;
        $agent->cash  = json_decode($agent->cash,true);
        $agent->shop  = json_decode($agent->shop,true);
        $agent->panel = json_decode($agent->panel,true);
        $code = InviteCode::where('user_id', $agent->uid)->value('code');
        if ($code == null) {
            self::addDomainInviteCode($agent->uid);
            $code = InviteCode::where('user_id', $agent->uid)->first();
        }
        $agent->code  = $code;
        return $agent;
    }


    /**
     * @Notes:
     * 获取用户上级代理信息
     * 1.如果上级不是代理商则循环获取
     * 2.如果上级代理商是无归属或超级管理员则直接返回 fasle
     * @Interface getSuperior
     * @Author: 133814250@qq.com MengShuai
     * @Date: 2020/2/27   16:33
     * @param $id
     * @return bool
     */
    public static function getSuperior($id,$all=false){
        $id = ($id==0) ? 1 : $id;
        $user = User::where('id','=',$id)->first();
        if($user->is_admin == 1) {
            return self::where('uid','=',$id)->first();
        }

        if($all==true && $user->ref_by > 0){
            return self::getSuperior($user->ref_by);
        }

        if($all==false && $user->ref_by > 1) {
            return self::getSuperior($user->ref_by);
        }
        return false;
    }

    /**
     * @Notes:
     * 生成域名代理无邀请码
     * @Interface addDomainInviteCode
     * @Author: 133814250@qq.com MengShuai
     * @Date: 2020/2/26   21:59
     * @param $uid
     */
    public static function addDomainInviteCode($uid)
    {
        $code = new InviteCode();
        while (true) {
            $temp_code = Tools::genRandomChar(4);
            if (InviteCode::where('user_id', $uid)->count() == 0) {
                break;
            }
        }
        $code->code = $temp_code;
        $code->user_id = $uid;
        $code->save();
    }

    /**
     * @Notes:
     * 获取基本配置信息
     * @Interface agent
     * @Author: 133814250@qq.com MengShuai
     * @Date: 2020/2/25   22:09
     * @return mixed
     */
    public function agent()
    {
        $agent = self::where('uid','=',Auth::getUser()->id)->first();
        if($agent == null){
            return false;
        }
        $agent->endtime = date("Y-m-d H:i:s",$agent->endtime);
        $agent->addtime = date("Y-m-d H:i:s",$agent->addtime);
        $agent->level = AgentLevel::where('id','=',$agent->level)->first();
        $agent->cash = json_decode($agent->cash,true);
        $agent->shop = json_decode($agent->shop,true);

        $panel = json_decode($agent->panel,true);
        $panel['enable_chatra'] = isset($panel['enable_chatra']) ? $panel['enable_chatra'] : '';
        $panel['chatra_id'] = isset($panel['chatra_id']) ? $panel['chatra_id'] : '';
        $agent->panel = $panel;
        return $agent;
    }

    public static function getShopLevel($uid){
        $agent = self::where('uid','=',$uid)->first();
        if($agent == false) {
            $level = 1;
        }else{
            $level = AgentLevel::where('id','=',$agent->level)->value('level');
        }
        return $level;
    }

    /**
     * @Notes:
     * 初始化添加下级代理的表数据结构
     * @Interface init_agent
     * @Author: 133814250@qq.com MengShuai
     * @Date: 2020/2/25   20:22
     * @param $uid
     * @param $level
     * @return int
     */
    public static function init_agent($uid,$level){
        $AgentConfig = new AgentConfig();
        $AgentLevel = new AgentLevel();
        if($AgentConfig::where('uid','=',$uid)->first() || $AgentLevel::where('uid','=',$uid)->first()){
            return 1001;
        }

        $AgentConfig->uid = $uid;
        $AgentConfig->level = $level->id;
        $AgentConfig->panel = json_encode(['name'=>Config::get('appName')]);
        $datatables = new Datatables(new DatatablesHelper());
        $datatables->query('Select shop.id from shop');
        $shops = json_decode($datatables->generate(),true);
        $cost_shop = json_decode(self::where('uid','=', Config::get('agent')->id )->first()->shop,true);
        
        $shop =array();
        foreach ($shops['data'] as $k => $v){
            $shop += [
                'id'.$v['id'] => [
                    //默认加价比率不可低于1.1，即最低为1.11
                    'p1' => sprintf("%.2f", $cost_shop["id{$v['id']}"]["p{$level->level}"] * 1.28),
                    'p2' => sprintf("%.2f", $cost_shop["id{$v['id']}"]["p{$level->level}"] * 1.25),
                    'p3' => sprintf("%.2f", $cost_shop["id{$v['id']}"]["p{$level->level}"] * 1.20),
                    'p4' => sprintf("%.2f", $cost_shop["id{$v['id']}"]["p{$level->level}"] * 1.15),
                ],
            ];
        }

        $AgentConfig->shop = json_encode($shop);
        $AgentConfig->cash = json_encode([
            'name' => '',
            'account' => '',
            'img' => 'http://bufanyun.cn-bj.ufileos.com/uploads_ml/20200225_ee8edecdb148d579db6cb0e9904e6362.png',
        ]);
        $AgentConfig->addtime = time();
        $AgentConfig->endtime = time()+365*86400; //1年
        $AgentConfig->status = 1;

        $result1 = $AgentConfig->save();

        $result2 = $AgentLevel->insert([
            //只有价格和名称可以被修改
            ['uid' => $uid, 'level' => '1', 'name' => '客户代理', 'price' => '8.00', 'status' => 1],
            ['uid' => $uid, 'level' => '2', 'name' => '青铜代理', 'price' => '28.00', 'status' => 1],
            ['uid' => $uid, 'level' => '3', 'name' => '黄金代理', 'price' => '68.00', 'status' => 1],
            ['uid' => $uid, 'level' => '4', 'name' => '钻石代理', 'price' => '288.00', 'status' => 1],
        ]);

        if($result1 && $result2) {
            return 1;
        }else{
            //删除插入
            $AgentConfig::where('uid','=',$uid)->delete() && $AgentLevel::where('uid','=',$uid)->delete();
            return 0;
        }
    }


}
