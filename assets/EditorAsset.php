<?php
namespace app\assets;

use yii\web\AssetBundle;

class EditorAsset extends AssetBundle
{
    public $sourcePath = "@bower/editor.md";
    public $jsOptions = [
        'position' => \yii\web\View::POS_HEAD
    ];    
    public $css = [
        'dist/css/editormd.css',
    ];
    public $js = [
        'dist/js/editormd.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
