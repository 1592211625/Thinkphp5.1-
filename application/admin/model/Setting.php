<?php
namespace app\admin\model;

use think\Db;
use think\Model;

class Setting extends Model{
    static function getSetting(){
        return Db::name('setting')->find();
    }
    static function editStting($data){
        return Db::name('setting')->where('id', 1)->update($data);
    }
}
