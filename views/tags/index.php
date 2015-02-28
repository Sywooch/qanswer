<?php
$this->title = '标签';
?>
<div id="mainbar-full">
    <div class="subheader">
        <h1 id="h-tags">标签</h1>
		<?php
		if (empty($_GET['tab'])) {
			$_GET['tab'] = 'popular';
		}
		$submenu = array(
			'items'=>array(
				array('label'=>'最流行', 'url'=>array('tags/index','tab'=>'popular'),'itemOptions'=>array('title'=>'最流行标签')),
				array('label'=>'最新', 'url'=>array('tags/index','tab'=>'newest'),'itemOptions'=>array('title'=>'最近创建的标签')),
			),
			'options' => ['id' => 'tabs', 'class' => 'nav nav-tabs']
		);
        echo \yii\widgets\Menu::widget($submenu);
		?>
    </div>
    <div class="page-description">
        <p>
        	标签是问题的关键字或者分类，方便其他用户通过标签能够更容易找到和回答你的问题。
        </p>
        <table>
            <tbody>
	            <tr>
	                <td>查找:</td>
	                <td style="padding-left: 5px;"><input type="text" name="tagfilter" id="tagfilter"></td>
	            </tr>
        	</tbody>
        </table>
    </div>
    <div id="tags_list">
        <table id="tags-browser">
            <tbody>
            	<tr>
            	<?php foreach($tags as $i=>$v):?>
            		<?php echo $this->render("_index_td",array('data'=>$v,'weeks'=>$weeks,'days'=>$days));?>
	            	<?php if (($i+1) % 5==0):?>
	            		<?php echo "</tr><tr>";?>
	            	<?php endif;?>
				<?php endforeach;?>
				</tr>
			</tbody>
		</table>
		<?= yii\widgets\LinkPager::widget(['pagination' => $pages]);?>
    </div>

    <script type="text/javascript">
	    $(function() {
            $("#tagfilter").focus().typeWatch({ highlight:true, wait:500, captureLength: -1, callback: finished });
        });

        function finished(txt) {
            $.ajax({
                type: "POST",
                url: iAsk.options.links.filterTagIndex,
                data: { filter: txt, tab: 'count',fkey:iAsk.options.user.fkey},
                dataType: "html",
                success: function (result) {
                    var domelement = $(result);
                    $("#tags_list").html(domelement);
                    $(".pager").hide();
                }
            });
        }
    </script>
</div>