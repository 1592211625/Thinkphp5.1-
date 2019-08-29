<?php
namespace app\admin\model;

use think\Model;

class Admin extends Model{
    static function getuser($userPost){
        $userData = Admin::where('admin_name',$userPost['username'])->find();
        if($userData){
            $pwd = md5($userPost['password'].$userData['admin_salt']);
            if($pwd==$userData['admin_pwd']){
                session('admin_name',$userData['admin_name']);
                session('admin_time',time());
                return json(['status'=>'success','msg'=>'登陆成功！']);
            }else{
                return json(['status'=>'fail','msg'=>'密码不正确！']);
            }
        }else{
            return json(['status'=>'fail','msg'=>'用户名不存在！']);
        }
    }
}
