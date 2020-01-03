<?php
declare (strict_types = 1);

namespace app\api\model;

use think\Model;

/**
 * @mixin think\Model
 */
class Task extends Model
{

    public function user()
    {
        // return $this->hasOne(User::class,'uid','uid');
        return $this->hasOne(User::class,'uid','uid')->bind([
            'phone',
            'nickname',
            'face'
        ]);

        
    }

    public function cate()
    {
        return $this->hasOne(Cate::class,'cid','cate')->bind([
            'cate_name' =>   'name' 
        ]);

        
    }

}
