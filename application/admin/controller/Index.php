<?php
namespace app\admin\controller;

use app\admin\model\Setting;
use think\Controller;
use think\Request;
class Index extends Base
{
    public function index(){
        return $this->fetch();
    }
    public function system(){
        $data = Setting::getSetting();
        $this->assign('data',$data);
        return $this->fetch();
    }
    public function edit(Request $request){
        $data = Setting::getSetting();
        $this->assign('data',$data);
        return $this->fetch();
    }
    public function editpro(Request $request){
        $data = $request->except("/admin/editpro");

        $valresult = $this->validate($data,'Index.edit');
        if($valresult!==true){
            return json(['status'=>'fail','msg'=>$valresult]);
        }
        $row = Setting::editStting($data);
        return $row;
    }
}
