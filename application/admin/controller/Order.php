<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;

class Order extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function test()
    {
        //
        $data = Db::name('order o')
                ->join('goods g','o.goods_id=g.id')
                ->field('o.id,o.goods_color,o.goods_type,o.goods_price,o.goods_num,o.goods_money,o.status,g.goods_name')
                ->paginate();
        $this->assign('data',$data);
        return $this->fetch('index');
    }

    public function track($id){
        $this->assign('id',$id);
        return $this->fetch();
    }
    public function trackpro(Request $request){
        $data = $request->except('/admin/order/trackpro');
        $data['status']=2;
        $res = Db::name('order')
            ->where('id',$data['id'])
            ->update($data);
        if($res){
            return json(['status'=>'success','msg'=>'提交成功']);
        }else{
            return json(['status'=>'fail','msg'=>'提交失败']);
        }
    }

    //退款处理
    public function cancel($id){
        $order = Db::name('order')
            ->where('id',$id)
            ->field('sku_id,child_order_no,backpay_desc,master_id,track_name,goods_num,goods_money')
            ->find();
        if(!$order['track_name']){
            p(111);
            Db::name('sku')
                ->where('id',$order['sku_id'])
                ->update(['number'=>Db::raw('num+'.$order['goods_num']),
                    'frozen_number'=>Db::raw('frozen_num-'.$order['goods_num'])
                ]);
        }

        //退款处理
        require_once '../extend/alipay/config.php';
        require_once '../extend/alipay/pagepay/service/AlipayTradeService.php';
        require_once '../extend/alipay/pagepay/buildermodel/AlipayTradeRefundContentBuilder.php';

        //获取支付宝交易号
        $masterData = Db::name('masterorder')->where('id',$order['master_id'])->find();

        //商户订单号，商户网站订单系统中唯一订单号
        $out_trade_no = $masterData['trade_no'];

        //支付宝交易号
        $trade_no = $masterData['alipay_no'];
        //请二选一设置

        //需要退款的金额，该金额不能大于订单金额，必填
        $refund_amount = $order['goods_money'];

        //退款的原因说明
        $refund_reason = $order['backpay_desc'];

        //标识一次退款请求，同一笔交易多次退款需要保证唯一，如需部分退款，则此参数必传
        $out_request_no = '';

        //构造参数
        $RequestBuilder=new \AlipayTradeRefundContentBuilder();
        $RequestBuilder->setOutTradeNo($out_trade_no);
        $RequestBuilder->setTradeNo($trade_no);
        $RequestBuilder->setRefundAmount($refund_amount);
        $RequestBuilder->setOutRequestNo($out_request_no);
        $RequestBuilder->setRefundReason($refund_reason);

        $aop = new \AlipayTradeService($config);
        /**
         * alipay.trade.refund (统一收单交易退款接口)
         * @param $builder 业务参数，使用buildmodel中的对象生成。
         * @return $response 支付宝返回的信息
         */
        $response = $aop->Refund($RequestBuilder);
        var_dump($response);;
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
        //
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
