<div id="mainbar">
	<div class="subheader">
		<h1 id="user-displayname"><?php echo Yii::t('global','messages');?></h1>
		<?php
		$menu = array(
			'items'=>array(
				array('label'=>'未读', 'url'=>array('users/inbox','tab'=>'unread'),'itemOptions'=>array('title'=>'未读')),
				array('label'=>'所有', 'url'=>array('users/inbox','tab'=>'all'),'itemOptions'=>array('title'=>'所有')),
			),
			'id'	=> 'tabs'
		);
		if (!isset($_GET['tab']) || $_GET['tab']=='unread') {
			$menu['items']['0']['itemOptions']['class'] = 'youarehere';
		}
		$this->widget('application.components.Mem4kMenu',$menu);
		?>
	</div>
	<div id="questions">
		<?php foreach ($list as $item):?>
		<?php echo $this->renderPartial('inbox-'.$item->type,array('item'=>$item));?>
		<?php endforeach;?>
	</div>
	<div class="cbt"></div>
	<?php
    if (isset($pages)) {
        $this->widget('MLinkPager', array(
            'pages' => $pages,
            'cssFile'=>false,
        ));
    }
	?>
</div>