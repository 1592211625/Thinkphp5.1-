<?php
namespace app\index\controller;

use app\index\model\Category;
use app\index\model\Img;
use app\index\controller\Base;
use think\Controller;
use think\Db;

class Index extends Base
{
    //首页
   public function index(){
       //分类
       $category = Category::select();
       $category = $category->toArray();
       $data = getTree($category);
       $this->assign('data',$data);
       //logo
       $logo = Img::where('imgtype', '1')->find();
       $logo = $logo->toArray();
       $this->assign('logo',$logo);
       //banner
       $banner = Img::where('imgtype', '0')->select();
       $banner = $banner->toArray();
       $this->assign('banner',$banner);
       //商品
       $cate = Db::name('category')
           ->where('pid','<>',0)
           ->where('recommend',1)
           ->all();
       $newData=[];
       foreach ($cate as $k=>$v){
           $parentName = Db::name('category')
                            ->where('id',$v['pid'])
                            ->value('catename');
           $newData[$k]['name']=$parentName.'&'.$v['catename'];
           $newData[$k]['goods']=Db::name('goods')
                            ->where(['cate_id'=>$v['id'],'goods_on'=>1])
                            ->field('id,goods_name,goods_subimg,original_price')
                            ->limit(6)
                            ->all();
           $newData[$k]['hot']=Db::name('goods')
                            ->where(['cate_id'=>$v['id'],'goods_hot'=>1])
                            ->field('id,goods_name,goods_subimg,original_price')
                           ->limit(4)
                           ->all();
       }
       $this->assign('goods',$newData);
       return $this->fetch();
   }

}
