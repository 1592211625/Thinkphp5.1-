<?php
namespace app\index\controller;
use think\Request;
use think\facade;
use think\Controller;
use think\Db;
use PHPMailer\PHPMailer;
class Forget extends Controller
{

    //获取发送邮箱验证码页面
    public function index(){
        return $this->fetch();
    }


    //发送验证码到邮箱
    public function sendEmail(Request $request){
        $data = $request->param();
        //验证用户传过来的账户存不存在
        $user = Db::name('user')->where('username',$data['mobile'])->find();
        $userId = $user['id'];
        if(!$userId){
            return json(['status'=>'fail','msg'=>'用户不存在，请核实后填写']);
        }

        //============执行发送邮件==========================
        //生成验证码
        $emailCode = mt_rand(1000,9999);
        $mail = new PHPMailer\PHPMailer();
        $mail->isSMTP(); // 启用SMTP
        $mail->Host = 'smtp.qq.com'; //SMTP服务器
        $mail->Port = 587;  //邮件发送端口   465 25 587
        $mail->SMTPAuth = true;  //启用SMTP身份验证认证
        $mail->SMTPSecure = "tls";   // 设置安全验证方式为ssl tls
        //$mail->SMTPDebug = 2;  //debug模式
        $mail->CharSet = "UTF-8"; //字符集
        $mail->Username = '1592211625@qq.com';  //发件人邮箱
        $mail->Password = 'thbubpefkdfjggdb';  //发件人密码 ==>授权码，非邮箱密码
        $mail->From = '1592211625@qq.com';  //发件人邮箱
        $mail->FromName = 'jyt';  //发件人姓名
        $mail->Subject = '来自jyt的邮箱验证'; //邮件标题
        $mail->Body = '您的验证码是：'.$emailCode;//邮件主体内容

        $mail->addAddress($data['email']); //添加收件人
        $mail->isHTML(true); //支持html格式内容

        //发送成功就删除
        if (!$mail->send()) {
            echo "Mailer Error: ".$mail->ErrorInfo;// 输出错误信息,用以邮件发送不成功问题排查
        }else{
            //将code存入session
            session('email_code',$emailCode);
            session('userphone',$user['username']);
            return json(['status'=>'success','msg'=>'邮件发送成功，请立即验证']);
        }
    }

    //调用邮箱验证码填写页面
    public function check(){

        return $this->fetch();
    }

    //验证用户邮箱验证码
    public function doCheck(Request $request){
        $code = $request->param('code');

        if(session('email_code') != $code){
            return json(['status'=>'fail','msg'=>'邮箱验证码输入有误，请核实后填写']);
        }
        return json(['status'=>'success','msg'=>'验证成功，请立即重置密码！']);
    }
    
    //获取重置密码页面
    public function unsetPwd(){
        return $this->fetch('set');
    }


    //处理重置密码
    public function doUnsetPwd(Request $request){
        $code = $request->param('code');
        if(session('email_code') != $code){
            return json(['status'=>'fail','msg'=>'邮箱验证码输入有误，请核实后填写']);
        }
        return json(['status'=>'success','msg'=>'验证成功，请立即重置密码！']);
    }

    public function set(Request $request){
        $data = $request->except(["/user/set","mail"]);
        $username = session('userphone');
        $data['salt'] = mt_rand(1000,9999);
        $salt = $data['salt'];
        $data['newPassword'] = md5($data['newPassword'].$salt);
        $res = Db::name('user')
            ->where('username',$username)
            ->update(['userpwd' => $data['newPassword'],'salt'=>$salt]);
        if($res){
            return json(['status'=>'success','msg'=>'密码修改成功']);
        }else{
            return json(['status'=>'fail','msg'=>'密码修改失败']);
        }
    }
    

   

}
