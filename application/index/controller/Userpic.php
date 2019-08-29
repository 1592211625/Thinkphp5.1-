<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;

class Userpic extends UBase
{
    //头像管理
    public function userpic(Request $request){
        $id = session('id');
        $data = Db::name('user')
            ->where('id',$id)
            ->find();
        $this->assign('data',$data);
        return $this->fetch();
    }
    public function changePic(Request $request){
        $file = $request->file('file');
        $res = $file->validate(['size'=>1567800,'ext'=>'jpg,png,gif'])->move('uploads/pic');
        if($res){
            return json(['status'=>'success','msg'=>'/'.$res->getPathname()]);
        }else{
            return json(['status'=>'fail','msg'=>$file->getError()]);
        }
    }
    public function picUpload(Request $request){
        $pic = $request->param('userimg');
        $id = $request->param('id');
        $res = Db::name('user')
            ->where('id',$id)
            ->update(['userimg'=>$pic]);
        if($res){
            return json(['status'=>'success','msg'=>'头像上传成功！']);
        }else{
            return json(['status'=>'fail','msg'=>'头像上传失败！']);
        }
    }


}
