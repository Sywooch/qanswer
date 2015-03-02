<div class="row">
    <div id="mainbar" class="col-md-9">
        <div class="subheader">
            <h1 id="h-all-questions">问题列表</h1>
            <?= \yii\widgets\Menu::widget($submenu); ?>
        </div>
        <div id="questions">
            <?php
            $currentUser = Yii::$app->user->identity;
            foreach ($questions as $item) {
    //			if (!$item->poststate->isDelete() || ($currentUser && ($currentUser->isAdmin() || $currentUser->isMod() || $currentUser->checkPerm('moderatorTools')))) {
                    echo $this->render('_question',array('data'=>$item));
    //			}
            }
            ?>
        </div>
        <?= 
            yii\widgets\LinkPager::widget([
                'pagination' => $pages,
            ]);
        ?>
    </div>
    <div id="sidebar" class="col-md-3">
        <div id="questions-count" class="module">
            <div class="summarycount al"><?php echo $pages->totalCount;?></div>
            <p>问题</p>
        </div>
        <?= $this->render('/common/_sidebar_tags');?>
        <?php // $this->widget('RepTops'); ?>
    </div>
</div>