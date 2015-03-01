<?php
$this->title = '提问';
?>
<h1>我要提问</h1>
<div id="mainbar" class="ask-mainbar">
	<script type="text/javascript">
	$(function(){
		initFadingHelpText();
		moveScroller();
	});
	</script>
	<?php echo $this->render('/post/_form', array('model'=>$model,'type'=>'ask')); ?>
</div>
<?php $this->render('/post/_ask-sidebar');?>