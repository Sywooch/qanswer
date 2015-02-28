<?php
namespace app\assets;

use yii\web\AssetBundle;

class EditorAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $jsOptions = [
        'position' => \yii\web\View::POS_HEAD
    ];    
    public $css = [
        '/css/wmd.css',
    ];
    public $js = [
        'js/jquery.wmd.js',
    ];
    public $depends = [
    ];
}
