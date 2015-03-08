
        <div class="favorites-table" id="favorites-table">
            <div class="row">
                <div class="col-md-12">
                    <?= \yii\widgets\Menu::widget($submenu);?>
                </div>
            </div>
            <?= $this->render('_question',array('favs'=>$favs)); ?>
            <?= yii\widgets\LinkPager::widget(['pagination' => $pages, 'options' => ['id' => 'favorite-pager', 'class' => 'pagination']]); ?>
        </div>
<script type="text/javascript">
  var favoritesSortOrder = 'views';
  var favoritesPageSize = <?php echo Yii::$app->params['pages']['userFavoritePagesize'];?>;
  var userViewStartDate = '';
  var favsUrl = "<?php echo yii\helpers\Url::to(['users/stats','do'=>'favorites']);?>";
</script>

