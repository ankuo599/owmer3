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
        $data               = input();
        $data['time']       = date('H:i',time());
        $data['date']       = date('Y-m-d H:i',time());
        $data['send']       = false;  //是否发送
        $data['from_id']    = (int)$data['from_id'];
        $data['to_id']      = (int)$data['to_id'];

        $to_id              = $data['to_id'];

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


    public function all(){
        $data = Db::connect('mongo')
        ->table('chat')
        ->select();
        returnJson('mongo',0,$data);
    }


    public function test(int $to_id,int $from_id){
        
        $map = [
            ['to_id','=',$to_id],
            ['from_id','=',$from_id],
        ];
        $data = Db::connect('mongo')
        ->table('chat')
        ->where($map)
        ->select();

        // echo json_encode($data);
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
            ['to_id','=',$to_id],
            ['from_id','=',$to_id]
        ];

        $res = Db::connect('mongo')
        ->table('chat')
        ->whereOr($map)
        ->order('_id','desc')
        ->limit(10)
        ->select();

        $res = $this->assoc_unique($res, 'from_id');
        returnJson('查询成功',0,$res);
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
        returnJson('mongo',0,$res);
    }


    // 去除重复 ------------------------------------------
    public function assoc_unique($arr, $key) {
        $res = $arr;
        $tmp_arr = array();
        foreach ($arr as $k => $v) {
            if (in_array($v[$key], $tmp_arr)) {
                //搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true
                unset($res[$k]);
            } else {
                $tmp_arr[] = $v[$key];
            }

        }

        return $this->array_new($res);
    }

    public function array_new($arr){
        $tmp_arr = $arr;
        $res = [];
        foreach($arr as $k => $v){
            $res[] = $this->search_arr($arr,$v['to_id'],$v['from_id']);
        }
        return $this->assoc_unique1($res, 'from_id');
    }

    public function search_arr($data,$to_id,$from_id){
        foreach($data as $k => $v){
            if( ($v['to_id'] == $to_id) && ($v['from_id'] == $from_id)  ){
                return $v;
            }elseif(  ($v['to_id'] == $from_id) && ($v['from_id'] == $to_id)  ){
                return $v;
            }
        }
    }
    
    // 去除重复
    public function assoc_unique1($arr, $key) {
        $res = $arr;
        $tmp_arr = array();
        foreach ($arr as $k => $v) {
            if (in_array($v[$key], $tmp_arr)) {
                //搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true
                unset($res[$k]);
            } else {
                $tmp_arr[] = $v[$key];
            }

        }
        return $res;
    }


}
