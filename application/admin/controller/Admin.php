<?php
namespace app\admin\controller;

use think\captcha\Captcha;
use think\Controller;
use think\Request;
use think\Validate;
use app\admin\model\Admin as User;

class Admin extends Controller
{
    public function tologin(Request $request){
        $data = $request->only(["captcha"]);
        $validate = new Validate();
        $rule = ['captcha'=>'require|captcha'];
        if(!$validate->check($data,$rule)){
            return json(['status'=>'fail','msg'=>'验证码错误，请输入正确验证码']);
        }
        $userPost = $request->param();
        $res = User::getuser($userPost);
        return $res;
    }

    public function captcha(){
        $captcha = new Captcha();
        $captcha->fontSize = 20;
        $captcha->length   = 3;
        $captcha->useNoise = false;
        return $captcha->entry();
    }

    function loginout(){
        session(null);
        $this->redirect('/admin/login');
    }
}
