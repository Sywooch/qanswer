<?php
use yii\helpers\Url;
use app\components\Formatter;
use app\components\String;
?>
<div class="owner-revision">
	<div onclick="toggleRev('<?= $revision->id;?>')"	class="revcell1 vm pull-left">
		<span title="显示/隐藏 正文" class="expander-arrow-show" id="rev-arrow-<?= $revision->id;?>">
			<a href="<?= Url::to('revisions/view', ['id'=>$revision->id]);?>"></a>
		</span>
	</div>
	<div onclick="toggleRev('<?= $revision->id;?>')"	class="revcell2 vm pull-left"><span title="版本 <?= $num;?>"><?= $num;?></span></div>
	<div class="revcell3 vm pull-left">
		<span class="revision-comment"><?php echo $revision->comment;?></span>
		<div style="padding-top: 10px;">
            <?= \yii\helpers\Html::a("显示源码", ['revisions/source','id'=>$revision->id], ['title'=>"显示该版本原始内容",'target'=>'_blank']);?>
		</div>
	</div>
	<div class="revcell4 pull-left">
		<div class="user-info">
			<div class="user-action-time">编辑 <span class="relativetime" title="<?= Formatter::time($revision->revtime);?>"><?= Formatter::ago($revision->revtime);?></span></div>
			<?= $this->render('/common/_user', ['user'=>$revision->author]);?>
		</div>
	</div>
</div>
<div style="display: block;" class="revcell5" id="rev-<?php echo $revision->id;?>">
    <div class="post-text">
    <?php echo String::markdownToHtml($revision->text);?>
    </div>
</div>
