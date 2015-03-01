<?php
$this->breadcrumbs=array(
	'我要回答',
);
?>
<h1>我来回答</h1>

<?php echo $this->renderPartial('_answerform', array('model'=>$model)); ?>