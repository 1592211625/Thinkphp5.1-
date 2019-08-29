<?php
namespace app\admin\controller;

use think\Controller;
use app\admin\model\User as userhandle;
use think\Request;

class User extends Controller
{
    //会员列表
    public function index(Request $request){
        if($request->isAjax()){
            $data = userhandle::getUser();
            $page = $data->render();
            $data = $data->toArray()['data'];
            return json(['data'=>$data,'page'=>$page]);
        }else{
            $data = userhandle::getUser();
            $this->assign('data',$data);
            return $this->fetch();
        }

    }
    public function userpro(Request $request){
        $data = $request->only(['id','status']);
        $res = userhandle::modifyUser($data);
        return json($res);
    }
}
