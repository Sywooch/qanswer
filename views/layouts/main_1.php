<?php
use app\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Url;
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo !empty($this->title) ? Html::encode($this->title)." - ".Yii::$app->name : Yii::$app->name; ?></title>
    <?php $this->head() ?>
</head>

<body>
<?php $this->beginBody() ?>  
    <div class="container">
        <div id="header">
            <?php
                NavBar::begin([
                    'brandLabel' => 'My Company',
                    'brandUrl' => Yii::$app->homeUrl,
                    'options' => [
                        'class' => 'navbar-inverse navbar-fixed-top',
                    ],
                ]);
                echo Nav::widget([
                    'options' => ['class' => 'navbar-nav navbar-right'],
                    'items' => [
                        array('label'=>'首页', 'url'=>array('/index/index'),'active'=>Yii::$app->controller->id=='index'),
                        array('label'=>'问题', 'url'=>array('/questions/index'),'active'=>Yii::$app->controller->id=='questions'),
                        array('label'=>'标签', 'url'=>array('/tags/index'),'active'=>Yii::$app->controller->id=='tags'),
                        array('label'=>'用户', 'url'=>array('/users/index'),'active'=>Yii::$app->controller->id=='users'),
                        array('label'=>'徽章', 'url'=>array('/badges/index'),'active'=>Yii::$app->controller->id=='badges'),
                        array('label'=>'等待回答', 'url'=>array('unanswered/index'),'active'=>Yii::$app->controller->id=='unanswered'),
                        Yii::$app->user->isGuest ?
                            ['label' => 'Login', 'url' => ['user/user/login']] :
                            ['label' => 'Logout (' . Yii::$app->user->identity->username . ')',
                                'url' => ['/site/logout'],
                                'linkOptions' => ['data-method' => 'post']],
                    ],
                ]);
                NavBar::end();
            ?>

            <div class="nav askquestion">
                <ul>
                    <li><?php echo Html::a('提问',array('questions/ask'),array('id'=>'nav-askquestion'));?></li>
                </ul>
            </div>
		</div>
    </div>
	<div class="container">
		<?php echo $content; ?>
	</div>
	<div id="footer">
		<div class="footerwrap">
			<div id="footer-menu">
				<a href="<?php echo Url::to('about/index');?>">关于</a>
				|
				<a href="http://blog.ilewen.com">博客</a>
				|
				<a href="mailto:services@ilewen.com">联系我们</a>				
			</div>
			<div id="copy">
			&copy; <?php echo date('Y'); ?> 乐问&nbsp;&nbsp;
				本站内容采用&nbsp;<a href="http://creativecommons.org/licenses/by-sa/2.5/cn/" rel="license"><img src="http://i.creativecommons.org/l/by-sa/2.5/cn/80x15.png" class="cc-icon" alt="知识共享许可协议"> <strong>知识共享署名-相同方式共享 2.5 中国大陆许可协议</strong></a>
			 &nbsp;&nbsp;<?php // echo $this->options['miibeian'];?>
			</div>
			<span class="right"><?php // echo Yii::$app->params['version']['release'];?></span>
		</div>

	</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>