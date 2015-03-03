<?php
use yii\helpers\Html;
$this->title = "徽章";
?>
<div class="row">
    <div id="mainbar" class="col-md-9">
        <div class="subheader">
            <h1 id="h-badges">徽章</h1>
        </div>

        <div class="page-description">
            <p>
                当你在<?php echo Yii::$app->params['sitename'];?>上提问和回答的时候，你可以获得这些徽章，这些徽章将出现在你的个人首页和用户卡里</p>
        </div>
        <div>
            <table>
                <tbody>
                    <?php foreach ($badges as $badge):?>
                    <tr>
                        <td class="check-cell">
                            <!-- <span class="badge-earned-check" title="you’ve earned this badge"></span> -->
                        </td>
                        <td class="badge-cell">
                            <?php
                            $type = ($badge->type==1)?"金":(($badge->type==2)?"银":"铜")."徽章:";
                            $class = ($badge->type==1)?"badge1":(($badge->type==2)?"badge2":"badge3");
                            echo Html::a(Html::tag("span",'',array('class'=>$class))." ".$badge->name, array('badges/view','id'=>$badge->id),array('class'=>'badge','title'=>$type.$badge->description)); ?>
                            <span class="item-multiplier">×&nbsp;<?php echo $badge->awardcount;?></span>
                        </td>
                        <td>
                        <?php echo $badge->description;?>
                        </td>
                    </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
        </div>
    </div>
    <div id="sidebar" class="col-md-3">
        <div class="module" id="badges-legend-module">
            <h4 id="h-legend">说明</h4>
            <div id="badge-legend">
                <div class="mb">
                    <a style="cursor: default;" title="金质徽章" class="badge"><span class="badge1"></span>&nbsp;金质徽章</a>
                </div>
                <p>金质徽章很少，你需要很努力才能获得，它意味着一种成就！</p>
                <div class="mb">
                    <a style="cursor: default;" title="银质徽章" class="badge"><span class="badge2"></span>&nbsp;银质徽章</a>
                </div>
                <p>银质徽章是对长期贡献的一种奖赏，它非同寻常，但是只要你感兴趣就肯定可以获得</p>
                <div class="mb">
                    <a style="cursor: default;" title="铜质徽章" class="badge"><span class="badge3"></span>&nbsp;铜质徽章</a>
                </div>
                <p>铜质徽章是对“<?php echo Yii::$app->params['sitename'];?>”一些基本功能使用后的奖赏，它比较容易获得</p>
            </div>
        </div>
    </div>
</div>

