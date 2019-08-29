<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;
use app\admin\model\Img;

class Image extends Controller
{


    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function test()
    {
        $data = Img::findData();
        $this->assign('data',$data);
        return $this->fetch('index');
    }

    public function imgupload(Request $request){
        $file = $request->file('file');
        $res = $file->validate(config('validata'))->move(config('path'));
        if($res){
            return json(['status'=>'success','msg'=>'/'.$res->getPathname()]);
        }else{
            return json(['status'=>'fail','msg'=>$file->getError()]);
        }
    }
    public function sort(Request $request){
        $data = $request->except('/admin/image/sort');
        $res = Img::modifySort($data);
    }
    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
        return $this->fetch('add');
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        p(111);
        $data = $request->except(["/admin/image","file"]);
        $res = Img::addData($data);
        return $res;
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $data = Img::get($id);
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->except('/admin/image');
        $res = Img::update($data,$id);
        if($res){
            return json(['status'=>'success','msg'=>'修改成功']);
        }else{
            return json(['status'=>'fail','msg'=>'修改失败']);
        }
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
        p($id);
//        $res = Img::destroy($id);
//        return $res;
    }
}
