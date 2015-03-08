<?php
use yii\helpers\Html;
?>
<div class="row">
    <?php foreach ($usertags as $i=>$usertag):?>
    <div class="col-md-3">
        <?= Html::a($usertag->tag, ['questions/tagged','tag'=>$usertag->tag], ['class'=>'post-tag','rel'=>'tag','title'=>"显示标签 ".$usertag->tag]); ?>
        <span class="item-multiplier">×&nbsp;<?php echo $usertag->totalcount;?></span>
    </div>
    <?php endforeach; ?>
</div>
<?= \yii\widgets\LinkPager::widget(['pagination' => $pages, 'options' => ['class' => 'pagination', 'id' => 'tags-pager']]);?>