<?php

namespace app\admin\controller;

use function GuzzleHttp\Promise\all;
use think\Controller;
use think\Db;
use think\Request;
use app\admin\model\Goods as gs;

class Goods extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function test(Request $request)
    {
        //
        $data = $request->param();
        $where = [];
        if(!empty($data['start'])){
            $where[] = ['goods_create_time','>=',strtotime($data['start'])];
        }
        if(!empty($data['end'])){
            $where[] = ['goods_create_time','<=',strtotime($data['end'])];
        }
        if(!empty($data['search'])){
            $where[] = ['goods_name','like','%'.$data['search'].'%'];
        }
        $data = gs::getGoods($where);
        $this->assign('data',$data);
        return $this->fetch('index');
    }

    public function cate(Request $request){
        $pid = $request->only('pid')['pid'];
        $cate = Db::name('category')
            ->where('pid',$pid)
            ->all();
        return json($cate);
    }
    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
        $cate = Db::name('category')
            ->where('pid','0')
            ->all();
        $this->assign('cate',$cate);
        return $this->fetch('add');
    }
    public function imgupload1(Request $request){
        $file = $request->file('file');
        $res = $file->validate(['size'=>156780,'ext'=>'jpg,png,gif'])->move('goodsimg/image1');
        if($res){
            return json(['status'=>'success','msg'=>'/'.$res->getPathname()]);
        }else{
            return json(['status'=>'fail','msg'=>$file->getError()]);
        }
    }

    public function imgupload2(Request $request){
        $file = $request->file('file');
        $res = $file->validate(['size'=>156780,'ext'=>'jpg,png,gif'])->move('goodsimg/image2');
        if($res){
            return json(['status'=>'success','msg'=>'/'.$res->getPathname()]);
        }else{
            return json(['status'=>'fail','msg'=>$file->getError()]);
        }
    }

    public function imgupload3(Request $request){
        $file = $request->file('file');
        $res = $file->validate(['size'=>156780,'ext'=>'jpg,png,gif'])->move('goodsimg/image3');
        if($res){
            return json(['status'=>'success','msg'=>'/'.$res->getPathname()]);
        }else{
            return json(['status'=>'fail','msg'=>$file->getError()]);
        }
    }

    public function sku(Request $request){
        $id = $request->only('id')['id'];
        $data = Db::name('goods')
            ->where('id',$id)
            ->field('goods_color_text,goods_type')
            ->find();
        $this->assign('sku_color',explode('|',$data['goods_color_text']));
        $this->assign('sku_type',explode('|',$data['goods_type']));
        $this->assign('sku_id',$id);
        return $this->fetch('sku');
    }
    public function addsku(Request $request){
        $data = $request->except('/admin/addsku');
        $newData = [];
        foreach ($data['sku_color'] as $k=>$v){
            $newData[$k]['sku_id']=$data['sku_id'][$k];
            $newData[$k]['sku_color']=$v;
            $newData[$k]['sku_type']=$data['sku_type'][$k];
            $newData[$k]['sku_price']=$data['sku_price'][$k];
            $newData[$k]['sku_num']=$data['sku_num'][$k];
            $newData[$k]['sku_time']=time();
            $newData[$k]['forzen_num']=0;
        }
        $res = Db::name('sku')->insertAll($newData);
        if($res){
            return json(['status'=>'success','msg'=>'添加sku成功']);
        }else{
            return json(['status'=>'fail','msg'=>'添加sku失败']);
        }
    }

    //查看sku
    public function looksku(Request $request,$id){
        $data = Db::name('sku s')
            ->join('goods g','g.id=s.sku_id')
            ->field('s.sku_id,g.goods_name,s.sku_color,s.sku_type,s.sku_price,s.sku_num,s.sku_time,s.id')
            ->where('sku_id',$id)
            ->all();
        $this->assign('data',$data);
        $this->assign('id',$id);
        return $this->fetch();
    }
    //编辑sku
    public function editsku(Request $request,$id){
        $data = Db::name('sku')->where('id',$id)->find();
        $this->assign("data",$data);
        return $this->fetch();
    }
    //编辑skuPro
    public function editskupro(Request $request,$id){
        $data=$request->post();
        $res = Db::name('sku')->where('id',$id)->update($data);
        if($res){
            return json(['status'=>'success','msg'=>'修改sku成功']);
        }else{
            return json(['status'=>'fail','msg'=>'修改sku失败']);
        }
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
        $data = $request->except(['file','/admin/goods']);
        $data['goods_create_time']=time();
        $data['goods_color'] = implode('|',$data['goods_color']);
        $data['goods_color_text'] = implode('|',$data['goods_color_text']);
        $str = $data['goods_img'];
        $image = \think\Image::open('.'.$str);
        $path = substr($str,0,strripos($str,'\\')+1);
        $imgName = time().mt_rand(1000,9999).'.'.$image->type();
        $image->thumb(100,100)->save('.'.$path.$imgName);
        $data['goods_subimg'] = $path.$imgName;
        $id = Db::name('goods')->insertGetId($data);
        if($id){
            return json(['status'=>'success','msg'=>'添加成功','id'=>$id]);
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
        $data = Db::name('goods')
            ->where('id',$id)
            ->find();
        $cate = Db::name('category')->all();
        $this->assign('data',$data);
        $this->assign('cate',$cate);
        $this->assign('goods_color',explode('|',$data['goods_color']));
        $this->assign('goods_color_text',explode('|',$data['goods_color_text']));
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
        //
        $data = $request->except('/admin/goods');
        $res = gs::update($data,$id);
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
        //删除当前商品的sku数据
        $res = Db::name('sku')->where('sku_id',$id)->delete();
        if($res) {
            //如果sku表删除成功再删除商品表中的数据
            $result = Db::name('goods')->where('id', $id)->delete();
            if($result){
                return json(['status'=>'success','msg'=>'删除成功！']);
            }else{
                return json(['status'=>'fail','msg'=>'删除失败！']);
            }
        }else{
            return json(['status'=>'fail','msg'=>'sku未删除！']);
        }
    }
}
