<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\admin\model\Category as cate;

class Category extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function test()
    {
        //
        $data = cate::all();
        $data = $data->toArray();
        $new = [];
        foreach ($data as $k=>$v){
            if($v['pid']==0){
                $new[] = $v;
                unset($data[$k]);
                foreach ($data as $kk=>$vv){
                    if($vv['pid']==$v['id']){
                        $vv['catename'] = '├'.$vv['catename'];
                        $new[] = $vv;
                    }
                }
            }
        }
        $this->assign('data',$new);

        return $this->fetch('index');
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
        $data = cate::all();
        $data = $data->toArray();
        $new = [];
        foreach ($data as $k=>$v){
            if($v['pid']==0){
                $new[] = $v;
                unset($data[$k]);
                foreach ($data as $kk=>$vv){
                    if($vv['pid']==$v['id']){
                        $vv['catename'] = '├'.$vv['catename'];
                        $new[] = $vv;
                    }
                }
            }
        }
        $this->assign('data',$new);

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
        //
        $data = $request->except('/admin/category');
        $res = cate::create($data);
        if($res){
            return json(['status'=>'success','msg'=>'添加成功']);
        }else{
            return json(['status'=>'fail','msg'=>'添加失败']);
        }


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
        //
        $oldData = cate::get($id);
        $data = cate::all();
        $data = $data->toArray();
        $new = [];
        foreach ($data as $k=>$v){
            if($v['pid']==0){
                $new[] = $v;
                unset($data[$k]);
                foreach ($data as $kk=>$vv){
                    if($vv['pid']==$v['id']){
                        $vv['catename'] = '├'.$vv['catename'];
                        $new[] = $vv;
                    }
                }
            }
        }
        $this->assign('data',$new);
        $oldData = $oldData->toArray();
        $this->assign('oldData',$oldData);
        return $this->fetch('edit');
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
        //
        $data = $request->except('/admin/category');
        $res = cate::update($data,$id);
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
    }
}
