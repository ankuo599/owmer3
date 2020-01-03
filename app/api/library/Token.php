<?php
declare (strict_types = 1);
namespace app\api\library;
use \chenbool\JWT\JWT;

/*
    // 签发 token
    $jwt = jwt_encode();

    // 解密 token
    $token = jwt_decode($jwt);

    // 验证 token
    jwt_check($token);    
*/
class Token
{
    protected $key;
    protected $token;
    public static $instance;

    function __construct()
    {
        $this->token = config('token');
        $this->key = $this->token['key'];
        unset($this->token['key']);
    }

    // 实例化
    public static function instance()
    {
        //如果不存在实例，则返回实例
        if( empty(self::$instance) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // https://blog.csdn.net/AnPHPer/article/details/80278696
    public function __set($name, $val) {
        return $this->data[$name] = $val;
    }
    
    public function __get($name) {
        return $this->data[$name];
    }

    // 加密token
    public function encode(string $key=null):string
    {
        $key || $key = $this->key;
        return JWT::encode($this->token, $key);
    }

    // 解密token
    public function decode(string $jwt,string $key=null):array
    {
        $key || $key = $this->key;

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
    public function getToken():string
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
    public function tokenToUid(string $token):int
    {
        $user = \app\api\model\User::field('uid')->getByToken($token);
        return $user['uid'];
    }

    // 获取新的token
    public function get_token(string $token):string
    {
        try {
            JWT::decode($token, $this->key, array('HS256'));
            return app_token();
        }catch(\lishaoen\JWT\ExpiredException $e) {  
            return app_token();
        } catch (Exception $e) {
            returnJson($e->getMessage(),-500);
        }

    }

    // 生成新的token 安卓使用
    public function app_token():array
    {
        $token  = jwt_encode();
        $tokenData = jwt_decode($token);
        return [
            'token'         =>  $token,
            'exp'           =>  $tokenData['exp'],
            'exp_time'      =>  date('Y-m-d H:i:s',$tokenData['exp'])
        ];
    }


}
