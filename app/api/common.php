<?php
// 这是系统自动生成的公共文件

// 返回json
function returnJson(string $str,int $code=0,$data=[]):string
{
    header('Access-Control-Allow-Origin: *');
    header('Content-type: application/json');

    // count($data)<=0 && $data = (object)$data;

    die(json_encode([
        'error' => $code,
        'msg' => $str,
        'data' => $data
    ]));
}

function _token() {
    return \app\api\library\Token::instance();
}

// 加密token
function jwt_encode(string $key=null):string
{
    return _token()->encode($key);
}

// 解密token
function jwt_decode(string $jwt,string $key=null):array
{
    return _token()->decode($jwt, $key);
}

// 获取token
function getToken():string
{
    return _token()->getToken();
}

// token转uid
function tokenToUid(string $token):int
{
    return _token()->tokenToUid($token);
}

// 获取uid
function getUid():int
{
    $token = getToken();
    return tokenToUid($token);
}

// 获取新的token
function get_token(string $token):string
{
    return _token()->get_token($token);
}

// 生成新的token 安卓使用
function app_token():array
{
    return _token()->app_token();
}


/**
 * 时间转换
 * @param int $time 转换的时间 以秒为单位
 * @return string 小时/天数
 */
function time_to_hour($time=null)
{
    $hour = $time/3600;
    if($hour <24){
        return $hour.'小时';
    }else{
        $day = $hour/24;
        return $day.'天';
    }
}

/**
 * 判断设备
 * @param int $device 设备类型
 * @return string 设备类型
 */
function get_device($device=null)
{
    switch ($device) {
        case 1:
            return 'Android';
            break;
        case 2:
            return 'IOS';
            break;
        default:
        return '不限制';
            break;
    }
}

/**
 * 写入资金明细
 * @param int $data 保存的记录
 * @return object 返回插入记录
 */
function put_record($data=[])
{
    $data['create_time']  =  time();
}

/**
 * 文件锁
 * @param object $obj 要执行操作对象
 * @param string $fun 运行的函数名
 * @return void 无返回值
 */
function run_lock(&$obj,$fun)
{
    // 判断文件是否存在
    file_exists('./lock.md') || file_put_contents('./lock.md','文件锁使用');
    // 打开文件
    $fp = fopen('./lock.md','r');
    //加锁
    if(flock($fp, LOCK_EX)){
        // 设置对象里面lock为真
        $obj->lock = true;

        // 代码开始 ******************
        $obj->$fun();
        // 代码结束 ******************

        //执行完成解锁
        flock($fp,LOCK_UN);
    }
    //关闭文件
    fclose($fp);
}

// 指定两个日期，转换为 Unix 时间戳
function getDiffYear(string $date):int
{
    $date1 = strtotime($date);  
    $date2 = time(); 

    //计算两个日期之间的时间差
    $diff = abs($date2 - $date1);  

    //转换时间差的格式
    $years = floor($diff / (365*60*60*24)); 

    return $years;
}