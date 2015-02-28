<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $jsOptions = [
        'position' => \yii\web\View::POS_HEAD
    ];    
    public $css = [
        'css/main.css',
        'css/jquery.autocomplete.css',
        'css/prettify.css',
        'css/site.css',
        '/css/wmd.css',
    ];
    public $js = [
        'js/prettify.js',
        'js/tageditor.js',
        'js/jquery.typewatch.js',
        'js/common.js',
        'js/jquery.wmd.js',
        'js/autosave.js',
        'js/user-page.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\web\JqueryAsset',
    ];
}
