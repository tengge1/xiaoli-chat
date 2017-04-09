<?php

namespace Home\Controller;

use Think\Controller;

// 对话控制器
class DialogController extends Controller {
    
    // 添加一个对话
    public function addDialog($username, $question, $answer) {
        $data['username'] = $username;
        $data['question'] = $question;
        $data['answer'] = $answer;
        M('Dialog') -> data($data) -> add();
    }
    
    // 获取一个对话
    public function getDialog($username, $question) {
        $answers = M('Dialog') -> where("username='%s' and question='%s'", $username, $question) -> select();
        if(empty($answers)) {
            return '';
        }
        $rand = rand(0, count($answers)-1);
        return $answers[$rand]['answer'];
    }
    
    // 过滤用户对话，如果处理，返回非空字符串，否则返回''
    public function filter($username, $keyword){
        //先判断是否在添加对话
        if(mb_strpos($keyword,'问:', 0, 'utf-8') === 0 || mb_strpos($keyword,'问：', 0, 'utf-8') === 0) {
            $start = mb_strpos($keyword, '问:', 0, 'utf-8');
            if($start === false) {
                $start = mb_strpos($keyword, '问：', 0, 'utf-8');
            }
            $end = mb_strpos($keyword, '答:', 0, 'utf-8');
            if($end === false) {
                $end = mb_strpos($keyword, '答：', 0, 'utf-8');
            }
            if($start !== false && $end !== false) {
                $question = mb_substr($keyword, $start+2, $end-3, 'utf-8');
                $answer = mb_substr($keyword, $end+2, mb_strlen($keyword, 'utf-8')-$end-2, 'utf-8');
                if($question == '') {
                    return '问题不允许为空！';
                }
                if($answer == '') {
                    return '答案不允许为空！';
                }
                $this -> addDialog($username, $question, $answer);
                return '记住了，我会好好练习的。';
            }
        }
        
        //查询数据库中是否已经保存该对话，并返回
        return $this -> getDialog($username, $keyword);
    }
}