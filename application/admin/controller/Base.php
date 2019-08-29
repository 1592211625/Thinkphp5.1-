<?php
namespace app\admin\controller;

use think\App;
use think\Controller;

class Base extends Controller{
    public function __construct()
    {
        parent::__construct();
        //判断用户是否登录
        if(!session('admin_name')){
            $this->redirect('/admin/login');
        }
        //判断登录时长
        if(time()-session('admin_time')>1800){
            $this->redirect('/admin/login');
        }else{
            session('admin_time',time());
        }
    }
}