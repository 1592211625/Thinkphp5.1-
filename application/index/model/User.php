<?php
namespace app\index\model;

use think\Db;
use think\Model;

class User extends Model{
    static function addCode($data){
        return Db::name('mobile')->insert($data);
    }
    static function getPhoneCode($data){
        return Db::name('mobile')
            ->where('mobile',$data)
            ->order('id','desc')
            ->find();
    }
    static function userData($userinfo){
        $user = new User($userinfo);
        $row = $user->save();
        return $row;
    }

    static function userLogin($data){
        $userData = User::where('username',$data)->find();
        return $userData;
    }

}
