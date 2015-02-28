<style type="text/css">
        .user-info
        {
    	    clear:both;
    	    height: 85px;
            width: 230px;
            margin: 8px 5px;
        }
        .user-info .user-gravatar48
        {
            float: left;
            width: 48px;
            height: 48px;
        }
        .user-info .reputation-score {
            font-size: 110%;
            margin-right:0px;
        }
        .user-info .user-tags {
            clear: both;
        }
        .subtabs a { font-size: 100%; }
        .no-search-results { font-weight: bold; font-size: 120%; padding: 20px; }

        .user-info
        {
            overflow:hidden;
        }
        #user-browser
        {
            margin-left:5px !important;
            margin-top:-15px;
        }
        .user-tags, .user-tags a
        {
            color: #888;
        }
        .user-tags
        {
            font-family: Arial,Liberation Sans,DejaVu Sans,sans-serif;
            font-size: 13px;
            margin-left: 53px;
        }
        .user-details
        {
            width: 175px !important;
        }
        #user-browser table td { vertical-align:top; }

</style>
<?php $this->title = $this->context->title;?>
<div id="mainbar-full">
    <div class="subheader">
        <h1 id="h-users">用户列表</h1>
		<?= \yii\widgets\Menu::widget($menu); ?>
    </div>

	<div style="padding-bottom: 10px;" class="page-description">
	    <table style="width: 100%;">
	        <tbody>
				<tr>
					<td>
						查找:<input type="text" style="margin-left: 10px;" value="" class="userfilter" name="userfilter" id="userfilter">
		            </td>
		            <td style="text-align: right;">
                        <?= \yii\widgets\Menu::widget($submenu); ?>
					</td>
				</tr>
			</tbody>
	    </table>
	</div>

	<div id="user-browser">
	    <table>
	        <tbody>
	        	<tr>
	        		<?php
                    $templates = [
                        'voters' => "_index-votes",
                        'editors' => "_index-edits",
                        'newusers' => "_index-newusers",
                        'reputation' => "_index-reputations",
                    ];
        			switch($tab) {
        				case 'voters':
							$template = "_index-votes";
        					break;
        				case 'editors':
							$template = "_index-edits";
        					break;
        				case 'newusers':
        					$template = "_index-newusers";
        					break;
        				case 'reputation' :
        				default:
        					$template = "_index-reputations";
        					break;
        			}
	        		?>
	        		<?php foreach($users as $i=>$v):?>
	            		<?php echo $this->render($template,array('user'=>$v,'params' => $params));?>
		            	<?php if (($i+1) % 4==0):?>
		            		<?php echo "</tr><tr>";?>
		            	<?php endif;?>
					<?php endforeach;?>
				</tr>
			</tbody>
		</table>
		<div class="pager fr">
			<?= yii\widgets\LinkPager::widget(['pagination' => $pages]);?>
		</div>
	</div>

</div>
