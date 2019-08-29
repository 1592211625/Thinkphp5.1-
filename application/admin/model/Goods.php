<?php
namespace app\admin\model;

use think\Db;
use think\Model;

class Goods extends Model{
    static public function getGoods($where){
        return Db::name('goods g')
                ->join('category c','g.cate_id=c.id')
                ->field('g.id,c.catename,g.goods_name,g.goods_img,g.original_price,g.goods_color_text,g.goods_type,g.goods_hot,g.goods_on,g.goods_create_time')
                ->order('id','desc')
                ->where($where)
                ->paginate(4,false,['query'=>request()->param()]);
    }
}
