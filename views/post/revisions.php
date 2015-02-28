<?php
use yii\helpers\Html;
?>
<div id="mainbar-full">
	<div class="subheader">
		<h1>
		<?php
			if ($post->isQuestion())
				echo Html::a("回到问题", array('questions/view','id'=>$post->id),array('title'=>"回到问题",'class'=>"question-hyperlink"));
			elseif ($post->isTag())
				echo Html::a("回到标签：".$post->title, array('tags/view','tag'=>$post->title),array('title'=>"回到标签",'class'=>"question-hyperlink"));
			elseif ($post->isAnswer())
				echo Html::a("回到回答", array('questions/view','id'=>$post->idv,'#'=>$post->id),array('title'=>"回到回答",'class'=>"question-hyperlink"));
		?>
		</h1>
	</div>

	<div id="revisions">
		<table>
			<tbody>
				<?php
				$total = count($revisions);
				foreach($revisions as $num=>$revision){
					echo $this->render('_revision',array(
						'revision'=>$revision,
						'num'	  =>$total - $num,
					));
				}
				?>
            </tbody>
		</table>

	</div>
</div>

<script type="text/javascript">
	function toggleRev(id) {
		var arrow = $("#rev-arrow-" + id);
		var uri = arrow.children('a').attr('href');
		var visible = arrow.attr("class").indexOf("show") > -1;
		arrow.attr("class", "expander-arrow-" + (visible ? "hide" : "show"));
		if (visible) {
			$("#rev-" + id).hide();
		} else {
			var revDiv = $("#rev-" + id);
			revDiv.show();
//			master.addSpinner(revDiv);
			$.get(uri, function (data) {
		 		revDiv.html(data);
//				master.removeSpinner();
			});
		}
	}
</script>