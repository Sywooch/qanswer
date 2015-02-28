<div id="favorites-table" class="favorites-table">
	<?php
	$this->widget('application.components.Mem4kMenu',$submenu);
	?>
	<style type="text/css">
	    .question-summary { width: 900px; }
	    .narrow .summary { width: 700px; }
	</style>
	<?php $this->renderPartial('_question',array('favs'=>$favs)); ?>
</div>
<?php $this->widget('MLinkPager', array(
    'pages' => $pages,
	'cssFile'=>false,
	'id'	=> 'question-pager',
	'htmlOptions'=>array('class'=>"pages fr")
))
?>
<script type="text/javascript">
  var favoritesSortOrder = 'added';
  var favoritesPageSize = 15;
  var userId = 7;
  var favsUrl = "<?php echo yii\helpers\Url::to('users/stats',array('do'=>'favorites'));?>";
</script>