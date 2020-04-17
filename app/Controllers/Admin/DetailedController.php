<?php

namespace App\Controllers\Admin;

use App\Models\Ann;
use App\Models\AgentMoneyLog;
use App\Controllers\AdminController;
use App\Services\Config;
use App\Services\Mail;
use App\Models\User;
use Exception;
use Ozdemir\Datatables\Datatables;
use App\Utils\DatatablesHelper;
use App\Utils\QQWry;
use App\Utils\Telegram;
use App\Services\BtApi\Bt;

class DetailedController extends AdminController
{
    public function index($request, $response, $args)
    {

//        AgentMoneyLog::Newly2($uid=1,$money=1,$before=999,1000,$memo='测试');

        $table_config['total_column'] = array( 'id' => 'ID','uid' => '代理ID','memo' => '备注',
            'money' => '变动金额', 'befores' => '变动前', 'after' => '变动后',
            'ip' => 'IP', 'region' => '归属地', 'addtime' => '创建时间','useragent' => 'User-Agent');
        $table_config['default_show_column'] = array('uid','memo',
            'money', 'befores','after','addtime');
        if ($this->AdminLevel !=1){
            unset($table_config['total_column']['uid'], $table_config['total_column']['useragent']);
            unset($table_config['default_show_column'][0]);
        }
        $table_config['ajax_url'] = 'detailed/ajax';
        return $this->view()
            ->assign('table_config', $table_config)
            ->display('admin/detailed/index.tpl');
    }

    public function ajax($request, $response, $args)
    {
        $where = '';
        if ($this->AdminLevel !=1){
            $where = "WHERE `uid` = {$this->user->id} ";
        }
        $datatables = new Datatables(new DatatablesHelper());
        $datatables->query("SELECT id,uid,money,befores,after,ip,useragent,addtime,memo 
                            FROM `agent_money_log` {$where} ORDER BY `agent_money_log`.`id` ASC");

        $datatables->edit('region', static function ($data) {
            $iplocation = new QQWry();
            $location = $iplocation->getlocation($data['ip']);
            return iconv('gbk', 'utf-8//IGNORE', $location['country'] . $location['area']);
        });

        $datatables->edit('addtime', static function ($data) {
            return date("Y-m-d H:i:s",$data['addtime']);
        });

        $body = $response->getBody();
        $body->write($datatables->generate());
    }

}
