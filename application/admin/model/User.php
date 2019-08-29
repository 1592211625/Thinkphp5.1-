<?php
namespace app\admin\model;

use think\Model;
use think\Db;

class User extends Model{
    static function getUser(){
        return Db::name('user')->paginate(2);
    }
    static function modifyUser($data){
        $res = User::update($data);
        if($res){
            return ['status'=>'success','msg'=>'操作成功'];
        }else{
            return ['status'=>'fail','msg'=>'操作失败'];
        }
    }
}
