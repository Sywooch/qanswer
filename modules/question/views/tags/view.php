<?php
use yii\helpers\Html;

$this->title = $tag->name;
$this->registerMetaTag(['name' => 'description', 'content' => $tag->post->excerpt], 'description');
?>

    <div id="mainbar-full">
        <div class="subheader">
            <h1>标签：<?php echo $tag->name;?></h1>
            <?= \yii\widgets\Menu::widget($submenu);?>
        </div>
    </div>
    <div class="row">
        <div id="questions" class="col-md-9">
            <div class="content-inside">
                <h2>关于 <?php echo Html::a($tag->name, array('questions/tagged','tag'=>$tag->name),array('rel'=>'tag','class'=>'post-tag','title'=>"该标签相关问题"));?></h2>
                <div class="post-text">
                    <?php echo isset($tag->post) ? $tag->post->content : "";?>
                </div>

                <div class="post-menu">
                    <form class="form-submit" action="#" method="get">
                        <input type="button" onclick="window.location.href='<?php echo yii\helpers\Url::to(['tags/edit','id'=>$tag->id]);?>'" value="编辑标签" />
                    </form>
                    <?php echo Html::a("版本历史", array('post/revisions','id'=>$tag->postid));?>
                </div>
                <br class="cbt" />
            </div>
        </div>
        <div id="sidebar" class='col-md-3'>
            <div class="module">
                <div class="summarycount al"><?php echo $tag->frequency;?></div>
                <p>问题</p>
                <div class="tagged">
                    <?php echo Html::a($tag->name, array('questions/tagged','name'=>$tag->name),array('title'=>'查看有该标签的问题列表','class'=>'post-tag','rel'=>'tag'));?>
                </div>
            </div>
        </div>
    </div>
</div>