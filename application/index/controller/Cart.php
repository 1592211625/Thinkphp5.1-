<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;

class Cart extends UBase
{
    //加入购物车
    public function addcart(Request $request){
        $data = $request->post();
        $sku = Db::name('sku')
                ->where(['sku_color'=>$data['goods_color'],'sku_type'=>$data['goods_type'],'sku_id'=>$data['goods_id']])
                ->find();
        if($sku==null){
            return json(['status'=>'fail','msg'=>'暂时无货']);
        }
        $cookieArray = cookie('cookieList')?cookie('cookieList'):[];
        $data['sku_id']=$sku['id'];
        $buffer = false;
        if($cookieArray){
            $cookieArray = json_decode($cookieArray,true);
            foreach ($cookieArray as $k=>&$v){
                if($v['sku_id']==$data['sku_id']){
                    $v['goods_num'] = $v['goods_num']+$data['goods_num'];
                    $buffer = true;
                }
                if($buffer == true){
                    cookie('cookieList',json_encode($cookieArray));
                }else{
                    array_push($cookieArray,$data);
                    cookie('cookieList',json_encode($cookieArray));
                }
            }
        }else{
            array_push($cookieArray,$data);
            cookie('cookieList',json_encode($cookieArray));
        }
        return json(['status'=>'success','msg'=>'添加购物车成功!']);
    }


    //购物车
    public function cart(){
        $data = json_decode(cookie('cookieList'),true);
        foreach($data as $k=>&$v){
            $goodData = Db::name('goods')
                        ->where('id',$v['goods_id'])
                        ->field('goods_name,goods_subimg,original_price')
                        ->find();
            $skuData = Db::name('sku')
                ->where('id',$v['sku_id'])
                ->field('sku_price,sku_num')
                ->find();
            $v['goods_info'] = $goodData;
            $v['sku_info'] = $skuData;
            $v['total'] = $v['goods_num']*$skuData['sku_price'];
        }
        $this->assign('data',$data);
        return $this->fetch();
    }

    //删除购物车商品
    public  function delcart(Request $request){
        $index = $request->param('index');
        $data = json_decode(cookie('cookieList'),true);
        unset($data[$index]);
        cookie('cookieList',json_encode($data));
        return true;
    }

    //改变购物车商品数量
    public function modcart(Request $request){
        $index = $request->param('index');
        $number = $request->param('number');
        $data = json_decode(cookie('cookieList'),true);
        $data[$index]['goods_num']=$number;
        cookie('cookieList',json_encode($data));
        return true;
    }
}
