<?php

namespace App\Models;

use App\Models\AgentMoneyLog;

/**
 * AgentLevel Model
 */
class AgentIncome extends Model
{
    protected $connection = 'default';
    protected $table = 'agent_income';


    /**
     * @Notes:
     * 生成卡密返佣
     * @Interface Kmincome
     * @Author: 133814250@qq.com MengShuai
     * @Date: 2020/3/2   22:08
     * @param $uid
     * @param $type_id
     * @param $count
     * @param $price
     * @param string $remark
     * @return bool
     */
    public static function Kmincome($uid,$type_id,$count,$price,$remark='生成卡密'){
        $boss_id = User::where(['id' =>$uid])->value('ref_by');
        if($boss_id == 0)$boss_id=1;
        if($boss_id == false) return false;
        if($boss_id > 1) {
            //计算上级价格
            $boss_cost = AgentConfig::getAgentCost($boss_id);
            $boss_price = bcmul($boss_cost['id' . $type_id], $count, 2);
            if (bccomp($price, $boss_price, 2) == 1) {
                $boss = User::where(['id' => $boss_id])->first();
                $income = sprintf("%.2f",bcsub($price, $boss_price, $scale = '2'));
                $old_money = $boss->money;
                $boss->money += $income;
                $boss->save();
            } else {
                return false;
            }
        }else{
            $income = sprintf("%.2f",$price);
            $boss = User::where(['id' => $boss_id])->first();
            $old_money = $boss->money;
            $boss->money += $income;
            $boss->save();
        }

        //插入收益记录
        $AgentIncome = new AgentIncome();
        $AgentIncome->uid       = $boss_id;
        $AgentIncome->user_id   = $uid;
        $AgentIncome->type      = 'km';
        $AgentIncome->type_id   = $type_id;
        $AgentIncome->money     = $price;
        $AgentIncome->income    = $income;
        $AgentIncome->addtime   = time();
        $AgentIncome->remark    = $remark;
        $AgentIncome->save();

        //上级资金变动
        AgentMoneyLog::Newly($boss_id,$income,$old_money,$boss->money,"代理(#{$uid})生成卡密：{$remark}");
        if($boss_id > 1){
            return self::Kmincome($boss_id,$type_id,$count,$boss_price,$remark='下级生成卡密X' . $count);
        }
        return false;
    }

}
