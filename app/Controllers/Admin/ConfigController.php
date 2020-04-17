<?php
/**
 *  站点配置
 */

namespace App\Controllers\Admin;

use App\Models\AgentConfig;
use App\Controllers\AdminController;
use Exception;
use Ozdemir\Datatables\Datatables;
use App\Utils\DatatablesHelper;

use App\Models\AgentMoneyLog;
use App\Models\InviteCode;
use App\Services\Config;
use App\Services\BtApi\Bt;
use App\Utils\Check;
use App\Utils\Tools;
use App\Utils\Common;
use App\Utils\Radius;
use voku\helper\AntiXSS;
use App\Utils\Hash;
use App\Utils\Da;
use App\Services\Auth;
use App\Services\Mail;
use App\Models\User;
use App\Models\LoginIp;
use App\Models\EmailVerify;
use App\Utils\GA;
use App\Utils\Geetest;
use App\Utils\TelegramSessionManager;

class ConfigController extends AdminController
{

    public function index($request, $response, $args)
    {

        $table_config['total_column'] = array('id' => 'ID',
            'datetime' => '时间', 'type' => '类型', 'value' => '内容');
        $table_config['default_show_column'] = array('op', 'id',
            'datetime', 'type', 'value');
        $table_config['ajax_url'] = 'auto/ajax';

        $agent =  new AgentConfig();
        $agent = $agent->agent();
        $agent->update_domain_price = Config::get('update_domain_price');
        return $this->view()
            ->assign('users',$this->user)
            ->assign('agent',$agent)
            ->assign('Host_Analysis',Config::get('Host_Analysis'))
            ->assign('table_config', $table_config)
            ->display('admin/config/index.tpl');
    }


    /**
     * @Notes:
     * 修改在线客服信息
     * @Interface update_chatra
     * @author [MengShuai] [<133814250@qq.com>]
     */
    public function update_chatra($request, $response, $args){
        $enable_chatra = (string) $request->getParam('enable_chatra') ? : '0';
        $chatra_id = (string) $request->getParam('chatra_id') ?: '';

        $agent = AgentConfig::where('uid',$this->user->id)->first();
        $panel = json_decode($agent->panel,true);
        $panel = [
            'name' => $panel['name'],
            'chatra_id' => $chatra_id,
            'enable_chatra' => $enable_chatra,
        ];
        $agent->panel = json_encode($panel);
        $agent->save();
        $res['ret'] = 1;
        $res['msg'] = '修改成功';
        return $response->getBody()->write(json_encode($res));
    }

    public function update_cash($request, $response, $args){
        $code = (string) $request->getParam('cash_email_code') ?: '';
        $name = (string) $request->getParam('account_name') ?: '';
        $account = (string) $request->getParam('account') ?: '';
        $img = (string) $request->getParam('account_code') ?: '';

        if (!EmailVerify::where('email', '=', $this->user->email)
            ->where('code', '=', $code)
            ->first()) {
            $res['ret'] = 0;
            $res['msg'] = '验证码错误';
            return $response->getBody()->write(json_encode($res));
        }
        $agent = AgentConfig::where('uid',$this->user->id)->first();
        $agent->cash = [
            'name' => $name,
            'account' => $account,
            'img' => $img,
        ];
        $agent->cash = json_encode($agent->cash);
        $agent->save();
        $res['ret'] = 1;
        $res['msg'] = '修改成功';
        return $response->getBody()->write(json_encode($res));
    }


    /**
     * @Notes:
     * 上传收款码到api云储存
     * @Interface cupdate
     * @Author: 133814250@qq.com MengShuai
     * @Date: 2020/2/23   0:38
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function cupdate($request, $response, $args){
        if($_FILES && $_FILES['file']['size'] > 1048576*3){
            $res['ret'] = 0;
            $res['msg'] = '上传图片不能超过3M';
            return $response->getBody()->write(json_encode($res));
        }
        $filename = 'qq133814250_agent_panel_312476439_cash_uid' . $this->user->id . '_' . date("YmdHis",time()) . rand(1000,9999) . '.png';
        $data = [
            'name' => $filename,
            'file'  => curl_file_create($_FILES['file']['tmp_name'], $_FILES['file']['type'], $_FILES['file']['name']),
        ];
        $result = json_decode(Common::Post($data,Config::get('chart_service')),true);
        $img_url = $result['data']['info'];
        if(!Common::is_url($img_url)){
            $res['ret'] = 0;
            $res['msg'] = '上传失败  请检查图片格式  多次出现请联系管理员。';
            return $response->getBody()->write(json_encode($res));
        }

        $res['ret'] = 1;
        $res['msg'] = '上传成功';
        $res['url'] = $img_url;
        return $response->getBody()->write(json_encode($res));
    }


    /**
     * @Notes:
     * 更新网站名称和域名
     * @Interface dupdate
     * @Author: 133814250@qq.com MengShuai
     * @Date: 2020/2/29   22:13
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function dupdate($request, $response, $args){
        $domain = trim((string) $request->getParam('domain')) ?: '';
        $name = (string) $request->getParam('name') ?: '';

        if(in_array($domain,Config::get('Host_Blacklist'))){
            $res['ret'] = 0;
            $res['msg'] = '你无权绑定此域名。';
            return $response->getBody()->write(json_encode($res));
        }
        if($domain != '') {
            $domains = explode(',',$domain);
            if(count($domains) > 1){
                $res['ret'] = 0;
                $res['msg'] = '等级限制：域名最多可以绑定1个';
                return $response->getBody()->write(json_encode($res));
            }

            foreach ($domains as $d) {
                if (Common::is_domain($d) == false) {
                    $res['ret'] = 0;
                    $res['msg'] = '域名格式错误';
                    return $response->getBody()->write(json_encode($res));
                }

                $lists = AgentConfig::where('domain', 'LIKE', "%{$d}%")->get();
                if($lists) {
                    foreach ($lists as $k => $v) {
                        $domain_array = explode(',', $v['domain']);
                        //存在匹配直接返回
                        if (in_array($domain, $domain_array) && ($v['uid']!=$this->user->id)) {
                            $res['ret'] = 0;
                            $res['msg'] = '此域名已被绑定';
                            return $response->getBody()->write(json_encode($res));
                        }
                    }
                }

            }
        }

        $AgentConfig = AgentConfig::where(['uid'=>$this->user->id])->first();
        if(!$AgentConfig){
            $res['ret'] = 0;
            $res['msg'] = '不存在代理配置 请联系管理员。';
            return $response->getBody()->write(json_encode($res));
        }

        //通过api添加域名
        $bt = new Bt(Config::get('BT_PANEL'),Config::get('BT_KEY'));
        $DoaminList = array_column($bt->WebDoaminList(Config::get('BT_WEB_ID')),'name'); //域名列表
        if(!in_array($domain,$DoaminList)) {
            $result = $bt->WebAddDomain(Config::get('BT_WEB_ID'),Config::get('WEB_NAME'),$domain); //添加域名
            if($result['status']!=true){
                $res['ret'] = 0;
                $res['msg'] = $result['msg'];
                return $response->getBody()->write(json_encode($res));
            }
        }

        if($this->user->money < Config::get('update_domain_price')){
            $res['ret'] = 0;
            $res['msg'] = '余额不足，请先充值。</br></br><a href="/user/code">点击进入充值界面</a>';
            return $response->getBody()->write(json_encode($res));
        }

        //资金变动
        AgentMoneyLog::Newly($this->user->id,"-" . Config::get('update_domain_price'),
            $this->user->money,($this->user->money-Config::get('update_domain_price')),"修改绑定域名：{$name}@{$domain}");

        $this->user->money = $this->user->money-Config::get('update_domain_price');
        $this->user->save();

        $AgentConfig->domain = $domain;
        $panel = json_decode($AgentConfig->panel,true);
        $panel['name'] = $name;
        $AgentConfig->panel = json_encode($panel);
        $AgentConfig->save();

        $res['ret'] = 1;
        $res['msg'] = '修改成功';
        return $response->getBody()->write(json_encode($res));
    }


    public function edit_email($request, $response, $args){
            $act = $request->getParam('act');

            //发送验证码
            if($act == 'send' || $act == 'cash') {
                $email = $this->user->email;
                if ($email == '') {
                    $res['ret'] = 0;
                    $res['msg'] = '账号未设置邮箱 请联系管理员。';
                    return $response->getBody()->write(json_encode($res));
                }

                // check email format
                if (!Check::isEmailLegal($email)) {
                    $res['ret'] = 0;
                    $res['msg'] = '邮箱无效 请联系管理员。';
                    return $response->getBody()->write(json_encode($res));
                }

                $ipcount = EmailVerify::where('ip', '=', $_SERVER['REMOTE_ADDR'])->where('expire_in', '>', time())->count();
//                if ($ipcount >= (int)Config::get('email_verify_iplimit')) {
//                    $res['ret'] = 0;
//                    $res['msg'] = '此IP请求次数过多';
//                    return $response->getBody()->write(json_encode($res));
//                }


                $mailcount = EmailVerify::where('email', '=', $email)->where('expire_in', '>', time())->count();
//                if ($mailcount >= 3) {
//                    $res['ret'] = 0;
//                    $res['msg'] = '此邮箱请求次数过多';
//                    return $response->getBody()->write(json_encode($res));
//                }

                $code = Tools::genRandomNum(6);

                $ev = new EmailVerify();
                $ev->expire_in = time() + Config::get('email_verify_ttl');
                $ev->ip = $_SERVER['REMOTE_ADDR'];
                $ev->email = $email;
                $ev->code = $code;
                $ev->save();

                $subject = Config::get('appName') . '- 验证邮件';

                //判断邮件模板
                if($act == 'send') {
                    $emali_model = 'edit_email/verify.tpl';
                }elseif ($act == 'cash'){
                    $emali_model = 'edit_email/cash.tpl';
                }

                try {
                    Mail::send($email, $subject, $emali_model, [
                        'code' => $code, 'expire' => date('Y-m-d H:i:s', time() + Config::get('email_verify_ttl'))
                    ], [
                        //BASE_PATH.'/public/assets/email/styles.css'
                    ]);
                } catch (Exception $e) {
                    $res['ret'] = 0;
                    $res['msg'] = '邮件发送失败，请联系网站管理员。';
                    return $response->getBody()->write(json_encode($res));
                }

                $res['ret'] = 1;
                $res['msg'] = '验证码发送成功，请查收邮件。';
                return $response->getBody()->write(json_encode($res));

            //提交新的邮件修改
            }else{
                $email = $request->getParam('new_email');
                // check email format
                if (!Check::isEmailLegal($email)) {
                    $res['ret'] = 0;
                    $res['msg'] = '邮箱无效 请联系管理员。';
                    return $response->getBody()->write(json_encode($res));
                }

                $EmailVerify = EmailVerify::where('email', '=', $this->user->email)
                    ->where('expire_in', '>', time())
                    ->orderBy('id', 'desc')
                    ->first();
                if(!$EmailVerify){
                    $res['ret'] = 0;
                    $res['msg'] = '请先发送验证邮件。';
                    return $response->getBody()->write(json_encode($res));
                }

                $code = $request->getParam('email_code') ?: '';
                if($EmailVerify->code != $code){
                    $res['ret'] = 0;
                    $res['msg'] = '验证码错误。';
                    return $response->getBody()->write(json_encode($res));
                }

                $this->user->email = $email;
                $this->user->save();
                $res['ret'] = 1;
                $res['msg'] = '修改成功';
                return $response->getBody()->write(json_encode($res));
            }

    }



    public function create($request, $response, $args)
    {
        return $this->view()->display('admin/auto/add.tpl');
    }

    public function add($request, $response, $args)
    {
        $auto = new Auto();
        $auto->datetime = time();
        $auto->value = $request->getParam('content');
        $auto->sign = $request->getParam('sign');
        $auto->type = 1;

        if (!$auto->save()) {
            $rs['ret'] = 0;
            $rs['msg'] = '添加失败';
            return $response->getBody()->write(json_encode($rs));
        }
        $rs['ret'] = 1;
        $rs['msg'] = '添加成功';
        return $response->getBody()->write(json_encode($rs));
    }


    public function delete($request, $response, $args)
    {
        $id = $request->getParam('id');
        $auto = Auto::find($id);
        if (!$auto->delete()) {
            $rs['ret'] = 0;
            $rs['msg'] = '删除失败';
            return $response->getBody()->write(json_encode($rs));
        }
        $rs['ret'] = 1;
        $rs['msg'] = '删除成功';
        return $response->getBody()->write(json_encode($rs));
    }

    public function ajax($request, $response, $args)
    {
        $datatables = new Datatables(new DatatablesHelper());
        $datatables->query('Select id,datetime,type,value from auto');

        $datatables->edit('datetime', static function ($data) {
            return date('Y-m-d H:i:s', $data['datetime']);
        });

        $datatables->edit('type', static function ($data) {
            return $data['type'] == 1 ? '命令下发' : '命令被执行';
        });

        $body = $response->getBody();
        $body->write($datatables->generate());
    }
}
