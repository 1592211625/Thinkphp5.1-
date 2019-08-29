<?php
namespace app\index\model;

use think\Db;
use think\Model;

class Img extends Model{
    static function banner(){
        return Db::table('img')->where('type',0)->select();
    }
}
