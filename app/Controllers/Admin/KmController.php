<?php

namespace App\Controllers\Admin;

use App\Models\Shop;
use App\Models\Bought;
use App\Models\AgentConfig;
use App\Models\AgentLevel;
use App\Models\ErrorLog;
use App\Models\Km;
use App\Models\AgentIncome;
use App\Models\AgentMoneyLog;
use App\Controllers\AdminController;
use App\Services\Config;
use App\Utils\Tools;
use Ozdemir\Datatables\Datatables;
use App\Utils\DatatablesHelper;

class KmController extends AdminController
{

    public function log($request, $response, $args)
    {
        $table_config['total_column'] = array('op' => '操作',
            'addtime' => '添加时间',
            'type_id' => '套餐名称','batch' => '批量编号',
            'nums' => '生成数量',
        );
        $table_config['default_show_column'] = array();
        foreach ($table_config['total_column'] as $column => $value) {
            $table_config['default_show_column'][] = $column;
        }
        $table_config['ajax_url'] = '/admin/km/log_ajax';

        return $this->view()
            ->assign('table_config', $table_config)
            ->display('admin/km/log.tpl');
    }


    public function log_ajax($request, $response, $args)
    {
        $datatables = new Datatables(new DatatablesHelper());
        $datatables->query("SELECT type_id,batch,addtime FROM `agent_km` 
                                    where `uid`= '{$this->user->id}' 
                                    GROUP BY `batch` 
                                    ORDER BY `agent_km`.`id` DESC");

        $datatables->edit('op', static function ($data){
            return "<a class=\"btn btn-brand\" href=\"/admin/km/{$data['batch']}/txt\">导出生成卡密</a>";
        });

        $datatables->edit('type_id', static function ($data) {
            $shop = Shop::find($data['type_id']);
            return explode('（',$shop->name)[0];
        });

        $datatables->edit('nums', static function ($data) {
            return km::where('batch',$data['batch'])->count();
        });

        $datatables->edit('addtime', static function ($data) {
            return ($data['addtime']==0) ? '--' :  date("Y-m-d H:i:s",$data['addtime']);
        });
        $body = $response->getBody();
        $body->write($datatables->generate());
    }

    public function log_txt($request, $response, $args){
        $batch = $args['batch'];
        $lists = Km::where(['uid' => $this->user->id,'batch' => $batch])
            ->orderBy('id', 'desc')
            ->get();
        if(empty($lists[0])){
            $rs['ret'] = 0;
            $rs['msg'] = '非法请求。';
            return $response->getBody()->write(json_encode($rs));
        }
        $type = explode('（',Shop::where(['id'=>$lists[0]['type_id']])->value('name'))[0];
        $nums = Km::where(['uid' => $this->user->id,'batch' => $batch])->count();
        //设置文件名称
        $filename = $type . "共{$nums}张--" . $batch;
        $content = "导出时间：" . date("Y-m-d H:i:s",time()) ."，以下是卡密信息 \r\n\r\n";
        foreach ($lists as $item) {
            $content .= $item['km']."\r\n";
        }
        Header("Content-type:application/octet-stream");
        Header("Accept-Ranges:bytes");
        header("Content-Disposition:attachment;filename={$filename}.txt");
        header("Expires:0");
        header("Cache-Control:must-revalidate,post-check=0,pre-check=0 ");
        header("Pragma:public");
        return $content;
    }


    public function create($request, $response, $args)
    {

        $count = $request->getParam('count');
        $type_id = $request->getParam('type_id');
        $user = $this->user;
        if($count < 0 || $count ==''){
            $rs['ret'] = 0;
            $rs['msg'] = '请输入生成数量。';
            return $response->getBody()->write(json_encode($rs));
        }

        if($count > 200){
            $rs['ret'] = 0;
            $rs['msg'] = '单次最多生成200张。';
            return $response->getBody()->write(json_encode($rs));
        }
        $shop = Shop::where('id',$type_id)->first();
        if($shop == null){
            $rs['ret'] = 0;
            $rs['msg'] = '非法请求-shop。';
            return $response->getBody()->write(json_encode($rs));
        }

        $cost = AgentConfig::getAgentCost($user->id)["id{$type_id}"];
        $need_money = bcmul($cost,$count,2);

        if (bccomp($user->money,$need_money, 2) == -1) {
            $rs['ret'] = 0;
            $res['msg'] = '余额不足，请先充值。</br></br><a href="/user/code">点击进入充值界面</a>';
            return $response->getBody()->write(json_encode($res));
        }

        //资金变动
        AgentMoneyLog::Newly($user->id,"-{$need_money}",$user->money,($user->money-$need_money),'生成卡密X' . $count);

        //扣费
        $user->money -= $need_money;
        $user->save();

        //生成
        $time = time();
        $batch = date("mdHis",$time) . Tools::genRandomChar();
        $data = [];
        for($i=1;$i<=$count;$i++){
            array_push($data,[
                'uid' => $user->id,
                'type_id' => $type_id,
                'km' => $shop->prefix . '-' . $user->id . '-' . Tools::genRandomChar(16),
                'batch' => $batch,
                'addtime' => $time,
            ]);
        }
        Km::insert($data);

        //返佣
        if($user->id > 1) {
            AgentIncome::Kmincome($user->id, $type_id, $count, $need_money, $remark = '生成卡密X' . $count);
        }
        $rs['ret'] = 1;
        $rs['msg'] = '生成成功。';
        return $response->getBody()->write(json_encode($rs));
    }

    public function index($request, $response, $args)
    {
        $table_config['total_column'] = array('op' => '操作', 'id' => 'ID', 'km' => ' 卡密',
            'type_id' => '套餐名称','batch' => '批量编号',
            'status' => '状态', 'user_id' => '使用者',
            'endtime' => '使用时间', 'addtime' => '添加时间'
        );
        $table_config['default_show_column'] = array();
        foreach ($table_config['total_column'] as $column => $value) {
            $table_config['default_show_column'][] = $column;
        }
        $table_config['ajax_url'] = 'km/ajax';

        $shops = Shop::where('status',1)->get();
        $AgentCost = AgentConfig::getAgentCost($this->user->id);
        $type_list = '';
        foreach ($shops as $k => $v){
            $cost = $AgentCost["id{$v['id']}"];
            $name = explode('（',$v['name'])[0] . '  ￥' . $cost;
            $type_list .= " <option value =\"{$v['id']}\" data-cost=\"{$cost}\">{$name}</option>";
        }
        return $this->view()
            ->assign('table_config', $table_config)
            ->assign('type_list', $type_list)
            ->display('admin/km/index.tpl');
    }


    public function ajax($request, $response, $args)
    {
        $datatables = new Datatables(new DatatablesHelper());
        $datatables->query("Select id,km,type_id,batch,status,user_id,endtime,addtime
                                   from agent_km 
                                   where `uid`= '{$this->user->id}' 
                                   ORDER BY `agent_km`.`id` DESC");

        $datatables->edit('op', static function ($data){
            return "<a class=\"btn btn-brand\" href=\"javascript:void(0);\" onclick=\"upstatus('{$data['id']}')\">冻结</a>";
        });

        $datatables->edit('type_id', static function ($data) {
            $shop = Shop::find($data['type_id']);
            return explode('（',$shop->name)[0];
        });

        $datatables->edit('status', static function ($data) {
            return self::getStatus($data['status']);
        });

        $datatables->edit('user_id', static function ($data) {
            return (empty($data['user_id'])) ? '--' :  $data['user_id'];
        });

        $datatables->edit('endtime', static function ($data) {
            return ($data['endtime']==0) ? '--' :  date("Y-m-d H:i:s",$data['endtime']);
        });

        $datatables->edit('addtime', static function ($data) {
            return ($data['addtime']==0) ? '--' :  date("Y-m-d H:i:s",$data['addtime']);
        });
        $body = $response->getBody();
        $body->write($datatables->generate());
    }


    public static function getStatus($s){
        if($s == '0') {
            return '未使用';
        }elseif ($s == '1') {
            return '已激活';
        }elseif ($s == '2'){
            return '已冻结';
        }
        return '未知状态';
    }




    public function add($request, $response, $args)
    {
        if ($this->AdminLevel !=1){
            ErrorLog::insert([
                'uid' => $this->user->id,
                'conent' => '操作了添加商品:' . $request->getParam('name'),
                'ip' => $_SERVER['REMOTE_ADDR'],
                'addtime' => date("Y-m-d H:i:s",time()),
            ]);
            $rs['ret'] = 0;
            $rs['msg'] = '添加失败，你没有访问权限。';
            return $response->getBody()->write(json_encode($rs));
        }
        $shop = new Shop();
        $shop->name = $request->getParam('name');
        $shop->price = $request->getParam('price');
        $shop->auto_renew = $request->getParam('auto_renew');
        $shop->auto_reset_bandwidth = $request->getParam('auto_reset_bandwidth');

        $content = array();
        if ($request->getParam('bandwidth') != 0) {
            $content['bandwidth'] = $request->getParam('bandwidth');
        }

        if ($request->getParam('expire') != 0) {
            $content['expire'] = $request->getParam('expire');
        }

        if ($request->getParam('class') != 0) {
            $content['class'] = $request->getParam('class');
        }

        if ($request->getParam('class_expire') != 0) {
            $content['class_expire'] = $request->getParam('class_expire');
        }

        if ($request->getParam('reset') != 0) {
            $content['reset'] = $request->getParam('reset');
        }

        if ($request->getParam('reset_value') != 0) {
            $content['reset_value'] = $request->getParam('reset_value');
        }

        if ($request->getParam('reset_exp') != 0) {
            $content['reset_exp'] = $request->getParam('reset_exp');
        }

        //if ($request->getParam('speedlimit')!=0) {
        $content['speedlimit'] = $request->getParam('speedlimit');
        //}

        //if ($request->getParam('connector')!=0) {
        $content['connector'] = $request->getParam('connector');
        //}

        if ($request->getParam('content_extra') != '') {
            $content['content_extra'] = $request->getParam('content_extra');
        }

        $shop->content = json_encode($content);


        if (!$shop->save()) {
            $rs['ret'] = 0;
            $rs['msg'] = '添加失败';
            return $response->getBody()->write(json_encode($rs));
        }
        $rs['ret'] = 1;
        $rs['msg'] = '添加成功';
        return $response->getBody()->write(json_encode($rs));
    }

    public function edit($request, $response, $args)
    {
        $id = $args['id'];
        $shop = Shop::find($id);

        if($this->AdminLevel==1){
            return $this->view()
                ->assign('shop', $shop)
                ->display('admin/shop/edit.tpl');
        }
        $agent      = AgentConfig::where('uid','=',$this->user->id)->first();
        $shop->cost = AgentConfig::getCost($agent->level,$shop->id)['cost'];

        if($shop->cost < 0.1){
            //商品价格异常
            return header("Location: /403");
            exit;
        }
        $s = json_decode($agent->shop,true);
        $shop->p1   = $s["id{$shop->id}"]["p1"];
        $shop->p2   = $s["id{$shop->id}"]["p2"];
        $shop->p3   = $s["id{$shop->id}"]["p3"];
        $shop->p4   = $s["id{$shop->id}"]["p4"];

        return $this->view()
            ->assign('shop', $shop)
            ->display('admin/shop/agent_edit.tpl');
    }

    public function update($request, $response, $args)
    {

        //下级代理修改定价
        if($this->AdminLevel!=1){
            $id = $args['id'];
            $p1 = (float)$request->getParam('p1');
            $p2 = (float)$request->getParam('p2');
            $p3 = (float)$request->getParam('p3');
            $p4 = (float)$request->getParam('p4');
            $agent = AgentConfig::where('uid','=',$this->user->id)->first();
            $cost  = AgentConfig::getCost($agent->level,$id)['cost'];
            $s     = json_decode($agent->shop,true);
            $cost110 = $cost*1.1;
            if($p1 < $cost110) {
                $rs['ret'] = 0;
                $rs['msg'] = '商店价格不能低于成本价的110%('.$cost110.')';
                return $response->getBody()->write(json_encode($rs));
            }elseif ($p2 < $cost110){
                $name = AgentLevel::where('uid','=',$this->user->id)->where('level','=',2)->value('name');
                $rs['ret'] = 0;
                $rs['msg'] = "{$name}价格不能低于成本价的110%({$cost110})";
                return $response->getBody()->write(json_encode($rs));
            }elseif ( $p3 < $cost110){
                $name = AgentLevel::where('uid','=',$this->user->id)->where('level','=',3)->value('name');
                $rs['ret'] = 0;
                $rs['msg'] = "{$name}价格不能低于成本价的110%({$cost110})";
                return $response->getBody()->write(json_encode($rs));
            }elseif ($p4 < $cost110){
                $name = AgentLevel::where('uid','=',$this->user->id)->where('level','=',4)->value('name');
                $rs['ret'] = 0;
                $rs['msg'] = "{$name}价格不能低于成本价的110%({$cost110})";
                return $response->getBody()->write(json_encode($rs));
            }

            $s["id{$id}"]["p1"] = sprintf("%.2f",$p1);
            $s["id{$id}"]["p2"] = sprintf("%.2f",$p2);
            $s["id{$id}"]["p3"] = sprintf("%.2f",$p3);
            $s["id{$id}"]["p4"] = sprintf("%.2f",$p4);
            $agent->shop = json_encode($s);
            $result = $agent->save();

            if(!$result){
                $rs['ret'] = 0;
                $rs['msg'] = '保存失败';
                return $response->getBody()->write(json_encode($rs));
            }
            $rs['ret'] = 1;
            $rs['msg'] = '保存成功';
            return $response->getBody()->write(json_encode($rs));
            exit;
        }
        $id = $args['id'];
        $shop = Shop::find($id);
        $shop->name = $request->getParam('name');
        $shop->price = $request->getParam('price');
        $shop->auto_renew = $request->getParam('auto_renew');

        if ($shop->auto_reset_bandwidth == 1 && $request->getParam('auto_reset_bandwidth') == 0) {
            $boughts = Bought::where('shopid', $id)->get();

            foreach ($boughts as $bought) {
                $bought->renew = 0;
                $bought->save();
            }
        }

        $shop->auto_reset_bandwidth = $request->getParam('auto_reset_bandwidth');
        $shop->status = 1;

        $content = array();
        if ($request->getParam('bandwidth') != 0) {
            $content['bandwidth'] = $request->getParam('bandwidth');
        }

        if ($request->getParam('expire') != 0) {
            $content['expire'] = $request->getParam('expire');
        }

        if ($request->getParam('class') != 0) {
            $content['class'] = $request->getParam('class');
        }

        if ($request->getParam('class_expire') != 0) {
            $content['class_expire'] = $request->getParam('class_expire');
        }

        if ($request->getParam('reset') != 0) {
            $content['reset'] = $request->getParam('reset');
        }

        if ($request->getParam('reset_value') != 0) {
            $content['reset_value'] = $request->getParam('reset_value');
        }

        if ($request->getParam('reset_exp') != 0) {
            $content['reset_exp'] = $request->getParam('reset_exp');
        }

        //if ($request->getParam('speedlimit')!=0) {
        $content['speedlimit'] = $request->getParam('speedlimit');
        //}

        //if ($request->getParam('connector')!=0) {
        $content['connector'] = $request->getParam('connector');
        //}

        if ($request->getParam('content_extra') != '') {
            $content['content_extra'] = $request->getParam('content_extra');
        }

        $shop->content = json_encode($content);

        if (!$shop->save()) {
            $rs['ret'] = 0;
            $rs['msg'] = '保存失败';
            return $response->getBody()->write(json_encode($rs));
        }
        $rs['ret'] = 1;
        $rs['msg'] = '保存成功';
        return $response->getBody()->write(json_encode($rs));
    }


    public function deleteGet($request, $response, $args)
    {
        $rs['ret'] = 0;
        $rs['msg'] = '下架失败 暂时不支持哦';
        return $response->getBody()->write(json_encode($rs));

        $id = $request->getParam('id');
        $shop = Shop::find($id);
        $shop->status = 0;
        if (!$shop->save()) {
            $rs['ret'] = 0;
            $rs['msg'] = '下架失败';
            return $response->getBody()->write(json_encode($rs));
        }

        $boughts = Bought::where('shopid', $id)->get();

        foreach ($boughts as $bought) {
            $bought->renew = 0;
            $bought->save();
        }

        $rs['ret'] = 1;
        $rs['msg'] = '下架成功';
        return $response->getBody()->write(json_encode($rs));
    }

    public function bought($request, $response, $args)
    {
        $table_config['total_column'] = array('op' => '操作', 'id' => 'ID',
            'datetime' => '购买日期', 'content' => '内容',
            'price' => '价格', 'user_id' => '用户ID',
            'user_name' => '用户名', 'renew' => '自动续费时间',
            'auto_reset_bandwidth' => '续费时是否重置流量');
        $table_config['default_show_column'] = array();
        foreach ($table_config['total_column'] as $column => $value) {
            $table_config['default_show_column'][] = $column;
        }
        $table_config['ajax_url'] = 'bought/ajax';
        return $this->view()->assign('table_config', $table_config)->display('admin/shop/bought.tpl');
    }

    public function deleteBoughtGet($request, $response, $args)
    {
        $id = $request->getParam('id');
        $shop = Bought::find($id);
        $shop->renew = 0;
        if (!$shop->save()) {
            $rs['ret'] = 0;
            $rs['msg'] = '退订失败';
            return $response->getBody()->write(json_encode($rs));
        }
        $rs['ret'] = 1;
        $rs['msg'] = '退订成功';
        return $response->getBody()->write(json_encode($rs));
    }



    public function ajax_bought($request, $response, $args)
    {
        $datatables = new Datatables(new DatatablesHelper());
        $datatables->query('Select bought.id as op,bought.id as id,bought.datetime,shop.id as content,bought.price,user.id as user_id,user.user_name,renew,shop.auto_reset_bandwidth from bought,user,shop where bought.shopid = shop.id and bought.userid = user.id');

        $datatables->edit('op', static function ($data) {
            return '<a class="btn btn-brand-accent" ' . ($data['renew'] == 0 ? 'disabled' : ' id="row_delete_' . $data['id'] . '" href="javascript:void(0);" onClick="delete_modal_show(\'' . $data['id'] . '\')"') . '>中止</a>';
        });

        $datatables->edit('content', static function ($data) {
            $shop = Shop::find($data['content']);
            return $shop->content();
        });

        $datatables->edit('renew', static function ($data) {
            if ($data['renew'] == 0) {
                return '不自动续费';
            }

            return date('Y-m-d H:i:s', $data['renew']) . ' 续费';
        });

        $datatables->edit('auto_reset_bandwidth', static function ($data) {
            return $data['auto_reset_bandwidth'] == 0 ? '不自动重置' : '自动重置';
        });

        $datatables->edit('datetime', static function ($data) {
            return date('Y-m-d H:i:s', $data['datetime']);
        });

        $body = $response->getBody();
        $body->write($datatables->generate());
    }
}
