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
        		switch($_GET['tab']) {
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
        		<?php foreach($users as $i=>$user):?>
            		<?php echo $this->render($template,array('user'=>$user));?>
	            	<?php if ($i % 4==0 && $i>0):?>
	            		<?php echo "</tr><tr>";?>
	            	<?php endif;?>
				<?php endforeach;?>
			</tr>
		</tbody>
	</table>
	<div class="pager fr">
        <?= yii\widgets\LinkPager::widget(['pagination' => $pages]); ?>
	</div>
</div>
