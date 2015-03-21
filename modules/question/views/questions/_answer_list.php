<div id="answers">
    <a name="tab-top"></a>
    <div id="answers-header">
        <div class="subheader answers-subheader">
            <h2><?php echo $model->answercount; ?>个回答</h2>
            <?php
            $submenu = array(
                'items' => array(
                    array('label' => '活跃', 'url' => ['questions/view', 'id' => $model->id, 'tab'=> 'activity', '#' => 'tab-top'], 'options' => ['title' => '按活跃度排序']),
                    array('label' => '时间', 'url' => ['questions/view', 'id' => $model->id, 'tab'=> 'oldest', '#' => 'tab-top'], 'options' => ['title' => '按时间排序']),
                    array('label' => '投票', 'url' => ['questions/view', 'id' => $model->id, 'tab'=> 'votes', '#' => 'tab-top'], 'options' => ['title' => '按投票排序']),
                ),
                'options' => ['id' => 'tabs', 'class' => 'nav nav-tabs']
            );
            if ($tab == 'activity') {
                $submenu['items'][0]['options']['class'] = 'active';
            }
            echo \yii\widgets\Menu::widget($submenu);
            ?>
        </div>
    </div>

    <?php
    foreach ($answers as $ans) {
        if (!$ans->poststate->isDelete() || ($this->me && ($this->me->isAdmin() || $this->me->isMod() || $ans->isSelf() || $this->me->checkPerm('moderatorTools')))) {
            echo $this->render('_answer', array('data' => $ans, 'question' => $model));
        }
    }
    ?>
    <?= yii\widgets\LinkPager::widget(['pagination' => $pages]);?>
</div>