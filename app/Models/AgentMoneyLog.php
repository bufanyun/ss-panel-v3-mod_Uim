<?php

namespace App\Models;

use App\Models\User;

/**
 * AgentLevel Model
 */
class AgentMoneyLog extends Model
{
    protected $connection = 'default';
    protected $table = 'agent_money_log';


    /**
     * @Notes:
     * 新增余额明细
     * @Interface Newly
     * @Author: 133814250@qq.com MengShuai
     * @Date: 2020/3/11   17:54
     */
    public static function Newly($uid,$money,$before,$after,$memo){
        return self::insert([
            'uid'    => $uid,
            'money'  => $money,
            'befores' => $before,
            'after'  => $after,
            'memo'   => $memo,
            'ip'     => $_SERVER['REMOTE_ADDR'],
            'useragent' => $_SERVER['HTTP_USER_AGENT'],
            'addtime' => time(),
        ]);
    }


    public static function Newly2($uid,$money,$before,$after,$memo){
        //监控合法性
        self::inspect($uid,$before);

        return self::insert([
            'uid'    => $uid,
            'money'  => $money,
            'befores' => $before,
            'after'  => $after,
            'memo'   => $memo,
            'ip'     => $_SERVER['REMOTE_ADDR'],
            'useragent' => $_SERVER['HTTP_USER_AGENT'],
            'addtime' => time(),
        ]);
    }

    /**
     * @Notes:
     * 检查合法性
     * @Interface inspect
     * @Author: 133814250@qq.com MengShuai
     * @Date: 2020/3/11   22:43
     */
    public static function inspect($uid,$before){
        $info = self::where(['uid' => $uid])->orderBy('id', 'desc')->first();
        if($info->after != $before){
            /** 异常处理 */

        }

        return true;
    }
}
