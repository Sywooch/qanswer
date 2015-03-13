<?php
use app\models\Vote;
use yii\helpers\Html;
use app\components\Formatter;
?>          
    <div class="votecell pull-left">
        <div class="vote">
            <input type="hidden" value="<?php echo $data->id;?>"/>
            <a title="<?php echo Yii::t('global','vote up');?>" class="vote-up-off<?php if ($data->hasVote!=NULL && $data->hasVote==Vote::UPVOTE) echo " vote-up-on";?>"><?php echo Yii::t('global','vote up');?></a>
            <span style="font-size: 200%;" class="vote-count-post"><?php echo $data->score;?></span>
            <a title="<?php echo Yii::t('global','vote down');?>" class="vote-down-off<?php if ($data->hasVote!=NULL && $data->hasVote==Vote::DOWNVOTE) echo " vote-down-on";?>"><?php echo Yii::t('global','vote down');?></a>

            <?= Html::a('收藏', ['/question/post/vote','postid' => $data->id, 'type' => Vote::VOTE_TYPE_FAVORITE],['class' => "star-off ".(($data->hasFav!=NULL && $data->hasFav==Vote::FAV) ? "star-on" : ""),'title' => '收藏 (取消收藏)']); ?>
            <div class="favoritecount"><b><?php echo $data->favcount;?></b></div>
        </div>
    </div>
    <div class="post-cell">
        <div class="post-text">
            <?php
            echo $data->content;
            ?>
        </div>
        <div class="post-taglist">
        <?php
        foreach (explode(' ',$data->tags) as $tag) {
            echo Html::a($tag, ['questions/tagged','tag'=>$tag],['class'=>'post-tag','rel'=>'tag','title'=>"显示标签 '$tag'"]);
        }
        ?>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="post-menu">
                    <?php
                    if (!Yii::$app->user->isGuest) {
                        echo $this->render('_post_menu',array('data'=>$data));
                    }
                    ?>
                </div>
            </div>
            <div class="col-md-3">
                <?php if ($data->lastedit>0):?>
                <div class="post-signature">
                    <div class="user-info">
                        <div class="user-action-time">编辑于
                        <?php echo Html::a(Html::tag('span',Formatter::ago($data->lastedit),array('class'=>'relativetime')), array('post/revisions','id'=>$data->id),array('title'=>"查看历史版本"));?>
                        </div>
                        <div class="user-gravatar32"></div>
                        <div class="user-details">
                        </div>
                    </div>
                </div>
                <?php endif;?>
            </div>
            <div class="col-md-3">
                <div class="post-signature<?php if ($data->author->id==Yii::$app->user->getId()) echo " owner";?>">
                <?php if (!$data->isWiki()):?>
                    <div class="user-info">
                        <div class="user-action-time">提问于
                             <span class="relativetime" title="<?php echo Formatter::time($data->createtime); ?>"><?php echo Formatter::ago($data->createtime);?></span>
                        </div>
                        <?= $this->render('/common/_user',array('user'=>$data->author));?>
                        <?php if ($data->revCount>1):?>
                        <div class="user-details">
                        <?php echo Html::a($data->revCount."个版本", array('post/revisions','id'=>$data->id),array('title'=>"查看历史版本"));?>
                        </div>
                        <?php endif;?>
                    </div>
                <?php else:?>
                    <?php $this->render('/common/_user_wiki',array('data'=>$data));?>
                <?php endif;?>
                </div>
            </div>
        </div>
        <?= $this->render('/post/_comments',array('data'=>$data));?>
        <?= $this->render('_bounty-notification',array('data'=>$data));?>
    </div>
<?= $this->render('dialog'); ?>
<script>
    $('#post-menu-dialog').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget) // Button that triggered the modal
//      var recipient = button.data('whatever') // Extract info from data-* attributes
      // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
      // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
      var modal = $(this);
      $.get(button.attr('data-href'),function(response) {
          modal.find('.modal-body').html(response);
//          alert(modal.find('.modal-body'));
      });
      modal.find('.modal-title').text('New message to ')
      
    });
</script>