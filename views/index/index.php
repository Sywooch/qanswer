<?php
use yii\helpers\Url;
?>
<div id="mainbar">
	<div class="subheader">
        <h1 id="h-all-questions">问题列表</h1>
		<?php
		if (!isset($_GET['tab'])) {
			$_GET['tab'] = 'hot';
		}
		$submenu = array(
			'items'=>array(
				array('label'=>'推荐', 'url'=>array('index/index','tab'=>'interesting'),'itemOptions'=>array('title'=>'按时间排序'),'visible'=>!Yii::$app->user->isGuest),
				array('label'=>'悬赏', 'url'=>array('index/index','tab'=>'bounty'),'itemOptions'=>array('title'=>'按悬赏排序')),
				array('label'=>'热门', 'url'=>array('index/index','tab'=>'hot'),'itemOptions'=>array('title'=>'热门')),
				array('label'=>'本周热门', 'url'=>array('index/index','tab'=>'week'),'itemOptions'=>array('title'=>'本周热门')),
				array('label'=>'本月热门', 'url'=>array('index/index','tab'=>'month'),'itemOptions'=>array('title'=>'本月热门')),
			),
			'id'	=> 'tabs'
		);
        echo \yii\widgets\Menu::widget($submenu);
//		$this->widget('application.components.Mem4kMenu',$subme nu);
		?>
    </div>
	<div id="questions">
		<?php
		foreach ($questions as $item) {
			$this->render('_question',array('data'=>$item));
		}
		?>
	</div>
	<div class="cbt"></div>
	<?php 
//    $this->widget('MLinkPager', array(
//	    'pages' => $pages,
//		'cssFile'=>false,
//	))
	?>
	<h2 class="bottom-notice">
        阅读更多问题？浏览<a href="<?php echo Url::to('questions/index');?>">全部问题列表</a>，或
        <a href="<?php echo Url::to('tags/index');?>">流行标签</a>。帮助我们回答问题
        <a href="<?php echo Url::to('unanswered/index');?>">等待回答</a>。
	</h2>
</div>
<div id="sidebar">
	<?php $this->render('/common/_sidebar_adv',array('position'=>'index.side.1'))?>
	<?php $this->render('/common/_sidebar_tags');?>
	<?php // $this->widget('RepTops'); ?>
</div>