<?php

namespace App\Controllers;

use App\Services\MalioConfig;
use App\Utils\Common;
use App\Services\Auth;
use App\Models\AgentIncome;
use App\Models\AgentConfig;
use App\Models\AgentLevel;
use App\Models\Km;
use App\Models\Node;
use App\Models\TrafficLog;
use App\Models\InviteCode;
use App\Models\Ann;
use App\Models\Speedtest;
use App\Models\Shop;
use App\Models\Coupon;
use App\Models\Bought;
use App\Models\Ticket;
use App\Services\Config;
use App\Services\Gateway\ChenPay;
use App\Services\BitPayment;
use App\Services\Payment;
use App\Utils;
use App\Utils\AliPay;
use App\Utils\Hash;
use App\Utils\Tools;
use App\Utils\Radius;
use App\Models\DetectLog;
use App\Models\DetectRule;
use App\Models\NodeOnlineLog;
use App\Models\NodeInfoLog;
use App\Models\AgentMoneyLog;
use Exception;
use Ozdemir\Datatables\Datatables;
use voku\helper\AntiXSS;

use App\Models\User;
use App\Models\Code;
use App\Models\Ip;
use App\Models\LoginIp;
use App\Models\BlockIp;
use App\Models\UnblockIp;
use App\Models\Payback;
use App\Models\Relay;
use App\Utils\QQWry;
use App\Models\UserSubscribeLog;
use App\Utils\GA;
use App\Utils\Geetest;
use App\Utils\Telegram;
use App\Utils\TelegramSessionManager;
use App\Utils\Pay;
use App\Utils\URL;
use App\Utils\DatatablesHelper;
use App\Services\Mail;

/**
 *  HomeController
 */
class UserController extends BaseController
{

    public function getmoney($request, $response, $args)
    {
        $user = $this->user;
        $res['money'] = $user->money;
        $res['ret'] = 1;
        return $this->echoJson($response, $res);
    }

    public function getPlanInfo($request, $response, $args)
    {
        $plan_time = explode('month',$request->getQueryParams()['time'])[0];
        $plan_num = explode('plan_',$request->getQueryParams()['num'])[1];
        if (empty($plan_num) || empty($plan_time)) {
            $res['ret'] = 0;
            return $this->echoJson($response, $res);
        }
        $shop = Shop::where('parentid', $plan_num)->where('month',$plan_time)->where('status',1)->first();

        //获取代理商店价格
        $shopLevel = AgentConfig::getShopLevel($this->user->id);
        $price = Config::get('agent')->shop["id{$shop->id}"]["p{$shopLevel}"];

        $res['id'] = $shop->id;
        $res['name'] = $shop->name;
        $res['price'] = $price;
        $res['ret'] = 1;
        return $this->echoJson($response, $res);
    }

    public function getPlanTime($request, $response, $args)
    {
        $plan_num = $request->getQueryParams()['num'];
        if (empty($plan_num)) {
            $res['ret'] = 0;
            return $this->echoJson($response, $res);
        }
        $plan_time = [];
        foreach ((MalioConfig::get('plan_shop_id'))[$plan_num] as $key => $value) {
            if ($value != 0) {
                $plan_time[] = $key;
            }
        }
        $res['plan_time'] = $plan_time;
        $res['ret'] = 1;
        return $this->echoJson($response, $res);
    }


    public function buyTrafficPackage($request, $response, $args)
    {
        $shopId = $request->getParam('shopid');
        $shop = Shop::where('id', $shopId)->where('status', 1)->first();
        if ($shop == null) {
            $res['ret'] = 0;
            $res['msg'] = '非法请求';
            return $response->getBody()->write(json_encode($res));
        }

        $price = $shop->price;
        $user = $this->user;

        if (!$user->isLogin) {
            $res['ret'] = -1;
            return $response->getBody()->write(json_encode($res));
        }
        if (bccomp($user->money, $price, 2) == -1) {
            $res['ret'] = 0;
            $res['msg'] = '喵喵喵~ 当前余额不足，总价为' . $price . '元。</br><a href="/user/code">点击进入充值界面</a>';
            return $response->getBody()->write(json_encode($res));
        }

        $user->money = bcsub($user->money, $price, 2);
        $traffic = $shop->bandwidth();
        $user->transfer_enable += $traffic * 1024 * 1024 * 1024;
        $user->save();

        $bought = new Bought();
        $bought->userid = $user->id;
        $bought->shopid = $shop->id;
        $bought->datetime = time();
        $bought->coupon = '';
        $bought->renew = 0;
        $bought->price = $price;
        $bought->save();

        $res['ret'] = 1;
        $res['msg'] = '购买成功';

        return $response->getBody()->write(json_encode($res));
    }

    public function share_account($request, $response, $args)
    {
        return $this->view()->display('user/share_account.tpl');
    }

    public function qrcode($request, $response, $args)
    {
        return $this->view()->display('user/qrcode.tpl');
    }


    public function Ranking($request, $response, $args){
//        if($this->user->id != 1){exit('403');}
        $nowtime = strtotime(date("Y-m-d",time()));
        $db = new DatatablesHelper();

        echo '==============================节点排行=====================';
        $query2 = "select sum(u+d) as total,node_id from `user_traffic_log` where `log_time` >={$nowtime} group by node_id order by total desc limit 0,100";
        $l2 = $db->query($query2);
        $s2='<p>';
        foreach ($l2 as $k2 => $v2){
            $node = Node::where("id", $v2['node_id'])->first();
            $i=$k2+1;
            $s2 .= "排名：<{$i}> ".$node['name']."({$node['id']})".'--'.Tools::flowAutoShow($v2['total']).'<p>';
        }
        echo $s2;

        echo '==============================用户排行=====================';
        $query = "select sum(u+d) as total,user_id from `user_traffic_log` where `log_time` >={$nowtime} group by user_id order by total desc limit 0,500";
        $l = $db->query($query);
        $s='<p>';
        foreach ($l as $k => $v){
            $node = User::where("id", $v['user_id'])->first();
            $i=$k+1;
            $s .= "排名：<{$i}> ".$node['email']."({$node['id']})".'--'.Tools::flowAutoShow($v['total']).'<p>';
        }
        echo $s;
    }


    /**
     * @Notes:
     * 卡密开通业务
     * @Interface OpenKm
     * @Author: 133814250@qq.com MengShuai
     * @Date: 2020/3/4   20:17
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function OpenKm($request, $response, $args){
        $code = $request->getParam('code');
        $code = trim($code);
        $user = $this->user;

        if ($code == "") {
            $res['ret'] = 0;
            $res['msg'] = "请输入正确的激活码。";
            return $response->getBody()->write(json_encode($res));
        }

        $codeq = Km::where("km", "=", $code)->first();
        if ($codeq == null) {
            $res['ret'] = 0;
            $res['msg'] = "激活码输入错误，或不存在。";
            return $response->getBody()->write(json_encode($res));
        }

        if ($codeq->status == '1') {
            $res['ret'] = 0;
            $res['msg'] = "此激活码已被使用。";
            return $response->getBody()->write(json_encode($res));
        }
        if ($codeq->status == '2') {
            $res['ret'] = 0;
            $res['msg'] = "此激活码已被冻结。";
            return $response->getBody()->write(json_encode($res));
        }

        $shop = Shop::where("id", $codeq->type_id)->where("status", 1)->first();
        if ($shop == null) {
            $res['ret'] = 0;
            $res['msg'] = "此激活码套餐已失效或已下架。";
            return $response->getBody()->write(json_encode($res));
        }

        //更新卡密记录
        $codeq->type_id = 1;
        $codeq->status  = 1;
        $codeq->user_id = $user->id;
        $codeq->endtime = time();
        $codeq->save();

        //更新用户套餐
        $shop->buy($user);

        $res['ret'] = 1;
        $res['msg'] = "恭喜你，套餐已成功激活。";
        return $response->getBody()->write(json_encode($res));
    }

    /**
     * @Notes:
     * 购买代理
     * @Interface agentBuy
     * @Author: 133814250@qq.com MengShuai
     * @Date: 2020/2/29   14:56
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function agentBuy($request, $response, $args)
    {
        $user = $this->user;
        $shop = $request->getParam('shop');
        $shop = AgentLevel::where('id', $shop)
            ->where('status', 1)
            ->where('uid',Config::get('agent')->uid)
            ->first();

        if ($shop == null) {
            $res['ret'] = 0;
            $res['msg'] = '非法请求';
            return $response->getBody()->write(json_encode($res));
        }

        $exist = AgentConfig::where('uid',$user->id)->first();
        if($exist == null){
            $price = $shop->price;
            $type = 'buy';
        }

        if(!empty($exist)) {
            $now = AgentLevel::where('id', $exist->level)->first();
            if ($now->level < $shop->level) {
                $price = sprintf("%.2f", ($shop->price - $now->price));
                $price = $price > 0 ? $price : 0.00;
                $type = 'up';
            } else {
                $res['ret'] = 0;
                $res['msg'] = '只能购买高于当前等级的代理。';
                return $response->getBody()->write(json_encode($res));
            }
        }

        if (bccomp($user->money, $price, 2) == -1) {
            $res['ret'] = 0;
            $res['msg'] = '喵喵喵~ 当前余额不足，总价为' . $price . '元。</br><a href="/user/code">点击进入充值界面</a>';
            return $response->getBody()->write(json_encode($res));
        }
        //用户资金变动
        AgentMoneyLog::Newly($user->id, "-" . $price,
            $user->money,(bcsub($user->money, $price, 2)),($type == 'buy') ? '购买代理：开通' : '购买代理：升级(原Lv.' . $now->level . ')');

        $user->money = bcsub($user->money, $price, 2);
        $user->is_admin = 1;
        $user->save();

        //开通/升级代理业务处理
        if($type == 'buy') {
            $result = AgentConfig::init_agent($user->id,$shop);

            if($result == 1001) {
                $res['ret'] = 0;
                $res['msg'] = '已存在代理信息，请联系管理员。';
                return $response->getBody()->write(json_encode($res));
            }elseif ($result == 0){
                $res['ret'] = 0;
                $res['msg'] = '开通失败，稍候重试。';
                return $response->getBody()->write(json_encode($res));
            }
        }elseif ($type == 'up') {
            $exist->level = $shop->level;
            $exist->save();
        }else{
            $res['ret'] = 0;
            $res['msg'] = '非法请求，未知操作';
            return $response->getBody()->write(json_encode($res));
        }

        //插入收益记录
        if($price > 0) {
            $boss = User::where('id',Config::get('agent')->uid)->first();
            $old_money = $boss->money;
            $boss->money += $price;
            $boss->save();

            $AgentIncome = new AgentIncome();
            $AgentIncome->uid = Config::get('agent')->uid;
            $AgentIncome->user_id = $user->id;
            $AgentIncome->type = 'agent';
            $AgentIncome->type_id = $shop->id;
            $AgentIncome->money = $price;
            $AgentIncome->income = $price;
            $AgentIncome->addtime = time();
            $AgentIncome->remark = ($type == 'buy') ? '购买代理：开通' : '购买代理：升级(原Lv.' . $now->level . ')';
            $AgentIncome->save();
            //代理资金变动
            AgentMoneyLog::Newly(Config::get('agent')->uid, $price,
                $old_money,($boss->money),"用户(#{$user->id}){$AgentIncome->remark}");
        }

        $res['ret'] = 1;
        $res['msg'] = '购买成功，左侧菜单栏已开启【代理面板】';
        return $response->getBody()->write(json_encode($res));
    }


    /**
     * @Notes:
     * 获取购买代理价格
     * @Interface CouponCheckAgent
     * @Author: 133814250@qq.com MengShuai
     * @Date: 2020/2/29   14:56
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function CouponCheckAgent($request, $response, $args)
    {
        $user = $this->user;
        if (!$user->isLogin) {
            $res['ret'] = -1;
            return $response->getBody()->write(json_encode($res));
        }

        $shop = $request->getParam('shop');
        $shop = AgentLevel::where('id', $shop)->where('status', 1)->where('uid',Config::get('agent')->uid)->first();
        if ($shop == null) {
            $res['ret'] = 0;
            $res['msg'] = '非法请求';
            return $response->getBody()->write(json_encode($res));
        }

        $exist = AgentConfig::where('uid',$user->id)->first();
        if($exist == null){
            $res['ret'] = 1;
            $res['name'] = $shop->name;
            $res['credit'] = $shop->price . '元';
            $res['total'] = $shop->price . '元';
            return $response->getBody()->write(json_encode($res));
        }

        $now = AgentLevel::where('id', $exist->level)->first();
        if($now->level < $shop->level) {
            $res['ret'] = 1;
            $res['name'] = $shop->name;
            $res['credit'] = (($shop->price - $now->price) > 0) ? sprintf("%.2f", ($shop->price - $now->price)) : 0.00;
            $res['total'] = "{$shop->price}-{$now->price}={$res['credit']}";
            return $response->getBody()->write(json_encode($res));
        }elseif ($now->level == $shop->level) {
            $res['ret'] = 0;
            $res['msg'] = '你已经是' . $now->name . '啦~';
            return $response->getBody()->write(json_encode($res));
        }else{
            $res['ret'] = 0;
            $res['msg'] = '你已经是' . $now->name . '啦，无法购买低于这个Lv的代理哦~';
            return $response->getBody()->write(json_encode($res));
        }

    }

    /**
     * @Notes:
     * 购买代理等级列表
     * @Interface agent
     * @Author: 133814250@qq.com MengShuai
     * @Date: 2020/2/29   14:56
     * @return mixed
     */
    public function agent(){
        //标签
        $agentTag = [
            '<span class="card-tag tag-cyan"># <b>体验</b></span>',
            '<span class="card-tag tag-green"># <b>新手</b></span>',
            '<span class="card-tag tag-red"># <b>推荐</b></span>',
            '<span class="card-tag tag-blue"># <b>大神</b></span>'
        ];


        $levels = AgentLevel::where('status', 1)
            ->where('uid',Config::get('agent')->uid)
            ->orderBy('level')
            ->get();
        //将商店价格重置为代理设置价格
        foreach ($levels as $k => $v){
            $levels[$k]->tag = $agentTag[$k];
            $levels[$k]->shop = self::agent_shop($v['level']);
        }
//        var_export($levels);exit;
        return $this->view()
            ->assign('levels', $levels)
            ->display('user/agent.tpl');
    }


    public function agent_shop($level){
//        $shops = Shop::where('status', 1)->get();
        $datatables = new Datatables(new DatatablesHelper());
        $datatables->query('SELECT id,name,content FROM `shop` where `parentid`=`id` and status=1 ORDER BY `shop`.`id` ASC');
        $shops = json_decode($datatables->generate(),true)['data'];

        $price = Config::get('agent')->shop;
        $data = '';
        foreach ($shops as $k => $v){
            $name = $v['name'];

            $data .= "<div class=\"pricing-item\">
           <div class=\"pricing-item-icon\" style=\"background-color: #03A9F4;\"><i class=\"fas fa-code\"></i></div>
           <div class=\"pricing-item-label\" style='font-size: 13px;'> {$name} ￥ {$price["id{$v['id']}"]["p{$level}"]}</div>
           </div>";
//            $data .= "<div class=\"shop-content-lefts\">{$name}</div>
//                      <div class=\"shop-content-rights\"><small>￥</small> {$price["id{$v['id']}"]["p{$level}"]}</div>";
        }

        return $data;
    }



    public function index($request, $response, $args)
    {
        $ssr_sub_token = LinkController::GenerateSSRSubCode($this->user->id, 0);

        $GtSdk = null;
        $recaptcha_sitekey = null;
        if (Config::get('enable_checkin_captcha') == true) {
            switch (Config::get('captcha_provider')) {
                case 'recaptcha':
                    $recaptcha_sitekey = Config::get('recaptcha_sitekey');
                    break;
                case 'geetest':
                    $uid = time() . random_int(1, 10000);
                    $GtSdk = Geetest::get($uid);
                    break;
            }
        }

        $Ann = Ann::orderBy('date', 'desc')->first();

        if (!$paybacks_sum = Payback::where("ref_by", $this->user->id)->sum('ref_get')) {
            $paybacks_sum = 0;
        }

        $class_left_days = floor((strtotime($this->user->class_expire)-time())/86400)+1;

        if ($_ENV['subscribe_client_url'] != '') {
            $getClient = new Token();
            for ($i = 0; $i < 10; $i++) {
                $token = $this->user->id . Tools::genRandomChar(16);
                $Elink = Token::where('token', '=', $token)->first();
                if ($Elink == null) {
                    $getClient->token = $token;
                    break;
                }
            }
            $getClient->user_id     = $this->user->id;
            $getClient->create_time = time();
            $getClient->expire_time = time() + 10 * 60;
            $getClient->save();
        } else {
            $token = '';
        }

        return $this->view()
            ->assign('class_left_days', $class_left_days)
            ->assign('paybacks_sum', $paybacks_sum)
            ->assign('subInfo', LinkController::getSubinfo($this->user, 0))
            ->assign('ssr_sub_token', $ssr_sub_token)
            ->assign('getClient', $token)
            ->assign('display_ios_class', Config::get('display_ios_class'))
            ->assign('display_ios_topup', Config::get('display_ios_topup'))
            ->assign('ios_account', Config::get('ios_account'))
            ->assign('ios_password', Config::get('ios_password'))
            ->assign('ann', $Ann)
            ->assign('geetest_html', $GtSdk)
            ->assign('mergeSub', Config::get('mergeSub'))
            ->assign('subUrl', Config::get('subUrl'))
            ->assign('user', $this->user)
            ->registerClass('URL', URL::class)
            ->assign('baseUrl', Config::get('baseUrl'))
            ->assign('recaptcha_sitekey', $recaptcha_sitekey)
            ->display('user/index.tpl');
    }


    public function lookingglass($request, $response, $args)
    {
        $Speedtest = Speedtest::where('datetime', '>', time() - Config::get('Speedtest_duration') * 3600)->orderBy('datetime', 'desc')->get();

        return $this->view()->assign('speedtest', $Speedtest)->assign('hour', Config::get('Speedtest_duration'))->display('user/lookingglass.tpl');
    }

    public function code($request, $response, $args)
    {

        $pageNum = $request->getQueryParams()['page'] ?? 1;
        $codes = Code::where('type', '<>', '-2')
            ->where('userid', '=', $this->user->id)
            ->orderBy('id', 'desc')
            ->paginate(15, ['*'], 'page', $pageNum);
//        $codes->setPath('/user/code');


        $codes2 = Km::where('status', '=', '1')
            ->where('user_id', '=', $this->user->id)
            ->orderBy('id', 'desc')
            ->paginate(15, ['*'], 'page', $pageNum);

        foreach ($codes2 as $k => $v){
            $codes2[$k]['endtime'] = date("Y-m-d H:i:s",$v['endtime']);
            $codes2[$k]['type'] = explode('（',Shop::where(['id' => $v['type_id']])->value('name'))[0];
        }

//        var_export(count($codes));exit;
        return $this->view()
            ->assign('codes', $codes)
            ->assign('codes2', $codes2)
            ->assign('shops', $codes2)

            ->assign('pmw', Payment::purchaseHTML())
            ->assign('bitpay', BitPayment::purchaseHTML())
            ->display('user/code.tpl');
    }

    public function orderDelete($request, $response, $args)
    {
        return (new ChenPay())->orderDelete($request);
    }

    public function donate($request, $response, $args)
    {
        if (Config::get('enable_donate') != 'true') {
            exit(0);
        }

        $pageNum = $request->getQueryParams()['page'] ?? 1;
        $codes = Code::where(
            static function ($query) {
                $query->where('type', '=', -1)
                    ->orWhere('type', '=', -2);
            }
        )->where('isused', 1)->orderBy('id', 'desc')->paginate(15, ['*'], 'page', $pageNum);
        $codes->setPath('/user/donate');
        return $this->view()->assign('codes', $codes)->assign('total_in', Code::where('isused', 1)->where('type', -1)->sum('number'))->assign('total_out', Code::where('isused', 1)->where('type', -2)->sum('number'))->display('user/donate.tpl');
    }

    public function isHTTPS()
    {
        define('HTTPS', false);
        if (defined('HTTPS') && HTTPS) {
            return true;
        }
        if (!isset($_SERVER)) {
            return false;
        }
        if (!isset($_SERVER['HTTPS'])) {
            return false;
        }
        if ($_SERVER['HTTPS'] === 1) {  //Apache
            return true;
        }

        if ($_SERVER['HTTPS'] === 'on') { //IIS
            return true;
        }

        if ($_SERVER['SERVER_PORT'] == 443) { //其他
            return true;
        }
        return false;
    }

    public function subscribe_log($request, $response, $args)
    {
        $pageNum = $request->getQueryParams()['page'] ?? 1;
        $logs = UserSubscribeLog::orderBy('id', 'desc')->where('user_id', $this->user->id)->paginate(15, ['*'], 'page', $pageNum);
        $logs->setPath('/user/subscribe_log');

        $iplocation = new QQWry();

        return $this->view()->assign('logs', $logs)->assign('iplocation', $iplocation)->display('user/subscribe_log.tpl');
    }

    public function code_check($request, $response, $args)
    {
        $time = $request->getQueryParams()['time'];
        $codes = Code::where('userid', '=', $this->user->id)->where('usedatetime', '>', date('Y-m-d H:i:s', $time))->first();
        if ($codes != null && strpos($codes->code, '充值') !== false) {
            $res['ret'] = 1;
            return $response->getBody()->write(json_encode($res));
        }

        $res['ret'] = 0;
        return $response->getBody()->write(json_encode($res));
    }

    public function f2fpayget($request, $response, $args)
    {
        $time = $request->getQueryParams()['time'];
        $res['ret'] = 1;
        return $response->getBody()->write(json_encode($res));
    }

    public function f2fpay($request, $response, $args)
    {
        $amount = $request->getParam('amount');
        if ($amount == '') {
            $res['ret'] = 0;
            $res['msg'] = '订单金额错误：' . $amount;
            return $response->getBody()->write(json_encode($res));
        }
        $user = $this->user;

        //生成二维码
        $qrPayResult = Pay::alipay_get_qrcode($user, $amount, $qrPay);
        //  根据状态值进行业务处理
        switch ($qrPayResult->getTradeStatus()) {
            case 'SUCCESS':
                $aliresponse = $qrPayResult->getResponse();
                $res['ret'] = 1;
                $res['msg'] = '二维码生成成功';
                $res['amount'] = $amount;
                $res['qrcode'] = $qrPay->create_erweima($aliresponse->qr_code);

                break;
            case 'FAILED':
                $res['ret'] = 0;
                $res['msg'] = '支付宝创建订单二维码失败! 请使用其他方式付款。';

                break;
            case 'UNKNOWN':
                $res['ret'] = 0;
                $res['msg'] = '系统异常，状态未知! 请使用其他方式付款。';

                break;
            default:
                $res['ret'] = 0;
                $res['msg'] = '创建订单二维码返回异常! 请使用其他方式付款。';

                break;
        }

        return $response->getBody()->write(json_encode($res));
    }

    public function alipay($request, $response, $args)
    {
        $amount = $request->getQueryParams()['amount'];
        Pay::getGen($this->user, $amount);
    }


    public function codepost($request, $response, $args)
    {
        $code = $request->getParam('code');
        $code = trim($code);
        $user = $this->user;

        if ($code == '') {
            $res['ret'] = 0;
            $res['msg'] = '非法输入';
            return $response->getBody()->write(json_encode($res));
        }

        $codeq = Code::where('code', '=', $code)->where('isused', '=', 0)->first();
        if ($codeq == null) {
            $res['ret'] = 0;
            $res['msg'] = '此充值码错误';
            return $response->getBody()->write(json_encode($res));
        }

        $codeq->isused = 1;
        $codeq->usedatetime = date('Y-m-d H:i:s');
        $codeq->userid = $user->id;
        $codeq->save();

        if ($codeq->type == -1) {
            $user->money += $codeq->number;
            $user->save();

            if ($user->ref_by != '' && $user->ref_by != 0 && $user->ref_by != null) {
                $gift_user = User::where('id', '=', $user->ref_by)->first();
                $gift_user->money += ($codeq->number * (Config::get('code_payback') / 100));
                $gift_user->save();

                $Payback = new Payback();
                $Payback->total = $codeq->number;
                $Payback->userid = $this->user->id;
                $Payback->ref_by = $this->user->ref_by;
                $Payback->ref_get = $codeq->number * (Config::get('code_payback') / 100);
                $Payback->datetime = time();
                $Payback->save();
            }

            $res['ret'] = 1;
            $res['msg'] = '充值成功，充值的金额为' . $codeq->number . '元。';

//            if (Config::get('enable_donate') == true) {
//                if ($this->user->is_hide == 1) {
//                    Telegram::Send('姐姐姐姐，一位不愿透露姓名的大老爷给我们捐了 ' . $codeq->number . ' 元呢~');
//                } else {
//                    Telegram::Send('姐姐姐姐，' . $this->user->user_name . ' 大老爷给我们捐了 ' . $codeq->number . ' 元呢~');
//                }
//            }

            return $response->getBody()->write(json_encode($res));
        }

        if ($codeq->type == 10001) {
            $user->transfer_enable += $codeq->number * 1024 * 1024 * 1024;
            $user->save();
        }

        if ($codeq->type == 10002) {
            if (time() > strtotime($user->expire_in)) {
                $user->expire_in = date('Y-m-d H:i:s', time() + $codeq->number * 86400);
            } else {
                $user->expire_in = date('Y-m-d H:i:s', strtotime($user->expire_in) + $codeq->number * 86400);
            }
            $user->save();
        }

        if ($codeq->type >= 1 && $codeq->type <= 10000) {
            if ($user->class == 0 || $user->class != $codeq->type) {
                $user->class_expire = date('Y-m-d H:i:s', time());
                $user->save();
            }
            $user->class_expire = date('Y-m-d H:i:s', strtotime($user->class_expire) + $codeq->number * 86400);
            $user->class = $codeq->type;
            $user->save();
        }
    }



    public function GaCheck($request, $response, $args)
    {
        $code = $request->getParam('code');
        $user = $this->user;


        if ($code == '') {
            $res['ret'] = 0;
            $res['msg'] = '二维码不能为空';
            return $response->getBody()->write(json_encode($res));
        }

        $ga = new GA();
        $rcode = $ga->verifyCode($user->ga_token, $code);
        if (!$rcode) {
            $res['ret'] = 0;
            $res['msg'] = '测试错误';
            return $response->getBody()->write(json_encode($res));
        }


        $res['ret'] = 1;
        $res['msg'] = '测试成功';
        return $response->getBody()->write(json_encode($res));
    }


    public function GaSet($request, $response, $args)
    {
        $enable = $request->getParam('enable');
        $user = $this->user;

        if ($enable == '') {
            $res['ret'] = 0;
            $res['msg'] = '选项无效';
            return $response->getBody()->write(json_encode($res));
        }

        $user->ga_enable = $enable;
        $user->save();


        $res['ret'] = 1;
        $res['msg'] = '设置成功';
        return $response->getBody()->write(json_encode($res));
    }

    public function ResetPort($request, $response, $args)
    {
        $price = Config::get('port_price');
        $user = $this->user;

        if ($user->money < $price) {
            $res['ret'] = 0;
            $res['msg'] = '余额不足';
            return $response->getBody()->write(json_encode($res));
        }

        $origin_port = $user->port;

        $user->port = Tools::getAvPort();


        $relay_rules = Relay::where('user_id', $user->id)->where('port', $origin_port)->get();
        foreach ($relay_rules as $rule) {
            $rule->port = $user->port;
            $rule->save();
        }

        $user->money -= $price;
        $user->save();

        $res['ret'] = 1;
        $res['msg'] = $user->port;
        return $response->getBody()->write(json_encode($res));
    }

    public function SpecifyPort($request, $response, $args)
    {
        $price = Config::get('port_price_specify');
        $user = $this->user;

        if ($user->money < $price) {
            $res['ret'] = 0;
            $res['msg'] = '余额不足';
            return $response->getBody()->write(json_encode($res));
        }

        $port = $request->getParam('port');

        if ($port < Config::get('min_port') || $port > Config::get('max_port') || Tools::isInt($port) == false) {
            $res['ret'] = 0;
            $res['msg'] = '端口不在要求范围内';
            return $response->getBody()->write(json_encode($res));
        }

        $port_occupied = User::pluck('port')->toArray();

        if (in_array($port, $port_occupied) == true) {
            $res['ret'] = 0;
            $res['msg'] = '端口已被占用';
            return $response->getBody()->write(json_encode($res));
        }

        $origin_port = $user->port;

        $user->port = $port;


        $relay_rules = Relay::where('user_id', $user->id)->where('port', $origin_port)->get();
        foreach ($relay_rules as $rule) {
            $rule->port = $user->port;
            $rule->save();
        }

        $user->money -= $price;
        $user->save();

        $res['ret'] = 1;
        $res['msg'] = '钦定成功';
        return $response->getBody()->write(json_encode($res));
    }

    public function GaReset($request, $response, $args)
    {
        $user = $this->user;
        $ga = new GA();
        $secret = $ga->createSecret();

        $user->ga_token = $secret;
        $user->save();
        return $response->withStatus(302)->withHeader('Location', '/user/edit');
    }


    public function nodeAjax($request, $response, $args)
    {
        $id = $args['id'];
        $point_node = Node::find($id);
        $prefix = explode(' - ', $point_node->name);
        return $this->view()->assign('point_node', $point_node)->assign('prefix', $prefix[0])->assign('id', $id)->display('user/nodeajax.tpl');
    }

    public function node($request, $response, $args)
    {
        $user = Auth::getUser();
        $nodes = Node::where('type', 1)->orderBy('node_class')->orderBy('id')->get();
        $relay_rules = Relay::where('user_id', $this->user->id)->orwhere('user_id', 0)->orderBy('id', 'asc')->get();
        if (!Tools::is_protocol_relay($user)) {
            $relay_rules = array();
        }

        $array_nodes = array();
        $nodes_muport = array();
        $db = new DatatablesHelper();
        $infoLogs = $db->query('SELECT * FROM ( SELECT * FROM `ss_node_info` WHERE log_time > ' . (time() - 300) . ' ORDER BY id DESC LIMIT 999999999999 ) t GROUP BY node_id ORDER BY id DESC');
        $onlineLogs = $db->query('SELECT * FROM ( SELECT * FROM `ss_node_online_log` WHERE log_time > ' . (time() - 300) . ' ORDER BY id DESC LIMIT 999999999999 ) t GROUP BY node_id ORDER BY id DESC');

        foreach ($nodes as $node) {
            if ($user->is_admin == 0 && $node->node_group != $user->node_group && $node->node_group != 0) {
                continue;
            }
            if ($node->sort == 9) {
                $mu_user = User::where('port', '=', $node->server)->first();
                $mu_user->obfs_param = $this->user->getMuMd5();
                $nodes_muport[] = array('server' => $node, 'user' => $mu_user);
                continue;
            }
            $array_node = array();


            $array_node['id'] = $node->id;
            $array_node['class'] = $node->node_class;
            $array_node['node_ip'] = $node->node_ip;
            $array_node['name'] = $node->name;
            if ($node->sort == 13) {
                $server = Tools::ssv2Array($node->server);
                $array_node['server'] = $server['add'];
            } else {
                $array_node['server'] = $node->server;
            }
            $array_node['sort'] = $node->sort;
            $array_node['info'] = $node->info;
            $array_node['mu_only'] = $node->mu_only;
            $array_node['group'] = $node->node_group;

            $array_node['raw_node'] = $node;
            $regex = Config::get('flag_regex');
            $matches = array();
            preg_match($regex, $node->name, $matches);
            if (isset($matches[0])) {
                $array_node['flag'] = $matches[0] . '.png';
            } else {
                $array_node['flag'] = 'unknown.png';
            }

            $sort = $array_node['sort'];
            $array_node['online_user'] = 0;
            
            foreach ($onlineLogs as $log) {
                if ($log['node_id'] != $node->id) {
                    continue;
                }
                if (in_array($sort, array(0, 7, 8, 10, 11, 12, 13))) {
                    $array_node['online_user'] = $log['online_user']+date("m")+rand(0,30);
                } else {
                    $array_node['online_user'] = -1;
                }
                break;
            }
            
            // check node status 
            // 0: new node; -1: offline; 1: online
            $node_heartbeat = $node->node_heartbeat + 300;
            $array_node['online'] = -1;
            if (!in_array($sort, array(0, 7, 8, 10, 11, 12, 13)) || $node_heartbeat == 300 ) {
                $array_node['online'] = 0;
            } elseif ($node_heartbeat > time()) {
                $array_node['online'] = 1;
            }

            $array_node['latest_load'] = -1;
            foreach ($infoLogs as $log) {
                if ($log['node_id'] == $node->id) {
                    $array_node['latest_load'] = (explode(' ', $log['load']))[0] * 100;
                    break;
                }
            }

            $array_node['traffic_used'] = (int)Tools::flowToGB($node->node_bandwidth);
            $array_node['traffic_limit'] = (int)Tools::flowToGB($node->node_bandwidth_limit);
            if ($node->node_speedlimit == 0.0) {
                $array_node['bandwidth'] = 0;
            } elseif ($node->node_speedlimit >= 1024.00) {
                $array_node['bandwidth'] = round($node->node_speedlimit / 1024.00, 1) . 'Gbps';
            } else {
                $array_node['bandwidth'] = $node->node_speedlimit . 'Mbps';
            }

            $array_node['traffic_rate'] = $node->traffic_rate;
            $array_node['status'] = $node->status;

            /**
             * 伪造节点显示数据
             */
            if(in_array($array_node['id'],Config::get('copy_node_id'))){
                $array_node['online'] = 1;
                $array_node['online_user'] = $log['online_user']+date("m")+rand(0,10);
            }

            $array_nodes[] = $array_node;
        }

//        var_export($array_nodes);exit;
        return $this->view()
            ->assign('nodes', $array_nodes)
            ->assign('nodes_muport', $nodes_muport)
            ->assign('relay_rules', $relay_rules)
            ->assign('tools', new Tools())
            ->assign('user', $user)
            ->registerClass('URL', URL::class)
            ->display('user/node.tpl');
    }


    public function node_old($request, $response, $args)
    {
        $user = Auth::getUser();
        $nodes = Node::where('type', 1)->orderBy('name')->get();
        $relay_rules = Relay::where('user_id', $this->user->id)->orwhere('user_id', 0)->orderBy('id', 'asc')->get();

        if (!Tools::is_protocol_relay($user)) {
            $relay_rules = array();
        }

        $node_prefix = array();
        $node_flag_file = array();
        $node_method = array();
        $a = 0;//命名的什么JB变量
        $node_order = array();
        $node_alive = array();
        $node_prealive = array();
        $node_heartbeat = array();
        $node_bandwidth = array();
        $node_muport = array();
        $node_isv6 = array();
        $node_class = array();
        $node_latestload = array();

        $ports_count = Node::where('type', 1)->where('sort', 9)->orderBy('name')->count();


        ++$ports_count;

        foreach ($nodes as $node) {
            if (($user->node_group == $node->node_group || $node->node_group == 0 || $user->is_admin) && (!$node->isNodeTrafficOut())) {
                if ($node->sort == 9) {
                    $mu_user = User::where('port', '=', $node->server)->first();
                    $mu_user->obfs_param = $this->user->getMuMd5();
                    $node_muport[] = array('server' => $node, 'user' => $mu_user);
                    continue;
                }

                $temp = explode(' - ', $node->name);
                $name_cheif = $temp[0];

                $node_isv6[$name_cheif] = $node->isv6;
                $node_class[$name_cheif] = $node->node_class;

                if (!isset($node_prefix[$name_cheif])) {
                    $node_prefix[$name_cheif] = array();
                    $node_order[$name_cheif] = $a;
                    $node_alive[$name_cheif] = 0;

                    $node_method[$name_cheif] = $temp[1] ?? '';

                    $a++;
                }


                if (in_array($node->sort, array(0, 7, 8, 10, 11, 12, 13))) {
                    $node_tempalive = $node->getOnlineUserCount();
                    $node_prealive[$node->id] = $node_tempalive;
                    if ($node->isNodeOnline() !== null) {
                        if ($node->isNodeOnline() === false) {
                            $node_heartbeat[$name_cheif] = '离线';
                        } else {
                            $node_heartbeat[$name_cheif] = '在线';
                        }
                    } elseif (!isset($node_heartbeat[$name_cheif])) {
                        $node_heartbeat[$name_cheif] = '暂无数据';
                    }

                    if ($node->node_bandwidth_limit == 0) {
                        $node_bandwidth[$name_cheif] = (int)($node->node_bandwidth / 1024 / 1024 / 1024) . ' GB 已用';
                    } else {
                        $node_bandwidth[$name_cheif] = (int)($node->node_bandwidth / 1024 / 1024 / 1024) . ' GB / ' . (int)($node->node_bandwidth_limit / 1024 / 1024 / 1024) . ' GB - ' . $node->bandwidthlimit_resetday . ' 日重置';
                    }

                    if ($node_tempalive != '暂无数据') {
                        $node_alive[$name_cheif] += $node_tempalive;
                    }
                } else {
                    $node_prealive[$node->id] = '暂无数据';
                    if (!isset($node_heartbeat[$temp[0]])) {
                        $node_heartbeat[$name_cheif] = '暂无数据';
                    }
                }

                if (isset($temp[1]) && strpos($node_method[$name_cheif], $temp[1]) === false) {
                    $node_method[$name_cheif] = $node_method[$name_cheif] . ' ' . $temp[1];
                }

                $nodeLoad = $node->getNodeLoad();
                if (isset($nodeLoad[0]['load'])) {
                    $node_latestload[$name_cheif] = ((float)(explode(' ', $nodeLoad[0]['load']))[0]) * 100;
                } else {
                    $node_latestload[$name_cheif] = null;
                }

                $node_prefix[$name_cheif][] = $node;

                if (Config::get('enable_flag') == 'true') {
                    $regex = Config::get('flag_regex');
                    $matches = array();
                    preg_match($regex, $name_cheif, $matches);
                    $node_flag_file[$name_cheif] = $matches[0] ?? 'null';
                }
            }
        }
        $node_prefix = (object)$node_prefix;
        $node_order = (object)$node_order;
        $tools = new Tools();
        return $this->view()->assign('relay_rules', $relay_rules)->assign('node_class', $node_class)->assign('node_isv6', $node_isv6)->assign('tools', $tools)->assign('node_method', $node_method)->assign('node_muport', $node_muport)->assign('node_bandwidth', $node_bandwidth)->assign('node_heartbeat', $node_heartbeat)->assign('node_prefix', $node_prefix)->assign('node_flag_file', $node_flag_file)->assign('node_prealive', $node_prealive)->assign('node_order', $node_order)->assign('user', $user)->assign('node_alive', $node_alive)->assign('node_latestload', $node_latestload)->registerClass('URL', URL::class)->display('user/node.tpl');
    }


    public function nodeInfo($request, $response, $args)
    {
        $user = Auth::getUser();
        $id = $args['id'];
        $mu = $request->getQueryParams()['ismu'];
        $relay_rule_id = $request->getQueryParams()['relay_rule'];
        $node = Node::find($id);

        if ($node == null) {
            return null;
        }


        switch ($node->sort) {
            case 0:
                if ((($user->class >= $node->node_class && ($user->node_group == $node->node_group || $node->node_group == 0)) || $user->is_admin) && ($node->node_bandwidth_limit == 0 || $node->node_bandwidth < $node->node_bandwidth_limit)) {
                    return $this->view()->assign('node', $node)->assign('user', $user)->assign('mu', $mu)->assign('relay_rule_id', $relay_rule_id)->registerClass('URL', URL::class)->display('user/nodeinfo.tpl');
                }
                break;
            case 1:
                if ($user->class >= $node->node_class && ($user->node_group == $node->node_group || $node->node_group == 0)) {
                    $email = $this->user->email;
                    $email = Radius::GetUserName($email);
                    $json_show = 'VPN 信息<br>地址：' . $node->server . '<br>' . '用户名：' . $email . '<br>密码：' . $this->user->passwd . '<br>支持方式：' . $node->method . '<br>备注：' . $node->info;

                    return $this->view()->assign('json_show', $json_show)->display('user/nodeinfovpn.tpl');
                }
                break;
            case 2:
                if ($user->class >= $node->node_class && ($user->node_group == $node->node_group || $node->node_group == 0)) {
                    $email = $this->user->email;
                    $email = Radius::GetUserName($email);
                    $json_show = 'SSH 信息<br>地址：' . $node->server . '<br>' . '用户名：' . $email . '<br>密码：' . $this->user->passwd . '<br>支持方式：' . $node->method . '<br>备注：' . $node->info;

                    return $this->view()->assign('json_show', $json_show)->display('user/nodeinfossh.tpl');
                }
                break;
            case 5:
                if ($user->class >= $node->node_class && ($user->node_group == $node->node_group || $node->node_group == 0)) {
                    $email = $this->user->email;
                    $email = Radius::GetUserName($email);

                    $json_show = 'Anyconnect 信息<br>地址：' . $node->server . '<br>' . '用户名：' . $email . '<br>密码：' . $this->user->passwd . '<br>支持方式：' . $node->method . '<br>备注：' . $node->info;

                    return $this->view()->assign('json_show', $json_show)->display('user/nodeinfoanyconnect.tpl');
                }
                break;
            case 10:
                if ((($user->class >= $node->node_class && ($user->node_group == $node->node_group || $node->node_group == 0)) || $user->is_admin) && ($node->node_bandwidth_limit == 0 || $node->node_bandwidth < $node->node_bandwidth_limit)) {
                    return $this->view()->assign('node', $node)->assign('user', $user)->assign('mu', $mu)->assign('relay_rule_id', $relay_rule_id)->registerClass('URL', URL::class)->display('user/nodeinfo.tpl');
                }
                break;
            case 13:
                if ((($user->class >= $node->node_class && ($user->node_group == $node->node_group || $node->node_group == 0)) || $user->is_admin) && ($node->node_bandwidth_limit == 0 || $node->node_bandwidth < $node->node_bandwidth_limit)) {
                    return $this->view()->assign('node', $node)->assign('user', $user)->assign('mu', $mu)->assign('relay_rule_id', $relay_rule_id)->registerClass('URL', URL::class)->display('user/nodeinfo.tpl');
                }
                break;
            default:
                echo '微笑';
        }
    }

    public function profile($request, $response, $args)
    {
        $pageNum = $request->getQueryParams()['page'] ?? 1;
        $paybacks = Payback::where('ref_by', $this->user->id)->orderBy('datetime', 'desc')->paginate(15, ['*'], 'page', $pageNum);
        $paybacks->setPath('/user/profile');

        $iplocation = new QQWry();

        $userip = array();

        $total = Ip::where('datetime', '>=', time() - 300)->where('userid', '=', $this->user->id)->get();

        $totallogin = LoginIp::where('userid', '=', $this->user->id)->where('type', '=', 0)->orderBy('datetime', 'desc')->take(10)->get();

        $userloginip = array();

        foreach ($totallogin as $single) {
            //if(isset($useripcount[$single->userid]))
            {
            if (!isset($userloginip[$single->ip])) {
                //$useripcount[$single->userid]=$useripcount[$single->userid]+1;
                $location = $iplocation->getlocation($single->ip);
                $userloginip[$single->ip] = iconv('gbk', 'utf-8//IGNORE', $location['country'] . $location['area']);
            }
            }
        }

        foreach ($total as $single) {
            //if(isset($useripcount[$single->userid]))
            {
                $single->ip = Tools::getRealIp($single->ip);
                $is_node = Node::where('node_ip', $single->ip)->first();
            if ($is_node) {
                continue;
            }


            if (!isset($userip[$single->ip])) {
                //$useripcount[$single->userid]=$useripcount[$single->userid]+1;
                $location = $iplocation->getlocation($single->ip);
                $userip[$single->ip] = iconv('gbk', 'utf-8//IGNORE', $location['country'] . $location['area']);
            }
            }
        }


        return $this->view()->assign('userip', $userip)->assign('userloginip', $userloginip)->assign('paybacks', $paybacks)->display('user/profile.tpl');
    }


    public function announcement($request, $response, $args)
    {
        $Anns = Ann::orderBy('date', 'desc')->get();


        return $this->view()->assign('anns', $Anns)->display('user/announcement.tpl');
    }

    public function tutorial($request, $response, $args)
    {
        $ssr_sub_token = LinkController::GenerateSSRSubCode($this->user->id, 0);
        $opts = $request->getQueryParams();
        if ($opts['os'] == 'faq') {
            return $this->view()->display('user/tutorial/faq.tpl');
        }
        if ($opts['os'] != '' && $opts['client'] != '') {
            $url = 'user/tutorial/'.$opts['os'].'-'.$opts['client'].'.tpl';
            return $this->view()
                ->assign('subInfo', LinkController::getSubinfo($this->user, 0))
                ->assign('ssr_sub_token', $ssr_sub_token)
                ->assign('mergeSub', Config::get('mergeSub'))
                ->assign('subUrl', Config::get('subUrl'))
                ->assign('user', $this->user)
                ->registerClass('URL', URL::class)
                ->assign('baseUrl', Config::get('baseUrl'))
                ->display($url);
        } else {
            return $this->view()->display('user/tutorial.tpl');
        }
    }

    public function edit($request, $response, $args)
    {
        $themes = Tools::getDir(BASE_PATH . '/resources/views');

        $BIP = BlockIp::where('ip', $_SERVER['REMOTE_ADDR'])->first();
        if ($BIP == null) {
            $Block = 'IP: ' . $_SERVER['REMOTE_ADDR'] . ' 没有被封';
            $isBlock = 0;
        } else {
            $Block = 'IP: ' . $_SERVER['REMOTE_ADDR'] . ' 已被封';
            $isBlock = 1;
        }

        $bind_token = TelegramSessionManager::add_bind_session($this->user);

        $config_service = new Config();

        $ssr_sub_token = LinkController::GenerateSSRSubCode($this->user->id, 0);

        return $this->view()
            ->assign('ssr_sub_token', $ssr_sub_token)
            ->assign('user', $this->user)
            ->assign('schemes', Config::get('user_agreement_scheme'))
            ->assign('themes', $themes)
            ->assign('isBlock', $isBlock)
            ->assign('Block', $Block)
            ->assign('bind_token', $bind_token)
            ->assign('telegram_bot', Config::get('telegram_bot'))
            ->assign('config_service', $config_service)
            ->registerClass('URL', URL::class)
            ->display('user/edit.tpl');
    }



    public function invite($request, $response, $args)
    {
        $code = InviteCode::where('user_id', $this->user->id)->first();
        if ($code == null) {
            $this->user->addInviteCode();
            $code = InviteCode::where('user_id', $this->user->id)->first();
        }

        $pageNum = $request->getQueryParams()['page'] ?? 1;
        $paybacks = Payback::where('ref_by', $this->user->id)
            ->orderBy('id', 'desc')
            ->paginate(15, ['*'], 'page', $pageNum);
//        if (!$paybacks_sum = Payback::where('ref_by', $this->user->id)->sum('ref_get')) {
        if (!$paybacks_sum = AgentIncome::where('uid', $this->user->id)->sum('income')) {
            $paybacks_sum = 0;
        }
        $paybacks->setPath('/user/invite');

        $other_invite_gift = Config::get('other_invite_gift');

        $agent = AgentConfig::where('uid',$this->user->id)->first();
        $agent_off = 0;
        $agent_domian = '';
        if($agent != null) {
            $agent_off = 1;
            $agent_domian = 'http://' . explode(',',$agent->domain)[0];
            if (Common::is_domain($agent_domian) == false) {
                $agent_domian = '未绑定独立域名';
            }
        }


        return $this->view()
            ->assign('code', $code)
            ->assign('paybacks', $paybacks)
            ->assign('agent_domian', $agent_domian)
            ->assign('agent_off', $agent_off)
            ->assign('other_invite_gift',$other_invite_gift)
            ->assign('paybacks_sum', $paybacks_sum)
            ->display('user/invite.tpl');
    }

    public function buyInvite($request, $response, $args)
    {
        $price = Config::get('invite_price');
        $num = $request->getParam('num');
        $num = trim($num);

        if (!Tools::isInt($num) || $price < 0 || $num <= 0) {
            $res['ret'] = 0;
            $res['msg'] = '非法请求';
            return $response->getBody()->write(json_encode($res));
        }

        $amount = $price * $num;

        $user = $this->user;

        if (!$user->isLogin) {
            $res['ret'] = -1;
            return $response->getBody()->write(json_encode($res));
        }

        if ($user->money < $amount) {
            $res['ret'] = 0;
            $res['msg'] = '余额不足，总价为' . $amount . '元。';
            return $response->getBody()->write(json_encode($res));
        }
        $user->invite_num += $num;
        $user->money -= $amount;
        $user->save();
        $res['invite_num'] = $user->invite_num;
        $res['ret'] = 1;
        $res['msg'] = '邀请次数添加成功';
        return $response->getBody()->write(json_encode($res));
    }

    public function customInvite($request, $response, $args)
    {
        $price = Config::get('custom_invite_price');
        $customcode = $request->getParam('customcode');
        $customcode = trim($customcode);

        if (!Tools::is_validate($customcode) || $price < 0 || $customcode == '' || strlen($customcode) > 32) {
            $res['ret'] = 0;
            $res['msg'] = '非法请求,邀请链接后缀不能包含特殊符号且长度不能大于32字符';
            return $response->getBody()->write(json_encode($res));
        }

        if ($customcode == 'vip888'){
            $res['ret'] = 0;
            $res['msg'] = '此后缀名为演示，无法抢注。';
            return $response->getBody()->write(json_encode($res));
        }

        if (InviteCode::where('code', $customcode)->count() != 0) {
            $res['ret'] = 0;
            $res['msg'] = '此后缀名被抢注了';
            return $response->getBody()->write(json_encode($res));
        }

        $user = $this->user;

        if (!$user->isLogin) {
            $res['ret'] = -1;
            return $response->getBody()->write(json_encode($res));
        }

        if ($user->money < $price) {
            $res['ret'] = 0;
            $res['msg'] = '余额不足，总价为' . $price . '元。';
            return $response->getBody()->write(json_encode($res));
        }
        //资金变动
        AgentMoneyLog::Newly($user->id,"-" . $price,
            $user->money,($user->money-$price),"定制邀请链接后缀名：{$customcode}");

        $code = InviteCode::where('user_id', $user->id)->first();
        $code->code = $customcode;
        $user->money -= $price;
        $user->save();
        $code->save();
        $res['ret'] = 1;
        $res['msg'] = '定制成功';
        return $response->getBody()->write(json_encode($res));
    }

    public function sys()
    {
        return $this->view()->assign('ana', '')->display('user/sys.tpl');
    }

    public function updatePassword($request, $response, $args)
    {
        $oldpwd = $request->getParam('oldpwd');
        $pwd = $request->getParam('pwd');
        $repwd = $request->getParam('repwd');
        $user = $this->user;
        if (!Hash::checkPassword($user->pass, $oldpwd)) {
            $res['ret'] = 0;
            $res['msg'] = '旧密码错误';
            return $response->getBody()->write(json_encode($res));
        }
        if ($pwd != $repwd) {
            $res['ret'] = 0;
            $res['msg'] = '两次输入不符合';
            return $response->getBody()->write(json_encode($res));
        }

        if (strlen($pwd) < 8) {
            $res['ret'] = 0;
            $res['msg'] = '密码太短啦';
            return $response->getBody()->write(json_encode($res));
        }
        $hashPwd = Hash::passwordHash($pwd);
        $user->pass = $hashPwd;
        $user->save();

        $user->clean_link();

        $res['ret'] = 1;
        $res['msg'] = '修改成功';
        return $this->echoJson($response, $res);
    }

    public function updateHide($request, $response, $args)
    {
        $hide = $request->getParam('hide');
        $user = $this->user;
        $user->is_hide = $hide;
        $user->save();

        $res['ret'] = 1;
        $res['msg'] = '修改成功';
        return $this->echoJson($response, $res);
    }

    public function Unblock($request, $response, $args)
    {
        $user = $this->user;
        $BIP = BlockIp::where('ip', $_SERVER['REMOTE_ADDR'])->get();
        foreach ($BIP as $bi) {
            $bi->delete();
        }

        $UIP = new UnblockIp();
        $UIP->userid = $user->id;
        $UIP->ip = $_SERVER['REMOTE_ADDR'];
        $UIP->datetime = time();
        $UIP->save();


        $res['ret'] = 1;
        $res['msg'] = $_SERVER['REMOTE_ADDR'];
        return $this->echoJson($response, $res);
    }

    public function shop($request, $response, $args)
    {
        $datatables = new Datatables(new DatatablesHelper());
        $datatables->query('SELECT id,name,content FROM `shop` where `parentid`=`id` and status=1 ORDER BY `shop`.`id` ASC');
        $shops = json_decode($datatables->generate(),true)['data'];
        $shopLevel = AgentConfig::getShopLevel($this->user->id);

        //将商店价格重置为代理设置价格
        foreach ($shops as $k => $v){
            $shops[$k]['name'] = explode('月付',$v['name'])[0];
            $shops[$k]['price'] = Config::get('agent')->shop["id{$v['id']}"]["p{$shopLevel}"];
            $shops[$k]['msg'] = json_decode($v['content'],true);
            $shops[$k]['msg']['extra'] = explode(';',$shops[$k]['msg']['content_extra']);
        }
//        var_export($shops);exit;
        return $this->view()
            ->assign('shops', $shops)
            ->display('user/shop.tpl');
    }

    public function CouponCheck($request, $response, $args)
    {
        $coupon = $request->getParam('coupon');
        $coupon = trim($coupon);

        $user = $this->user;

        if (!$user->isLogin) {
            $res['ret'] = -1;
            return $response->getBody()->write(json_encode($res));
        }

        $shop = $request->getParam('shop');

        $shop = Shop::where('id', $shop)->where('status', 1)->first();

        if ($shop == null) {
            $res['ret'] = 0;
            $res['msg'] = '非法请求';
            return $response->getBody()->write(json_encode($res));
        }

        //将商店价格重置为代理设置价格
        $shopLevel = AgentConfig::getShopLevel($this->user->id);
        $shop->price = Config::get('agent')->shop["id{$shop->id}"]["p{$shopLevel}"];

        if ($coupon == '') {
            $res['ret'] = 1;
            $res['name'] = $shop->name;
            $res['credit'] = '0 %';
            $res['total'] = $shop->price . '元';
            return $response->getBody()->write(json_encode($res));
        }

        $coupon = Coupon::where('code', $coupon)->first();

        if ($coupon == null) {
            $res['ret'] = 0;
            $res['msg'] = '优惠码无效';
            return $response->getBody()->write(json_encode($res));
        }

        if ($coupon->order($shop->id) == false) {
            $res['ret'] = 0;
            $res['msg'] = '此优惠码不可用于此商品';
            return $response->getBody()->write(json_encode($res));
        }

        $use_limit = $coupon->onetime;
        if ($use_limit > 0) {
            $use_count = Bought::where('userid', $user->id)->where('coupon', $coupon->code)->count();
            if ($use_count >= $use_limit) {
                $res['ret'] = 0;
                $res['msg'] = '优惠码次数已用完';
                return $response->getBody()->write(json_encode($res));
            }
        }

        $res['ret'] = 1;
        $res['name'] = $shop->name;
        $res['credit'] = $coupon->credit . ' %';
        $res['total'] = $shop->price * ((100 - $coupon->credit) / 100) . '元';

        return $response->getBody()->write(json_encode($res));
    }

    /**
     * @Notes:
     * 1.废弃优惠码
     * 2.增加代理价格购买
     * 3.根据代理成本价返佣收益金额以及收益记录
     * @Interface buy
     * @Author: 133814250@qq.com MengShuai
     * @Date: 2020/2/26   22:32
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function buy($request, $response, $args)
    {

        $shoptime = explode("month",$request->getParam('shoptime'))[0];
        $coupon = $request->getParam('coupon');
        $coupon = trim($coupon);
        $code = $coupon;
        $shop = $request->getParam('shop');
        $disableothers = $request->getParam('disableothers');
        $autorenew = $request->getParam('autorenew');

        $shop = Shop::where('parentid', $shop)->where('month',$shoptime)->where('status', 1)->first();

        if ($shop == null) {
            $res['ret'] = 0;
            $res['msg'] = '非法请求';
            return $response->getBody()->write(json_encode($res));
        }

        if ($coupon == '') {
            $credit = 0;
        } else {
            $coupon = Coupon::where('code', $coupon)->first();

            if ($coupon == null) {
                $credit = 0;
            } else {
                if ($coupon->onetime == 1) {
                    $onetime = true;
                }

                $credit = $coupon->credit;
            }

            if ($coupon->order($shop->id) == false) {
                $res['ret'] = 0;
                $res['msg'] = '此优惠码不可用于此商品';
                return $response->getBody()->write(json_encode($res));
            }

            if ($coupon->expire < time()) {
                $res['ret'] = 0;
                $res['msg'] = '此优惠码已过期';
                return $response->getBody()->write(json_encode($res));
            }
        }

        //获取代理商店价格
        $shopLevel = AgentConfig::getShopLevel($this->user->id);
        $price = Config::get('agent')->shop["id{$shop->id}"]["p{$shopLevel}"] * ((100 - $credit) / 100);
        $user = $this->user;

        if (!$user->isLogin) {
            $res['ret'] = -1;
            return $response->getBody()->write(json_encode($res));
        }

        if (bccomp($user->money, $price, 2) == -1) {
            $res['ret'] = 0;
            $res['msg'] = '喵喵喵~ 当前余额不足，总价为' . $price . '元。</br><a href="/user/code">点击进入充值界面</a>';
            return $response->getBody()->write(json_encode($res));
        }

        //资金变动
        AgentMoneyLog::Newly($user->id,"-" . $price,
            $user->money,(bcsub($user->money, $price, 2)),"购买套餐：" . explode('（',$shop->name)[0]);
        $user->money = bcsub($user->money, $price, 2);
        $user->save();

        if ($disableothers == 1) {
            $boughts = Bought::where('userid', $user->id)->get();
            foreach ($boughts as $disable_bought) {
                $disable_bought->renew = 0;
                $disable_bought->save();
            }
        }

        $bought = new Bought();
        $bought->userid = $user->id;
        $bought->shopid = $shop->id;
        $bought->datetime = time();
        if ($autorenew == 0 || $shop->auto_renew == 0) {
            $bought->renew = 0;
        } else {
            $bought->renew = time() + $shop->auto_renew * 86400;
        }

        $bought->coupon = $code;


        if (isset($onetime)) {
            $price = $shop->price;
        }
        $bought->price = $price;
        $bought->save();

        $shop->buy($user);
        
        //更新代理收益
        AgentConfig::addIncome(Config::get('agent')->uid, $this->user->id,
            Config::get('agent')->level, $shop->id,$price,'用户收益'
        );

        $res['ret'] = 1;
        $res['msg'] = '购买成功';

        return $response->getBody()->write(json_encode($res));
    }



    public function bought($request, $response, $args)
    {
        $pageNum = $request->getQueryParams()['page'] ?? 1;
        $shops = Bought::where('userid', $this->user->id)->orderBy('id', 'desc')->paginate(15, ['*'], 'page', $pageNum);
        $shops->setPath('/user/bought');

        return $this->view()->assign('shops', $shops)->display('user/bought.tpl');
    }

    public function deleteBoughtGet($request, $response, $args)
    {
        $id = $request->getParam('id');
        $shop = Bought::where('id', $id)->where('userid', $this->user->id)->first();

        if ($shop == null) {
            $rs['ret'] = 0;
            $rs['msg'] = '关闭自动续费失败，订单不存在。';
            return $response->getBody()->write(json_encode($rs));
        }

        if ($this->user->id == $shop->userid) {
            $shop->renew = 0;
        }

        if (!$shop->save()) {
            $rs['ret'] = 0;
            $rs['msg'] = '关闭自动续费失败';
            return $response->getBody()->write(json_encode($rs));
        }
        $rs['ret'] = 1;
        $rs['msg'] = '关闭自动续费成功';
        return $response->getBody()->write(json_encode($rs));
    }


    public function ticket($request, $response, $args)
    {
        if (Config::get('enable_ticket') != 'true') {
            exit(0);
        }
        $pageNum = $request->getQueryParams()['page'] ?? 1;
        $tickets = Ticket::where('userid', $this->user->id)->where('rootid', 0)->orderBy('datetime', 'desc')->paginate(15, ['*'], 'page', $pageNum);
        $tickets->setPath('/user/ticket');

        return $this->view()->assign('tickets', $tickets)->display('user/ticket.tpl');
    }

    public function ticket_create($request, $response, $args)
    {
        return $this->view()->display('user/ticket_create.tpl');
    }

    public function ticket_add($request, $response, $args)
    {
        $title = $request->getParam('title');
        $content = $request->getParam('content');
        $markdown = $request->getParam('markdown');

        if ($title == '' || $content == '') {
            $res['ret'] = 0;
            $res['msg'] = '非法输入';
            return $this->echoJson($response, $res);
        }

        if (strpos($content, 'admin') != false || strpos($content, 'user') != false) {
            $res['ret'] = 0;
            $res['msg'] = '请求中有不当词语';
            return $this->echoJson($response, $res);
        }

        $ticket = new Ticket();
        $antiXss = new AntiXSS();

        $ticket->title = $antiXss->xss_clean($title);
        $ticket->content = $antiXss->xss_clean($content);
        $ticket->rootid = 0;
        $ticket->userid = $this->user->id;
        $ticket->datetime = time();
        $ticket->save();

        if (Config::get('mail_ticket') == 'true' && $markdown != '') {
            $adminUser = User::where('is_admin', '=', '1')->get();
            foreach ($adminUser as $user) {
                $subject = Config::get('appName') . '-新工单被开启';
                $to = $user->email;
                $text = '管理员，有人开启了新的工单，请您及时处理。';
                try {
                    Mail::send($to, $subject, 'news/warn.tpl', [
                        'user' => $user, 'text' => $text
                    ], [
                    ]);
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            }
        }

        if (Config::get('useScFtqq') == 'true' && $markdown != '') {
            $ScFtqq_SCKEY = Config::get('ScFtqq_SCKEY');
            $postdata = http_build_query(
                array(
                    'text' => Config::get('appName') . '-新工单被开启',
                    'desp' => $markdown
                )
            );
            $opts = array('http' =>
                array(
                    'method' => 'POST',
                    'header' => 'Content-type: application/x-www-form-urlencoded',
                    'content' => $postdata
                ));
            $context = stream_context_create($opts);
            file_get_contents('https://sc.ftqq.com/' . $ScFtqq_SCKEY . '.send', false, $context);
        }

        $res['ret'] = 1;
        $res['msg'] = '提交成功';
        return $this->echoJson($response, $res);
    }

    public function ticket_update($request, $response, $args)
    {
        $id = $args['id'];
        $content = $request->getParam('content');
        $status = $request->getParam('status');
        $markdown = $request->getParam('markdown');

        if ($content == '' || $status == '') {
            $res['ret'] = 0;
            $res['msg'] = '非法输入';
            return $this->echoJson($response, $res);
        }

        if (strpos($content, 'admin') != false || strpos($content, 'user') != false) {
            $res['ret'] = 0;
            $res['msg'] = '请求中有不当词语';
            return $this->echoJson($response, $res);
        }


        $ticket_main = Ticket::where('id', '=', $id)->where('rootid', '=', 0)->first();
        if ($ticket_main->userid != $this->user->id) {
            $newResponse = $response->withStatus(302)->withHeader('Location', '/user/ticket');
            return $newResponse;
        }

        if ($status == 1 && $ticket_main->status != $status) {
            if (Config::get('mail_ticket') == 'true' && $markdown != '') {
                $adminUser = User::where('is_admin', '=', '1')->get();
                foreach ($adminUser as $user) {
                    $subject = Config::get('appName') . '-工单被重新开启';
                    $to = $user->email;
                    $text = '管理员，有人重新开启了<a href="' . Config::get('baseUrl') . '/admin/ticket/' . $ticket_main->id . '/view">工单</a>，请您及时处理。';
                    try {
                        Mail::send($to, $subject, 'news/warn.tpl', [
                            'user' => $user, 'text' => $text
                        ], [
                        ]);
                    } catch (Exception $e) {
                        echo $e->getMessage();
                    }
                }
            }
            if (Config::get('useScFtqq') == 'true' && $markdown != '') {
                $ScFtqq_SCKEY = Config::get('ScFtqq_SCKEY');
                $postdata = http_build_query(
                    array(
                        'text' => Config::get('appName') . '-工单被重新开启',
                        'desp' => $markdown
                    )
                );
                $opts = array('http' =>
                    array(
                        'method' => 'POST',
                        'header' => 'Content-type: application/x-www-form-urlencoded',
                        'content' => $postdata
                    ));
                $context = stream_context_create($opts);
                file_get_contents('https://sc.ftqq.com/' . $ScFtqq_SCKEY . '.send', false, $context);
                $useScFtqq = Config::get('ScFtqq_SCKEY');
            }
        } else {
            if (Config::get('mail_ticket') == 'true' && $markdown != '') {
                $adminUser = User::where('is_admin', '=', '1')->get();
                foreach ($adminUser as $user) {
                    $subject = Config::get('appName') . '-工单被回复';
                    $to = $user->email;
                    $text = '管理员，有人回复了<a href="' . Config::get('baseUrl') . '/admin/ticket/' . $ticket_main->id . '/view">工单</a>，请您及时处理。';
                    try {
                        Mail::send($to, $subject, 'news/warn.tpl', [
                            'user' => $user, 'text' => $text
                        ], [
                        ]);
                    } catch (Exception $e) {
                        echo $e->getMessage();
                    }
                }
            }
            if (Config::get('useScFtqq') == 'true' && $markdown != '') {
                $ScFtqq_SCKEY = Config::get('ScFtqq_SCKEY');
                $postdata = http_build_query(
                    array(
                        'text' => Config::get('appName') . '-工单被回复',
                        'desp' => $markdown
                    )
                );
                $opts = array('http' =>
                    array(
                        'method' => 'POST',
                        'header' => 'Content-type: application/x-www-form-urlencoded',
                        'content' => $postdata
                    ));
                $context = stream_context_create($opts);
                file_get_contents('https://sc.ftqq.com/' . $ScFtqq_SCKEY . '.send', false, $context);
            }
        }

        $antiXss = new AntiXSS();

        $ticket = new Ticket();
        $ticket->title = $antiXss->xss_clean($ticket_main->title);
        $ticket->content = $antiXss->xss_clean($content);
        $ticket->rootid = $ticket_main->id;
        $ticket->userid = $this->user->id;
        $ticket->datetime = time();
        $ticket_main->status = $status;

        $ticket_main->save();
        $ticket->save();


        $res['ret'] = 1;
        $res['msg'] = '提交成功';
        return $this->echoJson($response, $res);
    }

    public function ticket_view($request, $response, $args)
    {
        $id = $args['id'];
        $ticket_main = Ticket::where('id', '=', $id)->where('rootid', '=', 0)->first();
        if ($ticket_main->userid != $this->user->id) {
            $newResponse = $response->withStatus(302)->withHeader('Location', '/user/ticket');
            return $newResponse;
        }

        $pageNum = $request->getQueryParams()['page'] ?? 1;


        $ticketset = Ticket::where('id', $id)->orWhere('rootid', '=', $id)->orderBy('datetime', 'desc')->paginate(5, ['*'], 'page', $pageNum);
        $ticketset->setPath('/user/ticket/' . $id . '/view');


        return $this->view()->assign('ticketset', $ticketset)->assign('id', $id)->display('user/ticket_view.tpl');
    }


    public function updateWechat($request, $response, $args)
    {
        $type = $request->getParam('imtype');
        $wechat = $request->getParam('wechat');
        $wechat = trim($wechat);

        $user = $this->user;

        if ($user->telegram_id != 0) {
            $res['ret'] = 0;
            $res['msg'] = '您绑定了 Telegram ，所以此项并不能被修改。';
            return $response->getBody()->write(json_encode($res));
        }

        if ($user->discord != 0) {
            $res['ret'] = 0;
            $res['msg'] = '您绑定了 Discord ，所以此项并不能被修改。';
            return $response->getBody()->write(json_encode($res));
        }

        if ($wechat == '' || $type == '') {
            $res['ret'] = 0;
            $res['msg'] = '非法输入';
            return $response->getBody()->write(json_encode($res));
        }

        $user1 = User::where('im_value', $wechat)->where('im_type', $type)->first();
        if ($user1 != null) {
            $res['ret'] = 0;
            $res['msg'] = '此联络方式已经被注册';
            return $response->getBody()->write(json_encode($res));
        }

        $user->im_type = $type;
        $antiXss = new AntiXSS();
        $user->im_value = $antiXss->xss_clean($wechat);
        $user->save();

        $res['ret'] = 1;
        $res['msg'] = '修改成功';
        return $this->echoJson($response, $res);
    }


    public function updateSSR($request, $response, $args)
    {
        $protocol = $request->getParam('protocol');
        $obfs = $request->getParam('obfs');
        $obfs_param = $request->getParam('obfs_param');
        $obfs_param = trim($obfs_param);

        $user = $this->user;

        if ($obfs == '' || $protocol == '') {
            $res['ret'] = 0;
            $res['msg'] = '非法输入';
            return $response->getBody()->write(json_encode($res));
        }

        if (!Tools::is_param_validate('obfs', $obfs)) {
            $res['ret'] = 0;
            $res['msg'] = '混淆无效';
            return $response->getBody()->write(json_encode($res));
        }

        if (!Tools::is_param_validate('protocol', $protocol)) {
            $res['ret'] = 0;
            $res['msg'] = '协议无效';
            return $response->getBody()->write(json_encode($res));
        }

        $antiXss = new AntiXSS();

        $user->protocol = $antiXss->xss_clean($protocol);
        $user->obfs = $antiXss->xss_clean($obfs);
        $user->obfs_param = $antiXss->xss_clean($obfs_param);

        if (!Tools::checkNoneProtocol($user)) {
            $res['ret'] = 0;
            $res['msg'] = '系统检测到您目前的加密方式为 none ，但您将要设置为的协议并不在以下协议<br>' . implode(',', Config::getSupportParam('allow_none_protocol')) . '<br>之内，请您先修改您的加密方式，再来修改此处设置。';
            return $this->echoJson($response, $res);
        }

        if (!URL::SSCanConnect($user) && !URL::SSRCanConnect($user)) {
            $res['ret'] = 0;
            $res['msg'] = '您这样设置之后，就没有客户端能连接上了，所以系统拒绝了您的设置，请您检查您的设置之后再进行操作。';
            return $this->echoJson($response, $res);
        }

        $user->save();

        if (!URL::SSCanConnect($user)) {
            $res['ret'] = 1;
            $res['msg'] = '设置成功，但您目前的协议，混淆，加密方式设置会导致 Shadowsocks原版客户端无法连接，请您自行更换到 ShadowsocksR 客户端。';
            return $this->echoJson($response, $res);
        }

        if (!URL::SSRCanConnect($user)) {
            $res['ret'] = 1;
            $res['msg'] = '设置成功，但您目前的协议，混淆，加密方式设置会导致 ShadowsocksR 客户端无法连接，请您自行更换到 Shadowsocks 客户端。';
            return $this->echoJson($response, $res);
        }

        $res['ret'] = 1;
        $res['msg'] = '设置成功，您可自由选用客户端来连接。';
        return $this->echoJson($response, $res);
    }

    public function updateTheme($request, $response, $args)
    {
        $theme = $request->getParam('theme');

        $user = $this->user;

        if ($theme == '') {
            $res['ret'] = 0;
            $res['msg'] = '非法输入';
            return $response->getBody()->write(json_encode($res));
        }


        $user->theme = filter_var($theme, FILTER_SANITIZE_STRING);
        $user->save();

        $res['ret'] = 1;
        $res['msg'] = '设置成功';
        return $this->echoJson($response, $res);
    }


    public function updateMail($request, $response, $args)
    {
        $mail = $request->getParam('mail');
        $mail = trim($mail);
        $user = $this->user;

        if (!($mail == '1' || $mail == '0')) {
            $res['ret'] = 0;
            $res['msg'] = '非法输入';
            return $response->getBody()->write(json_encode($res));
        }


        $user->sendDailyMail = $mail;
        $user->save();

        $res['ret'] = 1;
        $res['msg'] = '修改成功';
        return $this->echoJson($response, $res);
    }

    public function PacSet($request, $response, $args)
    {
        $pac = $request->getParam('pac');

        $user = $this->user;

        if ($pac == '') {
            $res['ret'] = 0;
            $res['msg'] = '输入不能为空';
            return $response->getBody()->write(json_encode($res));
        }


        $user->pac = $pac;
        $user->save();

        $res['ret'] = 1;
        $res['msg'] = '修改成功';
        return $this->echoJson($response, $res);
    }


    public function updateSsPwd($request, $response, $args)
    {
        $user = Auth::getUser();
        $pwd = $request->getParam('sspwd');
        $pwd = trim($pwd);

        if ($pwd == '') {
            $res['ret'] = 0;
            $res['msg'] = '密码不能为空';
            return $response->getBody()->write(json_encode($res));
        }

        if (!Tools::is_validate($pwd)) {
            $res['ret'] = 0;
            $res['msg'] = '密码无效';
            return $response->getBody()->write(json_encode($res));
        }

        $user->updateSsPwd($pwd);
        $res['ret'] = 1;


        Radius::Add($user, $pwd);


        return $this->echoJson($response, $res);
    }

    public function updateMethod($request, $response, $args)
    {
        $user = Auth::getUser();
        $method = $request->getParam('method');
        $method = strtolower($method);

        if ($method == '') {
            $res['ret'] = 0;
            $res['msg'] = '非法输入';
            return $response->getBody()->write(json_encode($res));
        }

        if (!Tools::is_param_validate('method', $method)) {
            $res['ret'] = 0;
            $res['msg'] = '加密无效';
            return $response->getBody()->write(json_encode($res));
        }

        $user->method = $method;

        if (!Tools::checkNoneProtocol($user)) {
            $res['ret'] = 0;
            $res['msg'] = '系统检测到您将要设置的加密方式为 none ，但您的协议并不在以下协议<br>' . implode(',', Config::getSupportParam('allow_none_protocol')) . '<br>之内，请您先修改您的协议，再来修改此处设置。';
            return $this->echoJson($response, $res);
        }

        if (!URL::SSCanConnect($user) && !URL::SSRCanConnect($user)) {
            $res['ret'] = 0;
            $res['msg'] = '您这样设置之后，就没有客户端能连接上了，所以系统拒绝了您的设置，请您检查您的设置之后再进行操作。';
            return $this->echoJson($response, $res);
        }

        $user->updateMethod($method);

        if (!URL::SSCanConnect($user)) {
            $res['ret'] = 0;
            $res['msg'] = '设置成功，但您目前的协议，混淆，加密方式设置会导致 Shadowsocks原版客户端无法连接，请您自行更换到 ShadowsocksR 客户端。';
            return $this->echoJson($response, $res);
        }

        if (!URL::SSRCanConnect($user)) {
            $res['ret'] = 1;
            $res['msg'] = '设置成功，但您目前的协议，混淆，加密方式设置会导致 ShadowsocksR 客户端无法连接，请您自行更换到 Shadowsocks 客户端。';
            return $this->echoJson($response, $res);
        }

        $res['ret'] = 1;
        $res['msg'] = '设置成功，您可自由选用两种客户端来进行连接。';
        return $this->echoJson($response, $res);
    }

    public function logout($request, $response, $args)
    {
        Auth::logout();
        return $response->withStatus(302)->withHeader('Location', '/');
    }

    public function doCheckIn($request, $response, $args)
    {
        if (Config::get('enable_checkin_captcha') == 'true') {
            switch (Config::get('captcha_provider')) {
                case 'recaptcha':
                    $recaptcha = $request->getParam('recaptcha');
                    if ($recaptcha == '') {
                        $ret = false;
                    } else {
                        $json = file_get_contents('https://recaptcha.net/recaptcha/api/siteverify?secret=' . Config::get('recaptcha_secret') . '&response=' . $recaptcha);
                        $ret = json_decode($json)->success;
                    }
                    break;
                case 'geetest':
                    $ret = Geetest::verify($request->getParam('geetest_challenge'), $request->getParam('geetest_validate'), $request->getParam('geetest_seccode'));
                    break;
            }
            if (!$ret) {
                $res['ret'] = 0;
                $res['msg'] = '系统无法接受您的验证结果，请刷新页面后重试。';
                return $response->getBody()->write(json_encode($res));
            }
        }

        if (strtotime($this->user->expire_in) < time()) {
            $res['ret'] = 0;
            $res['msg'] = '您的账户已过期，无法签到。';
            return $response->getBody()->write(json_encode($res));
        }

        if (!$this->user->isAbleToCheckin()) {
            $res['ret'] = 0;
            $res['msg'] = '您似乎已经签到过了...';
            return $response->getBody()->write(json_encode($res));
        }
        $traffic = random_int(Config::get('checkinMin'), Config::get('checkinMax'));
        $this->user->transfer_enable += Tools::toMB($traffic);
        $this->user->last_check_in_time = time();
        $this->user->save();
        $res['msg'] = sprintf('获得了 %d MB流量.', $traffic);
        $res['unflowtraffic'] = $this->user->transfer_enable;
        $res['traffic'] = Tools::flowAutoShow($this->user->transfer_enable);
        $res['trafficInfo'] = array(
            'todayUsedTraffic' => $this->user->TodayusedTraffic(),
            'lastUsedTraffic' => $this->user->LastusedTraffic(),
            'unUsedTraffic' => $this->user->unusedTraffic(),
        );
        $res['ret'] = 1;
        return $this->echoJson($response, $res);
    }

    public function kill($request, $response, $args)
    {
        return $this->view()->display('user/kill.tpl');
    }

    public function handleKill($request, $response, $args)
    {
        $user = Auth::getUser();

        $email = $user->email;

        $passwd = $request->getParam('passwd');
        // check passwd
        $res = array();
        if (!Hash::checkPassword($user->pass, $passwd)) {
            $res['ret'] = 0;
            $res['msg'] = ' 密码错误';
            return $this->echoJson($response, $res);
        }

        if (Config::get('enable_kill') == 'true') {
            Auth::logout();
            $user->kill_user();
            $res['ret'] = 1;
            $res['msg'] = '您的帐号已经从我们的系统中删除。欢迎下次光临!';
        } else {
            $res['ret'] = 0;
            $res['msg'] = '管理员不允许删除，如需删除请联系管理员。';
        }
        return $this->echoJson($response, $res);
    }

    public function trafficLog($request, $response, $args)
    {
        $traffic = TrafficLog::where('user_id', $this->user->id)->where('log_time', '>', time() - 3 * 86400)->orderBy('id', 'desc')->get();
        return $this->view()->assign('logs', $traffic)->display('user/trafficlog.tpl');
    }


    public function detect_index($request, $response, $args)
    {
        $pageNum = $request->getQueryParams()['page'] ?? 1;
        $logs = DetectRule::paginate(15, ['*'], 'page', $pageNum);
        $logs->setPath('/user/detect');
        return $this->view()->assign('rules', $logs)->display('user/detect_index.tpl');
    }

    public function detect_log($request, $response, $args)
    {
        $pageNum = $request->getQueryParams()['page'] ?? 1;
        $logs = DetectLog::orderBy('id', 'desc')->where('user_id', $this->user->id)->paginate(15, ['*'], 'page', $pageNum);
        $logs->setPath('/user/detect/log');
        return $this->view()->assign('logs', $logs)->display('user/detect_log.tpl');
    }

    public function disable($request, $response, $args)
    {
        return $this->view()->display('user/disable.tpl');
    }

    public function discord_reset($request, $response, $args)
    {
        $user = $this->user;
        $user->discord = 0;
        $user->save();
        return $response->withStatus(302)->withHeader('Location', '/user/edit');
    }

    public function telegram_reset($request, $response, $args)
    {
        $user = $this->user;
        $user->telegram_id = 0;
        $user->save();
        return $response->withStatus(302)->withHeader('Location', '/user/edit');
    }

    public function resetURL($request, $response, $args)
    {
        $user = $this->user;
        $user->clean_link();
        return $response->withStatus(302)->withHeader('Location', '/user');
    }

    public function resetInviteURL($request, $response, $args)
    {
        $user = $this->user;
        $user->clear_inviteCodes();
        return $response->withStatus(302)->withHeader('Location', '/user/invite');
    }

    public function backtoadmin($request, $response, $args)
    {
        $userid = Utils\Cookie::get('uid');
        $adminid = Utils\Cookie::get('old_uid');
        $user = User::find($userid);
        $admin = User::find($adminid);

        if (!$admin->is_admin || !$user) {
            Utils\Cookie::set([
                'uid' => null,
                'email' => null,
                'key' => null,
                'ip' => null,
                'expire_in' => null,
                'old_uid' => null,
                'old_email' => null,
                'old_key' => null,
                'old_ip' => null,
                'old_expire_in' => null,
                'old_local' => null
            ], time() - 1000);
        }
        $expire_in = Utils\Cookie::get('old_expire_in');
        $local = Utils\Cookie::get('old_local');
        Utils\Cookie::set([
            'uid' => Utils\Cookie::get('old_uid'),
            'email' => Utils\Cookie::get('old_email'),
            'key' => Utils\Cookie::get('old_key'),
            'ip' => Utils\Cookie::get('old_ip'),
            'expire_in' => $expire_in,
            'old_uid' => null,
            'old_email' => null,
            'old_key' => null,
            'old_ip' => null,
            'old_expire_in' => null,
            'old_local' => null
        ], $expire_in);
        return $response->withStatus(302)->withHeader('Location', $local);
    }
}
