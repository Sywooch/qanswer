<?php
use yii\helpers\Url;
use app\components\Formatter;
use app\components\String;
?>
<tr class="owner-revision">
	<td onclick="toggleRev('<?php echo $revision->id;?>')"	class="revcell1 vm">
		<span title="显示/隐藏 正文" class="expander-arrow-show" id="rev-arrow-<?php echo $revision->id;?>">
			<a href="<?php echo Url::to('revisions/view',array('id'=>$revision->id));?>"></a>
		</span>
	</td>
	<td onclick="toggleRev('<?php echo $revision->id;?>')"	class="revcell2 vm"><span title="版本 <?php echo $num;?>"><?php echo $num;?></span></td>
	<td class="revcell3 vm">
		<span class="revision-comment"><?php echo $revision->comment;?></span>
		<div style="padding-top: 10px;">
			<?php echo Url::to("显示源码", array('revisions/source','id'=>$revision->id),array('title'=>"显示该版本原始内容",'target'=>'_blank'));?>
		</div>
	</td>
	<td class="revcell4">
		<div class="user-info">
			<div class="user-action-time">编辑 <span class="relativetime" title="<?php echo Formatter::time($revision->revtime);?>"><?php echo Formatter::ago($revision->revtime);?></span>
			</div>
			<?php echo $this->render('/common/_user',array('user'=>$revision->author));?>
		</div>
	</td>
</tr>
<tr>
	<td colspan="4">
		<div style="display: block;" class="revcell5" id="rev-<?php echo $revision->id;?>">
			<h1></h1>
			<div class="post-text">
			<?php echo String::markdownToHtml($revision->text);?>
			</div>
			<table width="100%">
				<tbody>
					<tr>
						<td align="left" style="vertical-align: top; width: 800px;"></td>
					</tr>
				</tbody>
			</table>
		</div>
	</td>
</tr>
<tr id="spacer-<?php echo $revision->id;?>"><td height="10px" colspan="4"></td></tr>
