<div class="favorites-table" id="favorites-table">
    <?= \yii\widgets\Menu::widget($submenu);?>
	<style type="text/css">
		.question-summary { width: 900px; }
		.narrow .summary { width: 700px; }
	</style>
	<?= $this->render('_question',array('favs'=>$favs)); ?>
    <?= yii\widgets\LinkPager::widget(['pagination' => $pages, 'options' => ['id' => 'favorite-pager', 'class' => 'pagination']]); ?>
</div>

<script type="text/javascript">
  var favoritesSortOrder = 'views';
  var favoritesPageSize = <?php echo Yii::$app->params['pages']['userFavoritePagesize'];?>;
  var userViewStartDate = '';
  var favsUrl = "<?php echo yii\helpers\Url::to(['users/stats','do'=>'favorites']);?>";
</script>

