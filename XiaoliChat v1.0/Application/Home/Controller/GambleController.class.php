<?php

namespace Home\Controller;

use Think\Controller;

// 赌博控制器
class GambleController extends Controller {
    
    // 赌博
    public function gamble($username, $gold) {
        // 先判断该用户在User表中是否存在
        $users = M('User') -> where("username='%s'", $username) -> select();
        if(empty($users)) { //用户在用户表中不存在
            return '您还没有设置昵称呢，赶紧发送“昵称 您希望的昵称”来设置昵称吧！';
        } else { //用户在用户表中存在
            $current_gold = intval($users[0]['gold']);
            $gold = intval($gold);
            if($gold>$current_gold) {
                return '抱歉您的金币不足，赌博失败！';
            }
            if(rand(1,11) < 5) {
                $data['gold'] = $current_gold - $gold;
                M('User') -> where("username='%s'", $username) -> save($data);
                return '赌博失败，您损失了'.$gold.'个金币';
            } else {
                $data['gold'] = $current_gold + $gold;
                M('User') -> where("username='%s'", $username) -> save($data);
                return '赌博成功，您获得了'.$gold.'个金币';
            }
        }
    }
}