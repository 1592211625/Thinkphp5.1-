<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function p ($data){
    echo "<pre style='color: #ff3f3f;font-size: 20px;font-family:Latha;line-height: 40px;letter-spacing: 5px'>";
    var_dump($data);
    echo "</pre>";
    die;
}
function e($data){
    echo "<pre style='color:#ff8349;font-size: 20px;font-family:Latha;letter-spacing: 5px'>";
    echo $data;
    echo "</pre>";
}

function getTree($data,$id=0,$level=0){
    $list = array();
    foreach ($data as $k=>$v){
        if($v['pid']==$id){
            $v['level']=$level;
            $v['child']=getTree($data,$v['id'],$level+1);
            $list[]=$v;
        }
    }
    return $list;
}

