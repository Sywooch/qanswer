<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<td class="tag-cell">
<?php echo Html::a($data->name, array('questions/tagged', 'tag' => $data->name), array('title' => "查看包含该标签的问题", 'class' => "post-tag", 'rel' => 'tag')); ?>
<span class="item-multiplier">×&nbsp;<?php echo $data->frequency; ?></span>
<div class="excerpt"><?php echo isset($data->post) ? $data->post->excerpt : ""; ?></div>
<div>
    <div class="stats-row fl">
        <a href="<?php echo Url::to(['questions/tagged', 'tag' => $data->name, 'days' => 1]); ?>" title="最近24小时<?php echo isset($days[$data->name]) ? $days[$data->name] : 0; ?>个问题使用该标签">一天 <?php echo isset($days[$data->name]) ? $days[$data->name] : 0; ?>次提问</a>,
        <a href="<?php echo Url::to(['questions/tagged', 'tag' => $data->name, 'days' => 7]); ?>" title="最近7天<?php echo isset($weeks[$data->name]) ? $weeks[$data->name] : 0; ?>个问题使用该标签">一周  <?php echo isset($weeks[$data->name]) ? $weeks[$data->name] : 0; ?>次提问</a>
    </div>
    <div class="cbt"></div>
</div>
</td>