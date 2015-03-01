<?php
use app\components\Formatter;
?>
<div id="rep-page-container">
	<table style="margin-left: -50px; width: 100%;" class="summary-title">
		<tbody>
			<tr>
		        <td style="width: 100%;">
		            <div class="subtabs" id="tabs-reputation">
						<a title="reputation by post" href="/users/rep/show?page=1&amp;pagesize=30&amp;userid=29407&amp;sort=post&amp;startdate=2011-03-15" class="youarehere">by post</a>
						<a title="reputation by time of day" href="/users/rep/show?page=1&amp;pagesize=30&amp;userid=29407&amp;sort=time&amp;startdate=2011-03-15">by time</a>
					</div>
		    	</td>
			</tr>
		</tbody>
	</table>
	<table class="rep-table">
		<tbody>
			<?php foreach ($reputes as $date=>$repute):?>
			<tr class="rep-table-row">
	            <td class="rep-cell">
	                <span class="rep-up"><?php echo $repute['total']; ?></span>
	            </td>

	            <td title="2011-03-16" data-load-url="/users/rep-day/29407/1300233600?sort=post&amp;StartDate=2011-03-15" class="rep-day ">
	                <a class="load-body expander-arrow-small-hide hide-body expander-arrow-small-show" style=""></a>
	                <?php echo $date;?>
	            </td>
	        </tr>
            <tr class="loaded-body">
                <td style="height: 0px; padding: 0px;" class="body-container body-loaded" colspan="2">
	                <div style="display: block;">
						<div class="rep-breakdown">
							<table class="tbl-reputation">
							    <tbody>
							    	<?php foreach ($repute['list'] as $item):?>
							        <tr class="rep-breakdown-row rep-recent-row expandable-row">
							            <td class="rep-left">
							                <span class="rep-up"><?php echo $item->reputation;?></span>
							            </td>
							            <td title="<?php echo Formatter::time($item->time);?>" class="rep-time">
										<?php echo Formatter::ago($item->time);?>
										</td>
						                <td class="rep-desc"><?php echo $item->lng;?></td>
						                <td data-load-url="/users/rep-post/29407/5325085/1300279703/1300281687" class="rep-link async-load load-prepped">
						                	<!-- <a style="" class="load-body expander-arrow-small-hide"></a>&nbsp; -->
						                	<?php echo yii\helpers\Html::a(yii\helpers\Html::encode($item->question->title), array('questions/view','id'=>$item->question->id),array('class'=>"answer-hyperlink"));?>
						                </td>
							        </tr>
							        <tr class="loaded-body">
							        	<td colspan="4" class="body-container" style="padding: 0px;"></td>
							        </tr>
							        <?php endforeach;?>
							    </tbody>
							</table>
						</div>
						<script type="text/javascript">
						    $(function () {
						        expandPostBody('.tbl-reputation td.rep-link.async-load:not(.load-prepped)', null, null, 4);
						    });
						</script>
	                </div>
            	</td>
            </tr>
			<?php endforeach;?>
	    </tbody>
	</table>
    <?= yii\widgets\LinkPager::widget(['pagination' => $pages, 'options' => ['id'=>"reputation-pager", 'class' => 'pagination']]); ?>
	<script type="text/javascript">
	    var reputationView = 'post';
	    var reputationPageSize = 30;

	    $(function () {
	        expandPostBody('.rep-table td.async-load:not(.load-prepped)', '', null, 2);
	        if(!$('.rep-day .expander-arrow-small-hide').length) $(".rep-table .load-body").slice(0, 3).click();
	    });
	</script>
</div>