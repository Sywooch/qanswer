<?php

use yii\helpers\Url;
use app\components\Formatter;
use app\models\Badge;
?>
<tr>
    <td style="width:70px">
        <div class="date">
            <div title="<?php echo Formatter::time($activity->time); ?>" class="date_brick"><?php echo Formatter::month($activity->time); ?></div>
        </div>
    </td>
    <td>
    <?php echo $activity->cntype; ?>
    </td>
    <td>
        <?php $badge = Badge::getBadge($activity->typeid); ?>
        <a class="badge" title="<?php echo $badge->typename; ?>: <?php echo $badge->description; ?>" href="<?php echo Url::to('badges/view', array('id' => $badge->id, 'uid' => $activity->uid)); ?>">
            <span class="<?php echo $badge->classname; ?>"></span>&nbsp;<?php echo $badge->name; ?>
        </a>
    </td>
</tr>