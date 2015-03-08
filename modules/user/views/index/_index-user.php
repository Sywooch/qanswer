<?php
use yii\helpers\Url;
use yii\helpers\Html;
/** @var app\modules\user\models\User $user */
?>
<div class="user-info">
    <div class="user-gravatar48">
        <a href="<?php echo Url::to(['view/index','id'=>$user->id]);?>">
            <img width="48" height="48" class="logo" alt="" src="<?php echo $user->middleavatar;?>">
        </a>
    </div>
    <div class="user-details">
        <?php echo Html::a($user->username, array('view/index','id'=>$user->id),array('title'=>$user->username));?>
        <br />
        <span class="user-location"><?php echo $user->profile->location;?></span>
        <br />
         <?php
        switch($tab) {
            case 'voters':
                $template = $user->getVotesByFilter($params['filter']);
                break;
            case 'editors':
                $template = $user->getEditsByFilter($params['filter']);
                break;
            case 'newusers':
                $template = \yii\helpers\Html::tag('span', $user->reputation, ['class' => 'reputation-score','title' => '总威望']);
                break;
            case 'reputation' :
            default:
                $reputation = $user->getReputationByFilter($params['filter']);
                $template = \yii\helpers\Html::tag('span', $reputation['reputationCount'], ['class' => 'reputation-score','title' => $reputation['title']]);
                break;
        }
        ?>
        <?= $template;?>
    </div>
</div>