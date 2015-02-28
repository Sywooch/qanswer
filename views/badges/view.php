<?php
use yii\helpers\Html;
?>
<div id="mainbar-full">
    <div class="subheader">
		<h1>徽章</h1>
    </div>
    <table class="mb">
        <tbody><tr>
            <td>
               <?php
					$type = ($badge->type==1)?"金":(($badge->type==2)?"银":"铜")."徽章:";
					$class = ($badge->type==1)?"badge1":(($badge->type==2)?"badge2":"badge3");
					echo Html::a(Html::tag("span",'',array('class'=>$class))." ".$badge->name, array('badges/view','id'=>$badge->id),array('class'=>'badge','title'=>$type.$badge->description));
				?>
            </td>
            <td class="pl">
                <?php echo $badge->description;?>
            </td>
        </tr>
    </tbody></table>

    <table class="mb">
        <tbody>
        	<tr>
	            <td>
	                <div class="summarycount ar"><?php echo $badge->awardcount;?></div>
	            </td>
	            <td class="pl">
	                <h1>用户获得了该徽章，最近授予:</h1>
	            </td>
        	</tr>
		</tbody>
    </table>

    <div class="mb post-badge">
    	<?php foreach ($awards as $award):?>
		<div class="user-list">
			<?php echo Html::a($award->user->username, array('users/view','id'=>$award->user->id)); ?>
			<span title="威望" class="reputation-score"><?php echo $award->user->reputation;?></span>
			<?php
			if ($award->user->gold >= 0) {
				echo '<span title="'.$award->user->gold.' 金徽章 "><span class="badge1"></span><span class="badgecount">'.$award->user->gold.'</span></span>';
			}
			if ($award->user->silver>=0) {
				echo '<span title="'.$award->user->silver.' 银徽章 "><span class="badge2"></span><span class="badgecount">'.$award->user->silver.'</span></span>';
			}
			if ($award->user->bronze>=0) {
				echo '<span title="'.$award->user->bronze.' 铜徽章 "><span class="badge3"></span><span class="badgecount">'.$award->user->bronze.'</span></span>';
			}
			?>
		</div>
		<?php endforeach;?>
    </div>
	<?php 
//    $this->widget('MLinkPager', array(
//	    'pages' => $pages,
//		'cssFile'=>false,
//	))
	?>
</div>


</div>