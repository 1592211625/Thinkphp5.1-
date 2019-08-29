<?php
namespace app\admin\model;

use think\Model;
use think\Db;

class Img extends Model{
    static function findData(){
        return Db::name('img')->select();
    }
    static function addData($data){
        return Db::name('img')->insert($data);
    }
    static function modifySort($data){
        return Db::name('img')->update($data);
    }

}
