<p><a href="<?php echo $data['user']->getAbsoluteUrl();?>"><?php echo $data['user']->username;?></a>，您好!</p>

<p>你的帖子<a href="<?php echo $data['url'];?>"><?php echo $data['title']?></a>有了新的评论，立刻去查看。</p>

<p>这是一封自动发给<?php echo $data['user']->email;?>的Email，请勿回复。<?php ?>

<p>如果您不希望将来从乐问网收到此类邮件，请点击下面链接，去个人帐号里设置取消（需要先登录）
<a href="<?php echo Yii::$app->urlManager->createAbsoluteUrl('users/setting');?>"><?php echo Yii::$app->urlManager->createAbsoluteUrl('users/setting');?></a>
</p>