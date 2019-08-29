<?php
namespace app\index\controller;
use function GuzzleHttp\Promise\all;
use think\Controller;
use think\Db;
use think\Request;
use app\index\model\User as adduser;
use think\Session;
use think\Validate;

class User extends Controller
{
    //登录
    public function tologin(Request $request){
        $data = $request->except('/web/tologin');
        $code = $request->only(["captcha"]);
        $validate = new Validate();
        $rule = ['captcha'=>'require|captcha'];
        if(!$validate->check($code,$rule)){
            return json(['status'=>'fail','msg'=>'验证码不正确']);
        }
        $userData = adduser::userLogin($data['username']);
        if($userData){
            $pwd = md5($data['userpwd'].$userData['salt']);
            if($pwd==$userData['userpwd']){
                session('username',$userData['username']);
                session('userimg',$userData['userimg']);
                session('id',$userData['id']);
                session('logintime',time());
                return json(['status'=>'success','msg'=>'登陆成功！']);
            }else{
                return json(['status'=>'fail','msg'=>'密码不正确！']);
            }
        }else{
            return json(['status'=>'fail','msg'=>'用户名不存在！']);
        }

    }
    //退出登录
    function loginout(){
        session(null);
        $this->redirect('/');
    }

    //注册
    public function toregister(Request $request){
        $data = $request->except('/web/toregister');
        $data['usertime'] = time();
        $data['status'] = 1;
        $data['salt'] = mt_rand(1000,9999);
        $salt = $data['salt'];
        $data['userpwd'] = md5($data['userpwd'].$salt);
        $data['userimg'] = '/static/index/images/1.jpg';
        $phoneCode = adduser::getPhoneCode($data['username']);
        if(time()-$phoneCode['code_time']>180){
            return json(['status'=>'fail','msg'=>'验证码已失效，请在3分钟内注册！']);
        }
        if($data['code']!=$phoneCode['code']){
            return json(['status'=>'fail','msg'=>'请输入正确的验证码！']);
        }
        $row = adduser::userData($data);
        return $row;
    }
    //验证码
    public function getcode(Request $request){
        $phone = $request->param("phone");
        $code = mt_rand(100000,999999);
        require_once '../extend/aliyun-note/api_demo/SmsDemo.php';
        $sms = new \SmsDemo();
        $res = $sms->sendSms($phone,$code);
        echo $code;
        if($res->Message=='OK'){
            session('phone_code',$code);
            $res = adduser::addCode(['mobile'=>$phone,'code'=>$code,'code_time'=>time()]);
            if($res){
                return json(['status'=>'success','msg'=>'发送成功']);
            }else{
                return json(['status'=>'fail','msg'=>'发送失败']);
            }
        }else{
            return json(['status'=>'fail','msg'=>'发送失败']);
        }
    }

    //订单列表
    public function orderList(){
        $user_id = session('id');
        $orderList = Db::name('order o')
                    ->join('goods g','o.goods_id=g.id')
                    ->where([
                        ['o.user_id','=',$user_id],
                        ['o.status','<>',0]
                    ])
                    ->field('g.goods_name,
                                g.goods_subimg,
                                g.original_price,
                                o.id,
                                o.child_order_no,
                                o.goods_price,
                                o.goods_color,
                                o.goods_type,
                                o.goods_num,
                                o.goods_money,
                                o.status,
                                o.create_time'
                    )
                    ->paginate(5);
        $this->assign('data',$orderList);
        return $this->fetch();
    }

    //代付款
    public function waitePay(){
        return $this->fetch();
    }

    //申请退款
    public function callback($id){
//        $this->assign('id',$id);
        $order = Db::name('order o')
            ->join('shop_goods g','o.goods_id=g.id')
            ->field('g.goods_name,
            g.goods_subimg,
            g.original_price,
            o.id,
            o.child_order_no,
            o.goods_price,
            o.goods_color,
            o.goods_type,
            o.goods_num,
            o.goods_money,
            o.status,
            o.create_time')
            ->where('o.id',$id)
            ->find();
        $this->assign('data',$order);
        return $this->fetch();
    }
    public function backPay(Request $request){
        $data = $request->except('/web/user/backpay');
        $data['status'] = 5;
        $res = Db::name('order')->where('id',$data['id'])->update($data);
        if($res){
            return json(['status'=>'success','msg'=>'退款申请已经受理！']);
        }else{
            return json(['status'=>'fail','msg'=>'系统繁忙稍后再试！']);
        }
    }
    public function cancel(){
        $id = request()->param('id');
        $track = Db::name('order')->where('id',$id)->value('track_name');
        if($track){
            $res = Db::name('order')->where('id',$id)->update(['status'=>2]);
        }else{
            $res = Db::name('order')->where('id',$id)->update(['status'=>1]);
        }
        if($res){
            return json(['status'=>'success','msg'=>'取消退款申请成功！']);
        }else{
            return json(['status'=>'fail','msg'=>'系统繁忙稍后再试！']);
        }
    }
}
