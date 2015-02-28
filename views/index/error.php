<?php
$this->pageTitle='Error';
?>

<h2>Error <?php echo $code; ?></h2>

<div class="error">
<?php echo CHtml::encode($message); ?>
</div>
<div style="margin-top:20px;">
	<p>返回到：</p>
	<ul>
		<li><?php echo CHtml::link('首页',$this->createUrl('index/index'));?></li>
		<li><?php echo CHtml::link('最新问题',$this->createUrl('questions/index'));?></li>
	</ul>

</div>