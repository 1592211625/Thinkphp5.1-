<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;

class Order extends Controller
{
    //确认支付
    public function topay($index = '',$type = ''){
        $data = request()->except('/web/topay');
        if(isset($data['type'])){
            $type = $data['type'];
        }
        //获取用户地址
        $address = Db::name('address')
                ->where('user_id',session('id'))
                ->all();
        //组装数据
        $goodsinfo=[];
        //定义总价
        $goodsinfo['total']=0;
        $goodsinfo['goods_num']=0;
        //判断是如何进入支付确认页面
        if($type=='buy'){           //直接购买进入
            $goods = Db::name('goods')
                ->where('id',$data['goods_id'])
                ->field('id,goods_name,goods_subimg')
                ->find();
            $sku = Db::name('sku')
                ->where(['sku_color'=>$data['goods_color'],'sku_type'=>$data['goods_type']])
                ->field('id,sku_price')
                ->find();
            $goodsinfo['total']+=$data['goods_num']*$sku['sku_price'];
            $goodsinfo['goods_num']+=$data['goods_num'];
            $goodsinfo['goods'][0]['id']=$goods['id'];
            $goodsinfo['goods'][0]['goods_name']=$goods['goods_name'];
            $goodsinfo['goods'][0]['goods_subimg']=$goods['goods_subimg'];
            $goodsinfo['goods'][0]['goods_color']=$data['goods_color'];
            $goodsinfo['goods'][0]['goods_type']=$data['goods_type'];
            $goodsinfo['goods'][0]['goods_num']=$data['goods_num'];
            $goodsinfo['goods'][0]['sku_id']=$sku['id'];
            $goodsinfo['goods'][0]['goods_price']=$sku['sku_price'];
            $goodsinfo['goods'][0]['total']=$data['goods_num']*$sku['sku_price'];
        }else{                      //购物车购买进入
            $indexArray = explode("t",$index);
            $cookieList = json_decode(cookie('cookieList'),true);
            foreach ($indexArray as $k=>$v){
                $cookieRow = $cookieList[$v];
                $goods = Db::name('goods')
                        ->where('id',$cookieRow['goods_id'])
                        ->field('id,goods_name,goods_subimg')
                        ->find();
                $sku = Db::name('sku')
                    ->where('id',$cookieRow['sku_id'])
                    ->field('id,sku_price')
                    ->find();
                $goodsinfo['total']+=$cookieRow['goods_num']*$sku['sku_price'];
                $goodsinfo['goods_num']+=$cookieRow['goods_num'];
                $goodsinfo['goods'][$k]['id']=$goods['id'];
                $goodsinfo['goods'][$k]['goods_name']=$goods['goods_name'];
                $goodsinfo['goods'][$k]['goods_subimg']=$goods['goods_subimg'];
                $goodsinfo['goods'][$k]['goods_color']=$cookieRow['goods_color'];
                $goodsinfo['goods'][$k]['goods_type']=$cookieRow['goods_type'];
                $goodsinfo['goods'][$k]['goods_num']=$cookieRow['goods_num'];
                $goodsinfo['goods'][$k]['sku_id']=$sku['id'];
                $goodsinfo['goods'][$k]['goods_price']=$sku['sku_price'];
                $goodsinfo['goods'][$k]['total']=$cookieRow['goods_num']*$sku['sku_price'];
            }
        }
        $this->assign('address',$address);
        $this->assign('data',$goodsinfo);
        return $this->fetch();
    }

    //支付
    public function pay(Request $request){
        Db::startTrans();
        try{
        $data = $request->except("/web/pay");
        $trade_no = date("Ymd").str_pad(mt_rand(1,99999),5,'0',STR_PAD_LEFT);
        $user_id = session('id');
        $masterOrder['user_id'] = $user_id;
        $masterOrder['trade_no'] = $trade_no;
        $masterOrder['free'] = $data['total'];
        $masterOrder['number'] = $data['goods_num'];
        $masterOrder['status'] = 0;
        $masterOrder['create_time'] = time();
        $master_id = Db::name('masterorder')->insertGetId($masterOrder);
        $order = [];
        foreach ($data['goods_info'] as $k=>$v){
            list($goods_id,$sku_id,$goods_name,$goods_color,$goods_type,$goods_single_num,$goods_single_price,$goods_single_total)=explode("==",$v);
            $order[$k]['user_id'] = $user_id;
            $order[$k]['master_id'] = $master_id;
            $order[$k]['address_id'] = $data['address_id'];
            $order[$k]['goods_id'] = $goods_id;
            $order[$k]['sku_id'] = $sku_id;
            $order[$k]['goods_name'] = $goods_name;
            $order[$k]['goods_color'] = $goods_color;
            $order[$k]['goods_type'] = $goods_type;
            $order[$k]['goods_num'] = $goods_single_num;
            $order[$k]['goods_price'] = $goods_single_price;
            $order[$k]['goods_money'] = $goods_single_total;
            $order[$k]['child_order_no'] = date('Ymd') .mt_rand(100,999);
            $order[$k]['pay_type'] = $data['pay_type'];
            $order[$k]['status'] = 0;
            $order[$k]['create_time'] = time();
        }
        $res = Db::name('order')->insertAll($order);
        Db::commit();
        }catch (\Exception $e){
            Db::rollback();
        }


//        return $this->fetch();
        require_once '../extend/alipay/config.php';
        require_once '../extend/alipay/pagepay/service/AlipayTradeService.php';
        require_once '../extend/alipay/pagepay/buildermodel/AlipayTradePagePayContentBuilder.php';

        //商户订单号，商户网站订单系统中唯一订单号，必填
        $out_trade_no = $trade_no;

        //订单名称，必填
        $subject = $order[0]['goods_name'];

        //付款金额，必填
        $total_amount = $masterOrder['free'];

        //商品描述，可空
        $body = '';

        //构造参数
        $payRequestBuilder = new \AlipayTradePagePayContentBuilder();
        $payRequestBuilder->setBody($body);
        $payRequestBuilder->setSubject($subject);
        $payRequestBuilder->setTotalAmount($total_amount);
        $payRequestBuilder->setOutTradeNo($out_trade_no);

        $aop = new \AlipayTradeService($config);

        /**
         * pagePay 电脑网站支付请求
         * @param $builder 业务参数，使用buildmodel中的对象生成。
         * @param $return_url 同步跳转地址，公网可以访问
         * @param $notify_url 异步通知地址，公网可以访问
         * @return $response 支付宝返回的信息
         */
        $response = $aop->pagePay($payRequestBuilder,$config['return_url'],$config['notify_url']);

        //输出表单
        var_dump($response);
    }

    public function notify(){
        /* *
         * 功能：支付宝服务器异步通知页面
         * 版本：2.0
         * 修改日期：2017-05-01
         * 说明：
         * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。

         *************************页面功能说明*************************
         * 创建该页面文件时，请留心该页面文件中无任何HTML代码及空格。
         * 该页面不能在本机电脑测试，请到服务器上做测试。请确保外部可以访问该页面。
         * 如果没有收到该页面返回的 success 信息，支付宝会在24小时内按一定的时间策略重发通知
         */

        require_once '../extend/alipay/config.php';
        require_once '../extend/alipay/pagepay/service/AlipayTradeService.php';

        $arr=$_POST;
        $alipaySevice = new \AlipayTradeService($config);
        $alipaySevice->writeLog(var_export($_POST,true));
        $result = $alipaySevice->check($arr);

        /* 实际验证过程建议商户添加以下校验。
        1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
        2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
        3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
        4、验证app_id是否为该商户本身。
        */
        if($result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代


            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——

            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表

            //商户订单号

            $out_trade_no = $_POST['out_trade_no'];

            $order = Db::name('masterorder')
                    ->where('trade_no',$out_trade_no)
                    ->find();


            //支付宝交易号

            $trade_no = $_POST['trade_no'];

            //交易状态
            $trade_status = $_POST['trade_status'];

            file_put_contents('./text.txt','交易时间:'.date('Y-m-d H:i:s').'----交易状态:'.$_POST['trade_status']."\n",FILE_APPEND);

            if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
//                Db::startTrans();
//                try{
                    $res = Db::name('masterorder')
                        ->where('trade_no',$out_trade_no)
                        ->update([
                            'status'=>1,
                            'alipay_no'=>$trade_no
                        ]);
                    file_put_contents('./text.txt',$res."\n",FILE_APPEND);
                    Db::name('order')
                        ->where('master_id',$order['id'])
                        ->update(['status'=>1]);
                    $sku = Db::name('order')
                            ->where('master_id',$order['id'])
                            ->field('sku_id,goods_num')
                            ->all();
                    foreach ($sku as $k=>$v){
                        Db::name('sku')
                            ->where('id',$v['sku_id'])
                            ->update([
                                "sku_num"=>Db::raw('sku_num-',$v['goods_num']),
                                "forzen_num"=>Db::raw('forzen_num+',$v['goods_num']),
                            ]);
                    }
//                    Db::commit();
//                }catch (\Exception $e){
//                    Db::rollback();
//                }
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
                //如果有做过处理，不执行商户的业务程序
                //注意：
                //付款完成后，支付宝系统发送该交易状态通知
            }
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
            echo "success";	//请不要修改或删除
        }else {
            //验证失败
            echo "fail";

        }
    }

    public function returns(){
        /* *
         * 功能：支付宝页面跳转同步通知页面
         * 版本：2.0
         * 修改日期：2017-05-01
         * 说明：
         * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。

         *************************页面功能说明*************************
         * 该页面可在本机电脑测试
         * 可放入HTML等美化页面的代码、商户业务逻辑程序代码
         */
        require_once("../extend/alipay/config.php");
        require_once '../extend/alipay/pagepay/service/AlipayTradeService.php';


        $arr=$_GET;
        $alipaySevice = new \AlipayTradeService($config);
        $result = $alipaySevice->check($arr);

        /* 实际验证过程建议商户添加以下校验。
        1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
        2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
        3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
        4、验证app_id是否为该商户本身。
        */
        if($result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代码

            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

            //商户订单号
            $out_trade_no = htmlspecialchars($_GET['out_trade_no']);



            //支付宝交易号
            $trade_no = htmlspecialchars($_GET['trade_no']);

            echo "验证成功<br />支付宝交易号：".$trade_no;
            $order = Db::name('masterorder')
                ->where('trade_no',$out_trade_no)
                ->find();
            $res = Db::name('masterorder')
                ->where('trade_no',$out_trade_no)
                ->update([
                    'status'=>1,
                    'alipay_no'=>$trade_no
                ]);
            file_put_contents('./text.txt',$res."\n",FILE_APPEND);
            Db::name('order')
                ->where('master_id',$order['id'])
                ->update(['status'=>1]);
            $sku = Db::name('order')
                ->where('master_id',$order['id'])
                ->field('sku_id,goods_num')
                ->all();
            foreach ($sku as $k=>$v){
                Db::name('sku')
                    ->where('id',$v['sku_id'])
                    ->update([
                        "sku_num"=>Db::raw('sku_num-'.$v['goods_num']),
                        "forzen_num"=>Db::raw('forzen_num+'.$v['goods_num'])
                    ]);
            }

            header('location:/web/user/orderlist');
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        }
        else {
            //验证失败
            echo "验证失败";
        }
    }

    public function track($id){
        $trackData = Db::name('order')
            ->where('id',$id)
            ->field('track_name,track_no')
            ->find();
        $data = file_get_contents("http://v.juhe.cn/exp/index?key=dc72ea7ab7584e486e8cf2fb2849238d&com=".$trackData['track_name']."&no=".$trackData['track_no']);
        $data = json_decode($data,true)['result'];
        if($data){
            //组装数据
            $str = '<div style="color:black;"><div style="height: 40px;border-bottom: 2px solid lightskyblue;text-align: center;line-height: 40px;width: 100%;font-size: 20px;font-weight: bold;">物流信息</div><div style="font-size: 14px;"><b>物流公司</b>：'.$data['company'].'&nbsp;&nbsp;<b>运单号</b>：'.$data['no'].'</div><ul>';
            //循环添加物流信息
            foreach ($data['list'] as $k=>$v){
                $str.='<li>时间：'.$v['datetime'].'</li>';
                $str.='<li>物流情况：'.$v['remark'].'</li>';
            }
            $str.= '</ul></div>';
            return json(['status'=>'success','msg'=>$str]);
        }else{
            return json(['status'=>'fail','msg'=>'暂无物流信息']);
        }
    }

}
