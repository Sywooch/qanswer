<?php
$this->title = "编辑帖子";
?>

<h1>编辑 <i><?php echo $model->isQuestion() ? \yii\helpers\Html::encode($model->title) : '答案'; ?></i></h1>
<div class="row">
    <div class="col-md-9">
        <?php if ($model->isAnswer()):?>
            <?php echo $this->render('_answerform', array('model'=>$model));?>
        <?php else:?>
            <?php echo $this->render('_form', array('model'=>$model)); ?>
            <script type="text/javascript">
            $(function(){
                initFadingHelpText();
                moveScroller();
            });
            </script>
        <?php endif;?>
    </div>
    <div class="col-md-3">
	<?php $this->render('_ask-sidebar');?>
    </div>
</div>
    