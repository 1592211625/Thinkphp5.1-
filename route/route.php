<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

//---------------------------------前台------------------------------------
//注册
Route::get('/web/register',function (){
    return view('index@user/register');
});
//前台注册
Route::post('/web/toregister','index/user/toregister');
//注册获取验证码
Route::post('/web/getcode','index/user/getcode');
//登录
Route::get('/web/login',function (){
    return view('index@user/login');
});
//忘记密码发送邮件
Route::get('web/forget/index','index/forget/index');
//发送邮箱验证码
Route::post('web/forget/sendemail','index/forget/sendEmail');
//获取邮箱验证页面
Route::get('web/forget/check','index/forget/check');
//验证用户邮箱验证
Route::post('web/forget/docheck','index/forget/doCheck');
//获取重置密码表单页面
Route::get('web/forget/unset','index/forget/unsetPwd');
//处理重置密码
Route::post('web/forget/dounset','index/forget/doUnsetPwd');
//处理重置密码
Route::post('user/set','index/forget/set');


//前台登录验证
Route::post('/web/tologin','index/user/tologin');
//退出前台登录
Route::get('/web/loginout','index/user/loginout');

//首页
Route::get('/','index/index/index');
//列表页
Route::get('/web/goodlist/:id','index/goods/goodList');
//详情页
Route::get('/web/goods/:id','index/goods/detail');

//加入购物车
Route::get('/web/cart','index/cart/cart');
//购物车
Route::post('/web/cart/addcart','index/cart/addcart');
//删除购物车商品
Route::post('/web/cart/delcart','index/cart/delcart');
//改变购物车商品数量
Route::post('/web/cart/modcart','index/cart/modcart');

//付款
Route::post('/web/pay','index/order/pay');
//确认付款
Route::any('/web/topay/[:index]/[:type]','index/order/topay');

//支付宝异步回调地址
Route::post('/web/pay/notify','index/order/notify');
//支付宝同步回调地址
Route::get('/web/pay/return','index/order/returns');


//我的订单->头像
Route::get('/web/userpic/userpic','index/userpic/userpic');
//我的订单->更换头像
Route::any('/web/userpic/changePic','index/userpic/changePic');
//我的订单->头像上传
Route::post('/web/userpic/picUpload','index/userpic/picUpload');

//我的订单->地址
Route::get('/web/address/address','index/address/address');
//我的订单->添加地址
Route::post('/web/address/addAddress','index/address/addAddress');
//我的订单->删除地址
Route::any('/web/address/delAddress/:id','index/address/delAddress');

//我的订单->订单列表
Route::get('/web/user/orderlist','index/user/orderList');
//我的订单->查看物流
Route::post('/web/order/track','index/order/track');

//我的订单->代付款
Route::get('/web/user/waitepay','index/user/waitePay');

//我的订单->申请退款
Route::get('/web/user/callback/:id','index/user/callback');
//我的订单->退款处理
Route::post('/web/user/backpay','index/user/backpay');
//我的订单->取消退款
Route::post('/web/order/cancel','index/user/cancel');

//---------------------------------前台------------------------------------

//================================================================================================

//---------------------------------后台------------------------------------
//后台登录
Route::get('/admin/login',function (){
    return view('admin@admin/login');
});
//后台登录验证
Route::post('admin/tologin','admin/admin/tologin');
//后台登录验证码
Route::get('admin/captcha','admin/admin/captcha');
//退出后台
Route::get('admin/loginout','admin/admin/loginout');

//后台首页
Route::get('admin/index','admin/index/index');
//网站设置
Route::get('admin/edit','admin/index/edit');
//网站设置pro
Route::post('admin/editpro','admin/index/editpro');


//后台设置
Route::get('admin/system','admin/index/system');
//会员列表
Route::get('admin/user','admin/user/index');
//会员冻结设置
Route::post('admin/userpro','admin/user/userpro');
////图片列表
Route::resource('admin/image','admin/Image');
//图片上传
Route::post('admin/imgupload','admin/Image/imgupload');
//分类列表
Route::resource('admin/category','admin/category');

//商品列表
Route::resource('admin/goods','admin/goods');
//商品分类处理
Route::post('admin/cate','admin/goods/cate');
//上传商品图
Route::post('admin/imgupload1','admin/goods/imgupload1');
//上传商品颜色图
Route::post('admin/imgupload2','admin/goods/imgupload2');
//上传商品描述
Route::post('admin/imgupload3','admin/goods/imgupload3');
//跳转商品sku
Route::get('admin/sku/[:id]','admin/goods/sku');
//提交商品sku
Route::post('admin/addsku','admin/goods/addsku');
//查看商品sku
Route::get('admin/looksku/:id','admin/goods/looksku');
//修改商品sku
Route::get('admin/editsku/:id','admin/goods/editsku');
//修改商品skuPro
Route::post('admin/editskupro/:id','admin/goods/editskupro');

//订单列表
Route::resource('admin/order','admin/order');
//退款处理
Route::post('admin/order/cancel','admin/order/cancel');
//添加快递
Route::get('admin/order/track/:id','admin/order/track');
//提交快递
Route::post('admin/order/trackpro','admin/order/trackpro');
//---------------------------------后台------------------------------------

