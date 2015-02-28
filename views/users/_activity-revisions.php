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
        <span style="color: maroon;"><?php echo $activity->cntype; ?></span>
    </td>
<td id="enable-load-revision-<?php echo $activity->data['rid']; ?>" class="async-load load-prepped">
    &nbsp;
    <b>
        <?php
        if ($activity->data['idtype'] == 'answer') {
            echo Html::a(Html::encode($activity->data['qtitle']), $activity->url . '#' . $activity->typeid, array('class' => 'answer-hyperlink timeline-answers', 'title' => $activity->data['qtitle']));
        } else {
            echo Html::a(Html::encode($activity->data['qtitle']), $activity->url, array('class' => 'question-hyperlink timeline-answers', 'title' => $activity->data['qtitle']));
        }
        ?>
    </b>
    <br>
<span class="revision-comment"><?php echo $activity->data['comment']; ?></span>
</td>
</tr>
