<?php

namespace Home\Controller;

use Think\Controller;

// 设置用户昵称、用户昵称修改、用户经验增加、用户金币增减
class UserController extends Controller {
    
    // 设置用户昵称
    public function setNickname($username, $nickname){
        
        // 先判断用户设置的昵称是否为空
        if($nickname == '') {
            return '用户昵称不允许为空！';
        }
        
        // 然后判断该昵称是否已经存在
        $users1 = M('User') -> where("username<>'%s' and nickname='%s'", $username, $nickname) -> select();
        if(!empty($users1)) {
            return '该昵称已经存在，设置失败！';
        }
        
        // 然后判断该用户在User表中是否存在
        $users2 = M('User') -> where("username='%s'", $username) -> select();
        if(empty($users2)) { //用户在用户表中不存在
            $data['username'] = $username;
            $data['nickname'] = $nickname;
            M('User') -> data($data) -> add();
        } else { //用户在用户表中存在
            $data['nickname'] = $nickname;
            M('User') -> where("username='%s'", $username) -> save($data);
        }
        return '您的昵称已经成功修改为'.$nickname;
    }
    
    // 获取用户昵称
    public function getNickname($username) {
        $users = M('User') -> where("username='%s'", $username) -> select();
        if(empty($users)) {
            return '您还没有设置昵称呢，赶紧发送“昵称 您希望的昵称”来设置昵称吧！';
        }
        return '您目前的昵称是'.$users[0]['nickname'];
    }
    
    // 处理用户昵称消息，用于获取或者修改用户昵称
    public function handleNicknameMsg($username, $nickname = null) {
        if($nickname == null) {
            return $this -> getNickname($username);
        } else {
            return $this -> setNickname($username, $nickname);
        }
    }
    
    // 增加用户经验
    public function addExperience($username, $experience = 10){
        // 先判断该用户在User表中是否存在
        $users = M('User') -> where("username='%s'", $username) -> select();
        if(empty($users)) { //用户在用户表中不存在
            return false;
        } else { //用户在用户表中存在
            $data['experience'] = intval($users[0]['experience']) + $experience;
            M('User') -> where("username='%s'", $username) -> save($data);
        }
        return true;
    }
    
    // 增加用户金币
    public function addGold($username, $gold = 10){
        // 先判断该用户在User表中是否存在
        $users = M('User') -> where("username='%s'", $username) -> select();
        if(empty($users)) { //用户在用户表中不存在
            return false;
        } else { //用户在用户表中存在
            $data['gold'] = intval($users[0]['gold']) + $gold;
            M('User') -> where("username='%s'", $username) -> save($data);
        }
        return true;
    }
    
    // 获取用户资料
    public function getUserData($username) {
        // 先判断该用户在User表中是否存在
        $users = M('User') -> where("username='%s'", $username) -> select();
        if(empty($users)) { //用户在用户表中不存在
            return '您还没有设置昵称呢，赶紧发送“昵称 您希望的昵称”来设置昵称吧！';
        } else { //用户在用户表中存在
            $user = $users[0];
            return "昵称：".$user['nickname']."\n经验：".$user['experience']."\n金币：".$user['gold'];
        }
    }
    
    // 获取经验排行榜
    public function getExperienceBoard($username) {
        // 先判断该用户在User表中是否存在
        $users = M('User') -> where("username='%s'", $username) -> select();
        if(empty($users)) { //用户在用户表中不存在
            return '您还没有设置昵称呢，赶紧发送“昵称 您希望的昵称”来设置昵称吧！';
        } else { //用户在用户表中存在
            $user_count = M('User') -> count();
            $user_experience = M('User') -> where("username='%s'", $username) -> select() [0]['experience'];
            $user_rank = M('User') -> where("experience>%d", $user_experience) -> count() + 1;
            $msg = "用户总数：".$user_count."\n当前经验：".$user_experience."\n您的排名：".$user_rank;
            // 获取前十名排行榜
            $users = M('User') -> order('experience desc') -> limit(10) -> select();
            $i = 1;
            foreach($users as $user) {
                $msg = $msg."\n".$user['nickname']." ".$user['experience']." ".$i;
                $i++;
            }
            return $msg;
        }
    }
    
    // 获取金币排行榜
    public function getGoldBoard($username) {
        // 先判断该用户在User表中是否存在
        $users = M('User') -> where("username='%s'", $username) -> select();
        if(empty($users)) { //用户在用户表中不存在
            return '您还没有设置昵称呢，赶紧发送“昵称 您希望的昵称”来设置昵称吧！';
        } else { //用户在用户表中存在
            $user_count = M('User') -> count();
            $user_gold = M('User') -> where("username='%s'", $username) -> select() [0]['gold'];
            $user_rank = M('User') -> where("gold>%d", $user_gold) -> count() + 1;
            $msg = "用户总数：".$user_count."\n当前金币：".$user_gold."\n您的排名：".$user_rank;
            // 获取前十名排行榜
            $users = M('User') -> order('gold desc') -> limit(10) -> select();
            $i = 1;
            foreach($users as $user) {
                $msg = $msg."\n".$user['nickname']." ".$user['gold']." ".$i;
                $i++;
            }
            return $msg;
        }
    }
    
    // 为第一次发言的用户随机设置一个昵称
    public function setInitNickname($username) {
        // 先判断该用户在User表中是否存在
        $users = M('User') -> where("username='%s'", $username) -> select();
        if(empty($users)) { //用户在用户表中不存在
            $nickname = '用户'.time();
            $data['username'] = $username;
            $data['nickname'] = $nickname;
            M('User') -> data($data) -> add();
            return '欢迎您，'.$nickname.'。发送【说明】可获得使用说明。';
        } else { //用户在用户表中存在
            return '';
        }
    }
}