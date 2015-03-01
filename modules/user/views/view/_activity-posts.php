<?php

use yii\helpers\Html;
use app\components\Formatter;
?>
<tr>
<td style="width: 70px;">
    <div class="date">
        <div title="<?php echo Formatter::time($activity->time); ?>" class="date_brick"><?php echo Formatter::month($activity->time); ?></div>
    </div>
</td>
<td>
<?php if ($activity->type == 'answer'): ?>
    <span class="accept-answer-link"><?php echo $activity->cntype; ?></span>
<?php else: ?>
    <b><?php echo $activity->cntype; ?></b>
<?php endif; ?>
</td>
<td id="enable-load-body-<?php echo $activity->typeid; ?>" class="async-load load-prepped">
    <b>
        <?php
        if ($activity->type == 'answer') {
            echo Html::a(Html::encode($activity->data['qtitle']), $activity->url . '#' . $activity->typeid, array('class' => 'answer-hyperlink timeline-answers', 'title' => $activity->data['qtitle']));
        } else {
            echo Html::a(Html::encode($activity->data['qtitle']), $activity->url, array('class' => 'question-hyperlink timeline-answers', 'title' => $activity->data['qtitle']));
        }
        ?>
    </b>
</td>
</tr>