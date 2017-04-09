<?php

namespace Home\Controller;

use Think\Controller;

class WechatCallbackController extends Controller {
    
    // 核心函数，根据用户的输入返回一定的消息
    public function dispatchMsg($username, $keyword) {
        
        // 处理不支持的消息
        if(empty($keyword)) {
            return '暂不支持该消息类型';
        }
        
        // 增加相应经验和金币
        $userController = new UserController();
        $userController -> addExperience($username, 10);
        $userController -> addGold($username, 10);
        
        // 如果该用户在数据库中不存在，首先设置昵称
        if(($msg = $userController -> setInitNickname($username)) != '') {
            return $msg;
        }
        
        // 处理昵称消息
        if($keyword == '昵称' || $keyword == '我的昵称') {
            return $userController -> getNickname($username);
        }
        if(mb_strlen($keyword, 'utf-8') > 3 && mb_substr($keyword, 0, 2, 'utf-8') == '昵称') {
            return $userController -> setNickname($username, mb_substr($keyword, 3, mb_strlen($keyword, 'utf-8')-3, 'utf-8'));
        }
        
        // 查询我的资料
        if($keyword == '资料' || $keyword == '我的资料') {
            return $userController -> getUserData($username);
        }
        
        // 排行榜功能
        if($keyword == '经验排行') {
            return $userController -> getExperienceBoard($username);
        }
        if($keyword == '金币排行') {
            return $userController -> getGoldBoard($username);
        }
        
        // 赌博功能
        if(mb_strlen($keyword, 'utf-8') > 3 && mb_substr($keyword, 0, 2, 'utf-8') == '赌博') {
            $gold = mb_substr($keyword, 3, mb_strlen($keyword, 'utf-8')-3, 'utf-8');
            if(!is_numeric($gold)) {
                return '赌博金额必须是数字';
            }
            if(doubleval($gold) <= 0) {
                return "赌博金额必须大于 0";
            }
            return (new GambleController()) -> gamble($username, $gold);
        }
        
        // 说明
        if($keyword == '说明'||$keyword == '使用说明'||$keyword == '菜单') {
            return "1.资料：发送【资料】可以查看资料。\n".
                "2.昵称：发送【昵称】可以查看昵称，发送【昵称 我喜欢的昵称】可以设置昵称。\n".
                "3.排行榜：发送【经验排行】可以查看经验排行，发送【金币排行】可以查看金币排行\n".
                "4.赌博：发送【赌博 金币数】可以玩赌博游戏，胜负概率50%。\n".
                "5.笑话：发送【笑话】可以查看笑话。\n".
                "6.养成：发送【问：问题 答：答案】可以养成，当你再发送问题时，机器人会直接返回答案。\n".
                "7.天气：发送【天气 城市】可以获得对应城市的天气。";
        }
        
        // 笑话
        if($keyword == '笑话') {
            return requestJoke();
        }
        
        // 天气功能
        if(mb_strlen($keyword, 'utf-8') > 3 && mb_substr($keyword, 0, 2, 'utf-8') == '天气') {
            return requestWeather(mb_substr($keyword, 3, mb_strlen($keyword, 'utf-8')-3, 'utf-8'));
        }
        
        // 处理用户自定义消息
        if(($msg = (new DialogController()) -> filter($username, $keyword)) != '') {
            return $msg;
        }
        
        return requestChat($keyword);
    }
    
    // 微信监听接口
    public function run(){
        // 下面一行为验证服务器，正式使用后注释掉
        //$this -> valid();
        if($this -> checkSignature()) {
            $this -> responseMsg();
        }
    }
    
    // 验证服务器资源
	public function valid()
    {
        $echoStr = $_GET["echostr"];
        
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }
    
    // 自动回复消息
    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

        //extract post data
		if (!empty($postStr)){
            /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
            the best way is to check the validity of xml by yourself */
            libxml_disable_entity_loader(true);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $post_msg_id = $this -> savePostMsg((array)$postObj);
            
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $time = time();
            $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
            $msgType = "text";
            $post_msg_array = (array)$postObj;
            $contentStr = $this -> dispatchMsg($post_msg_array['FromUserName'], $keyword);
            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
            echo $resultStr;
            $this -> savePushMsg((object)array(
                'PostMsgId' => $post_msg_id,
                'ToUserName' => $fromUsername,
                'FromUserName' => $toUsername,
                'CreateTime' => $time,
                'MsgType' => $msgType,
                'Content' => $contentStr
                ));

        }else { //在浏览器中直接访问该网页
        	echo "";
        	exit;
        }
    }
    
    // 检查微信传入值的签名
    private function checkSignature() {
        // you must define TOKEN by yourself
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        
		$token = C('WECHAT_TOKEN');
		$tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
    
    //保存用户发送的消息
    public function savePostMsg($postArray) {
        $data['to_user_name'] = $postArray['ToUserName'];
        $data['from_user_name'] =$postArray['FromUserName'];
        $data['create_time'] = $postArray['CreateTime'];
        $data['msg_type'] = $postArray['MsgType'];
        $data['content'] = $postArray['Content'];
        $data['msg_id'] = $postArray['MsgId'];
        $data['pic_url'] = $postArray['PicUrl'];
        $data['media_id'] = $postArray['MediaId'];
        $data['format'] = $postArray['Format'];
        $data['recognition'] = $postArray['Recognition'];
        return M('PostMsg') -> add($data);
    }
    
    // 保存微信回复的消息
    public function savePushMsg($pushObj) {
        $data['post_msg_id'] = $pushObj -> PostMsgId;
        $data['to_user_name'] = $pushObj -> ToUserName;
        $data['from_user_name'] = $pushObj -> FromUserName;
        $data['create_time'] = $pushObj -> CreateTime;
        $data['msg_type'] = $pushObj -> MsgType;
        $data['content'] = $pushObj -> Content;
        return M('PushMsg') -> add($data);
    }
}