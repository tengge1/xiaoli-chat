<?php

// 读取数据库中的配置参数
$config=M('config')->getField('name,value');
C($config);

/**
 * 从图灵机器人获取聊天内容
 * @param mixed $keyword 用户输入内容
 * @return mixed
 */
function requestChat($keyword) {
    $ch = curl_init();
    $url = 'http://apis.baidu.com/turing/turing/turing?key=879a6cb3afb84dbf4fc84a1df2ab7319&info='.urlencode($keyword).'&userid=eb2edb736';
    $header = array(
        'apikey:百度API Key',
    );
    // 添加apikey到header
    curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // 执行HTTP请求
    curl_setopt($ch , CURLOPT_URL , $url);
    $res = curl_exec($ch);

    return json_decode($res) -> showtext;
}
/**
 * 获取某个城市的天气
 * @param mixed $keyword 
 */
function requestWeather($keyword) {
    $ch = curl_init();
    $url = 'http://apis.baidu.com/apistore/weatherservice/cityname?cityname='.urlencode($keyword);
    $header = array(
        'apikey:百度API Key',
    );
    // 添加apikey到header
    curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // 执行HTTP请求
    curl_setopt($ch , CURLOPT_URL , $url);
    $res = curl_exec($ch);

    $obj =json_decode($res);
    if($obj -> errNum == -1) {
        return '抱歉，未找到该城市。';
    } else {
        $obj = $obj -> retData;
        return 
            "城市：".$obj -> city. "\n"
            ."时间：".$obj -> date." ".$obj -> time. "\n"
            ."经度：".$obj -> longitude. " 纬度：". $obj -> latitude. "\n"
            ."天气：".$obj -> weather. "\n"
            ."温度：".$obj -> l_tmp."℃~".$obj -> h_tmp."℃\n"
            ."风向：".$obj -> WD." "."风速：".$obj -> WS;
    }
}

/**
 * 随机获取一个笑话
 * @return string
 */
function requestJoke() {
    $ch = curl_init();
    $url = 'http://apis.baidu.com/showapi_open_bus/showapi_joke/joke_text?page='.rand(1,6719).'&maxResult=1';
    $header = array(
        'apikey: 百度API Key',
    );
    // 添加apikey到header
    curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // 执行HTTP请求
    curl_setopt($ch , CURLOPT_URL , $url);
    $res = curl_exec($ch);

    return strip_tags(json_decode($res, true)['showapi_res_body']['contentlist'][0]['text']);
}
