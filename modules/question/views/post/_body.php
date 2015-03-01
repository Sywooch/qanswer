<div class="post-text">
<?php
	 //$parser=new CMarkdownParser;
	//echo ($contentDisplay=$parser->safeTransform($data->content));
	$this->beginWidget('CMarkdown', array('purifyOutput'=>true));
	echo $data->content;;
	$this->endWidget();
?>
</div>