<style type="text/css">

</style>
<?php $this->title = $this->context->title;?>
<div id="mainbar-full">
    <div class="subheader">
        <h1 id="h-users">用户列表</h1>
		<?= \yii\widgets\Menu::widget($menu); ?>
    </div>
    <div class="row row-space-4">
        <div class="col-md-6">
            查找:<input type="text" style="margin-left: 10px;" value="" class="userfilter" name="userfilter" id="userfilter">
        </div>
        <div class="col-md-6">
            <?= \yii\widgets\Menu::widget($submenu); ?>
        </div>
    </div>

	<div id="user-browser">
        <div class="row">
            <?php foreach($users as $i=>$user):?>
            <div class="col-lg-2 col-md-3 col-xs-6">
                <?php echo $this->render("_index-user",array('user'=>$user,'params' => $params, 'tab' => $tab));?>
            </div>
            <?php endforeach;?>
        </div>
		<?= yii\widgets\LinkPager::widget(['pagination' => $pages, 'options' => ['class' => 'pagination']]);?>
	</div>

</div>
