<?php

namespace app\admin\validate;

use think\Validate;

class Index extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
	    'web_title'=>'require|max:5',
	    'web_keywords'=>'require|max:20',
	    'web_description'=>'require|between:10,50',
	    'web_footer'=>'require|between:10,120',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'web_title.require'=>'标题不能为空',
        'web_keywords.require'=>'关键词不能为空',
        'web_description.require'=>'网站描述不能为空',
        'web_footer.require'=>'底部信息不能为空',
        'web_title.max'=>'标题最多5个字符',
        'web_keywords.max'=>'关键词最多20个字符',
        'web_description.between'=>'描述在10到50个字符',
        'web_footer.between'=>'底部信息在10到120个字符',
    ];

    protected $scene = [
        'edit'  =>  ['web_title','web_keywords'],
    ];
}
