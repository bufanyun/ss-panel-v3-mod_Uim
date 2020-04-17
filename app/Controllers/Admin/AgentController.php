<?php

namespace App\Controllers\Admin;

use App\Models\AgentMoneyLog;
use App\Models\DetectRule;
use App\Utils\Telegram;
use App\Controllers\AdminController;

use App\Services\Config;
use Ozdemir\Datatables\Datatables;
use App\Utils\DatatablesHelper;
use App\Models\AgentConfig;
use App\Models\AgentLevel;
use App\Models\AgentCashLog;
use App\Models\User;
use App\Models\Shop;

class AgentController extends AdminController
{


    public function cash_log($request, $response, $args){
        $table_config['total_column'] = array(
            'op' => '操作',
            'uid' => '申请代理',
            'sttime' => '申请时间',
            'account' => '打款支付宝',
            'money' => '提现金额',
            'service_fee' => '手续费',
            'make_money' => '最终到账',
            'status' => '状态',
            'endtime' => '更新时间');
        $table_config['default_show_column'] = array();
        foreach ($table_config['total_column'] as $column => $value) {
            $table_config['default_show_column'][] = $column;
        }
        $table_config['ajax_url'] = 'cash_log_ajax';
        return $this->view()
            ->assign('table_config', $table_config)
            ->display('admin/agent/cash_log.tpl');

    }

    public function cash_log_ajax($request, $response, $args){

        $datatables = new Datatables(new DatatablesHelper());
        $datatables->query("SELECT id,account,uid,money,service_fee,make_money,sttime,endtime,status FROM `agent_cash_log` ORDER BY `agent_cash_log`.`sttime` DESC");


        $datatables->edit('op', static function ($data) {

            return '<a class="btn btn-brand" onclick="look_money_code(\''.$data['id'].'\',\''.$data['uid'].'\',\''.AgentConfig::getPaymentCode($data['uid']).'\',\''.$data['make_money'].'\')">打款</a>
                    <a class="btn btn-brand-accent" onclick="update_status(\''.$data['id'].'\',\'0\')">等待</a>
                    <a class="btn btn-waning" onclick="update_status(\''.$data['id'].'\',\'2\')">拒绝</a>
                    ';
        });

        $datatables->edit('status', static function ($data) {
            if($data['status'] == 1) {
                $status = '成功';
            }elseif ($data['status'] == 0) {
                $status = '处理中';
            }elseif ($data['status'] == 2){
                $status = '已拒绝';
            }
            return $status;
        });

        $datatables->edit('uid', static function ($data) {
            return User::getUserEmail($data['uid']) . "#{$data['uid']}";
        });
        $datatables->edit('sttime', static function ($data) {
            return date("Y-m-d H:i:s",$data['sttime']);
        });
        $datatables->edit('endtime', static function ($data) {
            if($data['endtime']==0)return '--';
            return date("Y-m-d H:i:s",$data['endtime']);
        });

        $body = $response->getBody();
        $body->write($datatables->generate());
    }


    public function cash_log_edit($request, $response, $args){
        $result = AgentCashLog::where(['id' => $args['id']])->first();
        if(!$result){
            $rs['ret'] = 0;
            $rs['msg'] = '申请ID不存在。'. $args['id'];
            return $response->getBody()->write(json_encode($rs));
        }

        $result->status = $args['status'];
        $result->endtime= time();
        $result->save();

        $rs['ret'] = 1;
        $rs['msg'] = '操作成功。';
        return $response->getBody()->write(json_encode($rs));
}


    public function income($request, $response, $args){
        $table_config['total_column'] = array(
            'id' => 'ID',
            'user_id' => '用户ID',
            'type' => '类型',
            'type_id' => '商品名称',
            'money' => '支付金额',
            'income' => '收益金额',
            'remark' => '备注',
            'addtime' => '创建时间');
        $table_config['default_show_column'] = array();
        foreach ($table_config['total_column'] as $column => $value) {
            $table_config['default_show_column'][] = $column;
        }
        $table_config['ajax_url'] = 'income_ajax';
        return $this->view()
            ->assign('table_config', $table_config)
            ->display('admin/agent/income.tpl');

    }

    public function income_ajax($request, $response, $args){
        
        $datatables = new Datatables(new DatatablesHelper());
        $datatables->query("Select id,uid,money,user_id,type,type_id,income,remark,addtime from agent_income where uid={$this->user->id}");

        $datatables->edit('type_id', static function ($data) {
            if(empty($data['type']) || $data['type'] == 'shop' || $data['type'] == 'km') {
                $shop = Shop::where('id', '=', $data['type_id'])->first();
                return $shop['name'];
            }
            if($data['type'] == 'agent') {
                $shop = AgentLevel::where('id', '=', $data['type_id'])->first();
                return $shop['name'];
            }

            return '其他';
        });

        $datatables->edit('type', static function ($data) {
            if(empty($data['type']) || $data['type'] == 'shop')
                return '购买套餐';
            if($data['type'] == 'agent')
                return '开通/升级代理';
            if($data['type'] == 'km')
                return '生成卡密';
            return '其他';
        });


        $datatables->edit('addtime', static function ($data) {
            return date("Y-m-d H:i:s",$data['addtime']);
        });

        $body = $response->getBody();
        $body->write($datatables->generate());
    }


    /**
     * @Notes:
     * 提交提现金额
     * @Interface cash_post
     * @Author: 133814250@qq.com MengShuai
     * @Date: 2020/3/1   11:33
     * @param $request
     * @param $response
     * @param $args
     */
    public function cash_post($request, $response, $args){
        $money = $request->getParam('userEmail');
        $user = $this->user;

        if($money == ''){
            $rs['ret'] = 0;
            $rs['msg'] = '请输入提现金额。';
            return $response->getBody()->write(json_encode($rs));
        }
        if($money < Config::get('cash_min_money')){
            $rs['ret'] = 0;
            $rs['msg'] = '最低提现不能低于30元。';
            return $response->getBody()->write(json_encode($rs));
        }
        if (bccomp($user->money, $money, 2) == -1) {
            $rs['ret'] = 0;
            $rs['msg'] = '输入的提现金额超过了你的账户余额。';
            return $response->getBody()->write(json_encode($rs));
        }

        $agent = AgentConfig::where('uid','=',$user->id)->first();
        $cash = json_decode($agent->cash,true);
        if($cash['name'] == '' || $cash['account'] == '' || $cash['img'] == ''){
            $rs['ret'] = 0;
            $rs['msg'] = '请先到【站点设置】完善提现收款相关的支付宝信息。';
            return $response->getBody()->write(json_encode($rs));
        }
        //默认空收款码地址
        if($cash['img'] == 'http://bufanyun.cn-bj.ufileos.com/uploads_ml/20200225_ee8edecdb148d579db6cb0e9904e6362.png'){
            $rs['ret'] = 0;
            $rs['msg'] = '请先到【站点设置】上传支付宝收款码。';
            return $response->getBody()->write(json_encode($rs));
        }

        $exist = AgentCashLog::where(['uid'=> $user->id,'status'=>0])->first();
        if($exist != null){
            $rs['ret'] = 0;
            $rs['msg'] = '存在未结算记录，请在结算后再次申请。';
            return $response->getBody()->write(json_encode($rs));
        }

        //资金变动
        AgentMoneyLog::Newly($user->id,"-" . $money,
            $user->money,($user->money-$money),"申请提现：" . sprintf("%.2f",$money));

        $user->money -= $money;
        $user->save();

        $AgentCashLog = new AgentCashLog();
        $AgentCashLog->uid = $user->id;
        $AgentCashLog->account = $cash['account'];
        $AgentCashLog->money = $money;
        $AgentCashLog->service_fee = Config::get('cash_fee');
        $AgentCashLog->make_money = sprintf("%.2f",($money-Config::get('cash_fee')));
        $AgentCashLog->sttime = time();
        $AgentCashLog->endtime = 0;
        $AgentCashLog->status = 0;
        $AgentCashLog->save();

        $rs['ret'] = 1;
        $rs['msg'] = '提交成功。';
        return $response->getBody()->write(json_encode($rs));
    }

    public function cash($request, $response, $args){
        $table_config['total_column'] = array(
            'sttime' => '申请时间',
            'account' => '打款支付宝',
            'money' => '提现金额',
            'service_fee' => '手续费',
            'make_money' => '最终到账',
            'status' => '状态',
            'endtime' => '更新时间');
        $table_config['default_show_column'] = array();
        foreach ($table_config['total_column'] as $column => $value) {
            $table_config['default_show_column'][] = $column;
        }
        $table_config['ajax_url'] = 'cash_ajax';
        return $this->view()
            ->assign('table_config', $table_config)
            ->display('admin/agent/cash.tpl');

    }

    public function cash_ajax($request, $response, $args){

        $datatables = new Datatables(new DatatablesHelper());
        $datatables->query("Select account,uid,money,service_fee,make_money,sttime,endtime,status from agent_cash_log where uid={$this->user->id}");

        $datatables->edit('status', static function ($data) {
            if($data['status'] == 1) {
                $status = '成功';
            }elseif ($data['status'] == 0) {
                $status = '处理中';
            }elseif ($data['status'] == 2){
                $status = '已拒绝';
            }
            return $status;
        });

        $datatables->edit('sttime', static function ($data) {
            return date("Y-m-d H:i:s",$data['sttime']);
        });
        $datatables->edit('endtime', static function ($data) {
            if($data['endtime']==0)return '--';
            return date("Y-m-d H:i:s",$data['endtime']);
        });

        $body = $response->getBody();
        $body->write($datatables->generate());
    }



    public function type($request, $response, $args)
    {
        $table_config['total_column'] = array(
            'op' => '操作',
            'level' => '等级Lv',
            'name' => '名称',
            'price' => '在线开通价格',
            'status' => '显示状态');
        $table_config['default_show_column'] = array();
        foreach ($table_config['total_column'] as $column => $value) {
            $table_config['default_show_column'][] = $column;
        }
        $table_config['ajax_url'] = 'type_ajax';
        return $this->view()
            ->assign('table_config', $table_config)
            ->display('admin/agent/type.tpl');
    }


    public function type_ajax($request, $response, $args)
    {
        $datatables = new Datatables(new DatatablesHelper());
        $datatables->query("Select id,uid,level,name,price,status from agent_level where uid={$this->user->id}");

        $datatables->edit('status', static function ($data) {
            return $data['status'] == 1 ? '启用' : '隐藏';
        });

        $datatables->edit('op', static function ($data) {
            return '<a class="btn btn-brand" href="/admin/agent/' . $data['id'] . '/type_edit">编辑</a>
                    <a class="btn btn-brand-accent" id="delete">删除</a>';
        });

        $body = $response->getBody();
        $body->write($datatables->generate());
    }


    public function type_edit($request, $response, $args)
    {
        $id = $args['id'];
        $where['id'] = $id;

        $info = AgentLevel::where('uid','=',$this->user->id)
            ->where('id','=',$id)
            ->first();

        return $this->view()
            ->assign('info', $info)
            ->display('admin/agent/type_edit.tpl');
    }


    public function type_edit_post($request, $response, $args){
        $id = $request->getParam('id');

        $info = AgentLevel::where('uid','=',$this->user->id)
            ->where('id','=',$id)
            ->update([
                'price' => $request->getParam('price'),
                'name' => $request->getParam('name'),
                'status' => $request->getParam('status'),
            ]);
        if($info){
            $rs['ret'] = 1;
            $rs['msg'] = '修改成功。';
            return $response->getBody()->write(json_encode($rs));
        }
        $rs['ret'] = 0;
        $rs['msg'] = '修改失败。';
        return $response->getBody()->write(json_encode($rs));
    }



    public function lists($request, $response, $args)
    {
        $table_config['total_column'] = array(
            'op'      => '操作',
            'id'      => '代理ID',
            'name'    => '用户名',
            'email'    => '邮箱',
            'level'    => '等级',
            'invite_num' => '下级用户',
            'domain'  => '绑定域名',
            'addtime' => '开通时间',
            'endtime' => '到期时间',
            'status'  => '代理面板',
        );
        $table_config['default_show_column'] = array();
        foreach ($table_config['total_column'] as $column => $value) {
            $table_config['default_show_column'][] = $column;
        }
        $table_config['ajax_url'] = 'lists_ajax';

        return $this->view()
            ->assign('table_config', $table_config)
            ->display('admin/agent/lists.tpl');
    }

    public function lists_ajax($request, $response, $args)
    {

        $sql = "SELECT 
                u.id as op, u.id, u.user_name as name, u.email,
                l.name as level, 
                c.domain, c.addtime, c.endtime, c.status  
                FROM agent_config c, agent_level l, user u 
                WHERE c.level = l.id AND c.uid = u.id";
        $sql .= ($this->AdminLevel !=1) ? " and u.ref_by=" . $this->user->id : "";

//        $sql .= " ORDER BY c.id ASC";  //没作用
        $datatables = new Datatables(new DatatablesHelper());
        $datatables->query("$sql");

        $datatables->edit('op', static function ($data) {
            return '<a class="btn btn-brand" href="/admin/agent/' . $data['id'] . '/lists_edit">编辑</a>
                    <a class="btn btn-brand-accent" id="delete" value="' . $data['id'] . '" href="javascript:void(0);" onClick="delete_modal_show(\'' . $data['id'] . '\')">删除</a>';
        });

        $datatables->edit('addtime', static function ($data) {
            return date("Y-m-d",$data['addtime']);
        });

        $datatables->edit('endtime', static function ($data) {
            return date("Y-m-d",$data['endtime']);
        });

        $datatables->edit('status', static function ($data) {
            return $data['status'] == 1 ? '已开启' : '已关闭';
        });

        $datatables->edit('invite_num', static function ($data) {
            return $data['invite_num'] = User::where('ref_by','=',$data['id'])->count() . '人';
        });

        $body = $response->getBody();
        $body->write($datatables->generate());
    }


    public function lists_create($request, $response, $args)
    {
        $levels = AgentLevel::where('uid','=',$this->user->id)
            ->where('status','=',1)
            ->get();
        $level = '';
        foreach ($levels as $k => $v){
            $level .= "<option value=\"{$v['id']}\">{$v['name']}</option>";
        }

        return $this->view()
            ->assign('level', $level)
            ->display('admin/agent/add.tpl');
    }

    public function lists_add($request, $response, $args)
    {
        $level = $request->getParam('level');
        $id = $request->getParam('id');

        $levels = AgentLevel::where('uid','=',$this->user->id)
            ->where('id','=',$level)
            ->where('status','=',1)
            ->first();
        if(!$levels){
            $rs['ret'] = 0;
            $rs['msg'] = '等级不存在或已禁用。';
            return $response->getBody()->write(json_encode($rs));
        }

        $where['id'] = $id;
        if(($this->AdminLevel !=1)){
            $where['ref_by'] = $this->user->id;
        }

        $user = User::where($where)->first();
        if($user == null){
            $rs['ret'] = 0;
            $rs['msg'] = '用户ID错误或不再你的归属下。';
            return $response->getBody()->write(json_encode($rs));
        }
        if($user['is_admin'] != 0){
            $rs['ret'] = 0;
            $rs['msg'] = '用户ID:' . $id . '已经是代理了。';
            return $response->getBody()->write(json_encode($rs));
        }
        if($user['enable'] != 1){
            $rs['ret'] = 0;
            $rs['msg'] = '用户ID:' . $id . '已被禁用，请先启用用户登录。';
            return $response->getBody()->write(json_encode($rs));
        }
        $user->is_admin = 1;
        $result = $user->save();
        if(!$result){
            $rs['ret'] = 0;
            $rs['msg'] = '添加失败';
            return $response->getBody()->write(json_encode($rs));
        }

        $callback = AgentConfig::init_agent($id,$levels);
        if($callback == 1) {
            $rs['ret'] = 1;
            $rs['msg'] = '添加成功';
        }elseif ($callback == 0) {
            $rs['ret'] = 0;
            $rs['msg'] = '添加失败';
        }elseif ($callback == 1001){
            $rs['ret'] = 0;
            $rs['msg'] = '用户ID:' . $id . '已存在代理数据，若无法登陆请联系管理员。';
        }
        return $response->getBody()->write(json_encode($rs));
    }


    public function lists_edit($request, $response, $args)
    {
        $id = $args['id'];
        $where['id'] = $id;
        if(($this->AdminLevel !=1)){
            $where['ref_by'] = $this->user->id;
        }
        $user = User::where($where)->first();
        if($user == null){
            $rs['ret'] = 0;
            $rs['msg'] = '用户ID错误或不再你的归属下。';
        return $response->getBody()->write(json_encode($rs));
        }

        $info = AgentConfig::where('uid','=',$id)->first();
        $info->enable = $user->enable;
        $info->remark = $user->remark;
        $levels = AgentLevel::where('uid','=',$this->user->id)
            ->where('status','=',1)
            ->get();
        $level = '';
        foreach ($levels as $k => $v){
            $selected = ($info->level==$v['id']) ? 'selected' : '';
            $level .= "<option value=\"{$v['id']}\" {$selected}>{$v['name']}</option>";
        }
        return $this->view()
            ->assign('info', $info)
            ->assign('level', $level)
            ->display('admin/agent/edit.tpl');
    }


    public function lists_edit_post($request, $response, $args){
        $uid = $request->getParam('uid');

        $where['id'] = $uid;
        if(($this->AdminLevel !=1)){
            $where['ref_by'] = $this->user->id;
        }

        $user = User::where($where)->first();
        if($user == null){
            $rs['ret'] = 0;
            $rs['msg'] = '用户ID错误或不再你的归属下。';
            return $response->getBody()->write(json_encode($rs));
        }

        $agent = AgentConfig::where('uid','=',$uid)->first();
        if($agent == null){
            $rs['ret'] = 0;
            $rs['msg'] = '代理身份异常 请联系管理员。';
            return $response->getBody()->write(json_encode($rs));
        }

        $user->remark = $request->getParam('remark');
        $user->enable = $request->getParam('enable');
        $result1 = $user->save();

        $agent->status = $request->getParam('status');
        $agent->level = $request->getParam('level');
        $result2 = $agent->save();
        if($result1 && $result2){
            $rs['ret'] = 1;
            $rs['msg'] = '修改成功。';
            return $response->getBody()->write(json_encode($rs));
        }
        $rs['ret'] = 0;
        $rs['msg'] = '修改失败。';
        return $response->getBody()->write(json_encode($rs));
    }




    public function edit($request, $response, $args)
    {
        $id = $args['id'];
        $rule = DetectRule::find($id);
        return $this->view()->assign('rule', $rule)->display('admin/detect/edit.tpl');
    }

    public function update($request, $response, $args)
    {
        $id = $args['id'];
        $rule = DetectRule::find($id);

        $rule->name = $request->getParam('name');
        $rule->text = $request->getParam('text');
        $rule->regex = $request->getParam('regex');
        $rule->type = $request->getParam('type');

        if (!$rule->save()) {
            $rs['ret'] = 0;
            $rs['msg'] = '修改失败';
            return $response->getBody()->write(json_encode($rs));
        }

        Telegram::SendMarkdown('规则更新：' . PHP_EOL . $request->getParam('name'));

        $rs['ret'] = 1;
        $rs['msg'] = '修改成功';
        return $response->getBody()->write(json_encode($rs));
    }


    public function delete($request, $response, $args)
    {
        $rs['ret'] = 0;
        $rs['msg'] = '删除失败，请在【编辑中禁用】';
        return $response->getBody()->write(json_encode($rs));


        $id = $request->getParam('id');
        $rule = DetectRule::find($id);
        if (!$rule->delete()) {
            $rs['ret'] = 0;
            $rs['msg'] = '删除失败';
            return $response->getBody()->write(json_encode($rs));
        }
        $rs['ret'] = 1;
        $rs['msg'] = '删除成功';
        return $response->getBody()->write(json_encode($rs));
    }


}
