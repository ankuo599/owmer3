<?php
declare (strict_types = 1);
namespace app\api\controller;

use think\facade\Cache,
    think\facade\Db,
    GatewayWorker\Lib\Gateway;

class Chat
{
 
     // 初始化registerAddress
     public function __construct(){
        Gateway::$registerAddress = config('gateway_worker.registerAddress');
     }

    public function index()
    {

    }
  
    /*
        to_id	        ture	int	接收id
        from_id	        ture	string	发送人id
        from_face	    ture	string	发送人头像
        from_nickrname	ture	string	发送人昵称
        type	ture	string	发送类型，仅支持text
        data	ture	string	内容
    */
    public function send()
    {
        request()->isPost() || returnJson('非法请求请求',-100);
        //接受参数
        $data = input();
        $data['time'] = date('Y-m-d H:i',time());
        $data['send'] = false;  //是否发送
        $to_id = $data['to_id'];

        // 验证数据
        $validate = new \app\api\validate\Chat;
        $validate->scene('send')->check($data) || returnJson($validate->getError(),-10);

        // 判断接受一方是否在线
        if (Gateway::isUidOnline($to_id)){
            Gateway::sendToUid($to_id,json_encode($data));
            $data['send'] = true;
            // 保存消息
            $this->addMsg($data);
            returnJson('发送成功',0,$data);
        }

        // --------- 不在线 --------
        // 保存消息
        $this->addMsg($data);
        returnJson('发送成功',0,$data);
    }  

    // 绑定uid
    public function bind()
    {
        request()->isPost() || returnJson('非法请求请求',-100);

        // 接受参数
        $data = input();
        $client_id = $data['client_id'];
        $uid = $data['uid'];

        // 验证数据
        $validate = new \app\api\validate\Chat;
        $validate->scene('bind')->check($data) || returnJson($validate->getError(),-10);

        Gateway::isOnline($client_id) || returnJson('clientId不合法',-10,$data);
        Gateway::getUidByClientId($client_id) && returnJson('已被绑定',-10,$data);
  
        // 直接绑定
        Gateway::bindUid($client_id,$uid);
        // 返回成功
        returnJson('绑定成功',0,$data);
    }  


    public function test(){

        // Db::connect('mongo')
        // ->table('chat')
        // ->insert([
        //     'user'  =>  'bool'
        // ]);

        $data = Db::connect('mongo')
        ->table('chat')
        ->select();

        returnJson('mongo',0,$data);
    }

    // 添加消息列表
    public function addMsg(array $data)
    {
        return Db::connect('mongo')
        ->table('chat')
        ->insert($data);

    }

    // 获取我的消息列表
    public function getMyList()
    {
        // 接受参数
        $data = input();
        $to_id = (int)$data['to_id'];

        $map = [
            ['to_id','=',$to_id]
        ];

        $aggregate = ['sum'   => 'number'];
        $groupBy  = [
            'from_id',
            'to_id',
            'from_face',
            'from_nickrname',
            'type',
            'data',
            'number',
            'time',
            'send'
        ];

        $res = Db::connect('mongo')
        ->table('chat')
        // ->where($map)
        // ->order('_id','desc')
        // ->getDistinct('from_id');
        // ->limit(10)
        // ->select();
        ->multiAggregate([],$groupBy);

        returnJson('mongo',0,$res);
    }


    // 获取消息列表
    public function getMsgList()
    {
        // 接受参数
        $data = input();
        $from_id = (int)$data['from_id'];
        $to_id = (int)$data['to_id'];

        $map = [
            ['from_id','=',$from_id],
            ['to_id','=',$to_id]
        ];

        $or = [
            ['to_id','=',$from_id],
            ['from_id','=',$to_id]
        ];

        $res = Db::connect('mongo')
        ->table('chat')
        ->whereOr($map)
        ->whereOr($or)
        // ->limit(30)
        ->order('_id','asc')
        ->select();
        // echo json_encode($res);
        returnJson('mongo',0,$res);
    }


}
