<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;

class Address extends UBase
{
    //我的地址
    public function address(){
        $id = session('id');
        $data = Db::name('address')
            ->where('user_id',$id)
            ->all();
        $this->assign('data',$data);
        return $this->fetch('address');
    }
    //添加地址
    public function addAddress(Request $request){
        $data = $request->except("/web/address/addAddress");
        $data['status']=0;
        $data['user_id']=session('id');
        $res = Db::name('address')
                ->insert($data);
        if($res){
            return json(['status'=>'success','msg'=>'地址添加成功！']);
        }else{
            return json(['status'=>'fail','msg'=>'地址添加失败！']);
        }

    }
    //删除地址
    public function delAddress(Request $request){
    $id = $request->only('id')['id'];
    $res = Db::name('address')
        ->where('id', $id)
        ->delete();
        if($res){
            return json(['status'=>'success','msg'=>'地址删除成功！']);
        }else{
            return json(['status'=>'fail','msg'=>'地址删除失败！']);
        }
    }
}
