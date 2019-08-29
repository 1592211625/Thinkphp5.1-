<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;

class Goods extends Controller
{
    //列表页
    public function goodList(Request $request,$id){
        $name = Db::name('category')
                    ->where('id',$id)
                    ->field('catename')
                    ->find();
        $this->assign('name',$name['catename']);
        $data = Db::name('goods')
                    ->where(['cate_id'=>$id,'goods_on'=>1])
                    ->field('id,goods_name,goods_subimg,original_price')
                    ->all();
        $this->assign('data',$data);
        return $this->fetch();
    }
    //详情页
    public function detail(Request $request,$id){
        $data = Db::name('goods')
                    ->where('id',$id)
                    ->field('id,goods_name,goods_img,original_price,goods_color,goods_color_text,goods_type,goods_desc')
                    ->find();
        $this->assign('goods_color',explode("|",$data['goods_color']));
        $this->assign('goods_color_text',explode("|",$data['goods_color_text']));
        $this->assign('goods_type',explode("|",$data['goods_type']));
        $this->assign('data',$data);
        return $this->fetch();
    }
}
