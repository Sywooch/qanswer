<?php
use yii\helpers\Html;

$this->title = $tag->name. "：专家用户";
$this->registerMetaTag(['name' => 'description', 'content' => $tag->name."专家用户列表"], 'description');
?>
<div id="mainbar-full">
    <div class="subheader">
		<h1>标签：<?php echo $tag->name;?></h1>
		<?= \yii\widgets\Menu::widget($submenu);?>
    </div>
</div>

<div id="questions">
	<div class="content-inside">
		<h2>
		<?php echo Html::a($tag->name, array('questions/tagged','tag'=>$tag->name),array('class'=>'post-tag','rel'=>'tag','title'=>""));?>&nbsp;&nbsp;问题</h2>
		<table>
			<tbody>
				<tr>
					<td>
						<div class="summarycount ar"><?php echo $questionsCount['week'];?></div>
					</td>
					<td style="padding-left: 10px;" class="summary-value">
						<h1>最近7天</h1>
					</td>
				</tr>
				<tr>
					<td>
						<div class="summarycount ar"><?php echo $questionsCount['month'];?></div>
					</td>
					<td style="padding-left: 10px;" class="summary-value">
						<h1>最近30天</h1>
					</td>
				</tr>
				<tr>
					<td>
						<div class="summarycount ar"><?php echo $questionsCount['all'];?></div>
					</td>
					<td style="padding-left: 10px;" class="summary-value">
						<h1>全部</h1>
					</td>
				</tr>
			</tbody>
		</table>
		<p></p>
		<h2><?php echo Html::a($tag->name, array('questions/tagged','tag'=>$tag->name),array('class'=>'post-tag','rel'=>'tag','title'=>""));?>  回答者</h2>
		<div class="fl" style="width: 44%;">
			<h2>最近30天</h2>
			<table>
				<tbody>
					<?= $this->render('_top_answerer',array('users'=>$answerersMonth,'tag'=>$tag));?>
				</tbody>
			</table>
		    <p>
		    </p>
		</div>
		<div class="fl" style="width: 44%;">
			<h2>全部</h2>
			<table>
				<tbody>
					<?= $this->render('_top_answerer',array('users'=>$answerersAll,'tag'=>$tag));?>
				</tbody>
			</table>
			<p></p>
		</div>

		<h2 class="cbt"><?php echo Html::a($tag->name, array('questions/tagged','tag'=>$tag->name),array('class'=>'post-tag','rel'=>'tag','title'=>""));?>  提问者</h2>
		<div class="fl" style="width: 44%;">
			<h2>最近30天</h2>
			<table>
				<tbody>
					<?= $this->render('_top_asker',array('users'=>$usersMonth,'tag'=>$tag));?>
				</tbody>
			</table>
			<p></p>
		</div>
		<div class="fl" style="width: 44%;">
		    <h2>全部</h2>
			<table>
				<tbody>
					<?= $this->render('_top_asker',array('users'=>$usersAll,'tag'=>$tag));?>
				</tbody>
			</table>
		    <p></p>
		</div>
		<p style="color: rgb(153, 153, 153);">
			只统计了非WIKI问题和回答 (每日更新)
		</p>
	</div>
</div>

<div id="sidebar">
	<div class="module">
		<div class="summarycount al"><?php echo $tag->frequency;?></div>
		<p>问题使用了该标签</p>
		<div class="tagged">
			<?= Html::a($tag->name, array('questions/tagged','tag'=>$tag->name),array('class'=>'post-tag','rel'=>'tag','title'=>"显示包含该标签的问题"));?>
	    </div>
	</div>
</div>
