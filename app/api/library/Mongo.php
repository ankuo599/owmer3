<?php
declare (strict_types = 1);
namespace app\api\library;

/*
*/
class Mongo
{
    protected $_conn;
    protected $_db;
    public static $instance;


    function __construct()
    {
        // $this->token = config('token');
        $this->_conn = new \MongoDB\Driver\Manager('mongodb://localhost:27017/chat');
        $this->_db = 'chat';
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

    // https://www.php.cn/php-weizijiaocheng-405982.html
    // 执行sql
    function exec($opts) {
        $cmd = new \MongoDB\Driver\Command($opts);
        $res =  $this->_conn->executeCommand($this->_db, $cmd);
        return $res->toArray();
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



}
