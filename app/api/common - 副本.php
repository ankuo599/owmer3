<?php
// 这是系统自动生成的公共文件

// 返回json
function returnJson(string $str,int $code=0,$data=[]):string
{
    header('Content-type: application/json');

    // count($data)<=0 && $data = (object)$data;

    die(json_encode([
        'error' => $code,
        'msg' => $str,
        'data' => $data
    ]));
}

use \chenbool\JWT\JWT;
/*
    // 签发 token
    $jwt = jwt_encode();

    // 解密 token
    $token = jwt_decode($jwt);

    // 验证 token
    jwt_check($token);    
*/
// 加密token
function jwt_encode(string $key=null):string
{
    $token = config('token');
    $key || $key = $token['key'];
    return JWT::encode($token, $key);
}

// 解密token
function jwt_decode(string $jwt,string $key=null):array
{
    $token = config('token');
    $key || $key = $token['key'];

    try {
        //当前时间减去60,把时间留点余地
        JWT::$leeway = 60; 
        return (array)JWT::decode($jwt, $key, array('HS256')); 
    } catch(\chenbool\JWT\SignatureInvalidException $e) {  
        //签名不正确
        returnJson($e->getMessage(),-500);
    }catch(\chenbool\JWT\BeforeValidException $e) {  
        // 签名在某个时间点之后才能用
        returnJson($e->getMessage(),-500);
    }catch(\chenbool\JWT\ExpiredException $e) {  
        // token过期
        returnJson($e->getMessage(),-500);
    }catch(Exception $e) {  
        returnJson($e->getMessage(),-500);
    }

}

// 获取token
function getToken():string
{
    $token = '';
    // 检测header 头部是否有token
    if( request()->header('token') ){
        $token = request()->header('token');
    }else{
        // 获取token
        $token = input('token');
        $token || returnJson('缺少token',-500);
    }
    return $token;
}

// token转uid
function tokenToUid(string $token):int
{
    $user = \app\api\model\User::field('uid')->getByToken($token);
    return $user['uid'];
}


// 获取新的token
function get_token(string $token):string
{
    $key = config('token')['key'];

    try {
        JWT::decode($token, $key, array('HS256'));
        return app_token();
    }catch(\lishaoen\JWT\ExpiredException $e) {  
        return app_token();
    } catch (Exception $e) {
        returnJson($e->getMessage(),-500);
    }

}

// 生成新的token 安卓使用
function app_token():array
{
    // 生成新的token
    $token  = jwt_encode();
    $tokenData = jwt_decode($token);
    return [
        'token'         =>  $token,
        'exp'           =>  $tokenData['exp'],
        'exp_time'      =>  date('Y-m-d H:i:s',$tokenData['exp'])
    ];
}
