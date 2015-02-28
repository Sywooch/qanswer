<?php
use app\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Url;
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo !empty($this->title) ? Html::encode($this->title)." - ".Yii::$app->name : Yii::$app->name; ?></title>
    <?php $this->head() ?>
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::$app->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::$app->request->baseUrl; ?>/css/jquery.autocomplete.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::$app->request->baseUrl; ?>/css/prettify.css" />
	<?php if (Yii::$app->controller->id == 'users'):?>
		<script type="text/javascript">
		$(function () {
			$("#userfilter").val("").focus().typeWatch({ highlight: true, wait: 500, captureLength: -1, callback: finished(false) });
		});
		function finished(all) {
			var showingAll = all;
			return function(txt) {
				var search = $.trim(txt.toLowerCase());
				if (search.length < 3 && showingAll) { return; }
				var url = iAsk.options.links.usersfilter;
				if (search.length < 3) {
					url = url + '?show=all';
					showingAll = true;
				}
				var spinner = $("#userfilter").addSpinnerAfter({'margin-left' : "8px", 'margin-bottom' : '-4px'}).next();

				$.ajax({
					url: url,
					data: {
						search: search,
						filter: '<?php echo (isset($_GET['filter'])) ? $_GET['filter'] : ((isset($_GET['tab']) && $_GET['tab']=='newusers') ? 'reputation' : 'week'); ?>',
						tab: '<?php echo (isset($_GET['tab'])) ? $_GET['tab'] : 'reputation'; ?>'
						},
						success: function (result) {
							$("#user-browser").replaceWith($(result).filter("#user-browser"));
							$("#tabs-interval").replaceWith($(result).find("#tabs-interval"));
						},
						complete: function() { spinner.remove(); }
					});
				}
			}
		</script>
	<?php endif;?>

	<script type="text/javascript">
		var ulinks = {
			'privileges' : '<?php echo Url::to('privileges/index');?>',
			'logout'	 : '<?php echo Url::to('users/logout');?>',
			'login'		 : '<?php echo Url::to('users/login');?>',
		};
        $(function () {
            profileLink.init(
				'<img src="<?php // echo is_object(Yii::$app->user->identity) ? Yii::$app->user->identity->middleavatar : "#";?>" height="48" width="48" alt="">',
				false,
				'<?php // echo Url::to('users/view',array('id'=>is_object(Yii::$app->user->identity) ? Yii::$app->user->identity->id : 0,'tab'=>'activity'));?>',
				<?php // echo $this->time;?> - (new Date()).getTime() / 1000,
				ulinks
			);
        });
        <?php if ($this->context->id=='users'):?>
        var uid = <?php echo isset($_GET['id']) ? $_GET['id'] : ((Yii::$app->user->getId()>0) ? Yii::$app->user->getId() : 0);?>
        <?php endif;?>
    </script>

	<script type="text/javascript">
	iAsk.init({
		"site": {
			"base"		: "<?php echo Yii::$app->request->baseUrl; ?>",
			'baseUrl'	: '<?php echo Yii::$app->getUrlManager()->getBaseUrl();?>',
		},
		"user": {
			"isRegistered": <?php if (Yii::$app->user->isGuest):?>false<?php else:?>true<?php endif;?>,
			"fkey": "<?php echo Yii::$app->request->csrfToken;?>",
			"messages": [
			 <?php if (Yii::$app->user->identity):?>
			 <?php foreach(Yii::$app->user->identity->notifies as $notify):?>
			 {
				"id": <?php echo $notify->id;?>,
				"messageTypeId": <?php echo $notify->typeid;?>,
				"text": '<?php echo $notify->formatMessage;?>',
				"userId": "<?php echo $notify->uid;?>",
				"showProfile": true
			},
			<?php endforeach;?>
			<?php endif;?>
			],
			"inboxUnviewedCount": 0
		},
		"links": {
			'protect'			: '<?php echo Url::to('/post/protect');?>',
			'unprotect' 		: '<?php echo Url::to('/post/unprotect');?>',
			'lock'				: '<?php echo Url::to('/post/lock');?>',
			'unlock' 			: '<?php echo Url::to('/post/unlock');?>',
			'popup'				: '<?php echo Url::to('/post/popup');?>',
			'commenthelp'		: '<?php echo Url::to('/post/commenthelp');?>',
			'login'		 		: '<?php echo Url::to('/users/login');?>',
			'validateduplicate'	: '<?php echo Url::to('/post/validateduplicate');?>',
			'messageInfoMod'	: '<?php echo Url::to('/messages/infomod');?>',
			'filterTagIndex'	: '<?php echo Url::to('/filter/tagindex');?>',
			'bountyStart'		: '<?php echo Url::to('/post/bountystart');?>',
			'userActivity'		: '<?php echo Url::to(['users/activity']);?>',
			'userRep'			: '<?php echo Url::to(['users/rep']);?>',
			'userStat'			: '<?php echo Url::to(['users/stats']);?>',
			'userView'			: '<?php echo Url::to('/users/view');?>',
			'postView'			: '<?php echo Url::to('/post/view');?>',
			'revisionsView'		: '<?php echo Url::to('/revisions/view');?>',
			'profilelink'		: '<?php echo Url::to('/users/profilelink');?>',
			'usersfilter'		: '<?php echo Url::to('/users/filter');?>',
			'savepreference'	: '<?php echo Url::to('/users/savepreference');?>',
			'subscriber'		: '<?php echo Url::to('/tags/subscriber');?>',
			'vote'				: '<?php echo Url::to('/post/vote');?>',
			'messageMark'		: '<?php echo Url::to('/messages/markread');?>',
			'postComments'		: '<?php echo Url::to('/post/comments');?>'
		},
	});
	<?php if (Yii::$app->controller->id == 'questions'):?>
	$(function(){
		iAsk.question.init({
			questionId:22,
			editCommentUrl:	"<?php echo Url::to('/post/comments');?>",
			addCommentUrl:	"<?php echo Url::to('/post/view');?>",
			voteCommentUrl:	"<?php echo Url::to('/post/comments');?>",
			moreCommentsUrl:"<?php echo Url::to('/post/view');?>",
            hasOpenBounty:<?php if (Yii::$app->controller->hasOpenBounty):?>true<?php else:?>false<?php endif;?>,
			canOpenBounty:<?php if (Yii::$app->controller->me && Yii::$app->controller->me->checkPerm('setBounties') && !Yii::$app->controller->hasOpenBounty):?>true<?php else:?>false<?php endif;?>,            
		});
		styleCode();
	})
	<?php endif;?>
	</script>
	<?php if (Yii::$app->user->identity && $count = count(Yii::$app->user->identity->notifies)>0):?>
	<style type="text/css">
	body { margin-top: <?php echo 2.5*$count;?>em;}
	</style>
	<?php endif;?>
    
</head>

<body>
<?php $this->beginBody() ?>    
	<div id="notify-container">
	</div>
	<div id="overlay-header"></div>
	<div id="custom-header"></div>
	<div class="container">
		<div id="header">
			<div id="topbar">
				<div id="hlinks">
					<span id="hlinks-user">
					<?php if (!Yii::$app->user->isGuest) :?>
						<?php if (Yii::$app->user->identity->messagecount>0):?>
						<a title="<?php echo Yii::$app->user->identity->messagecount;?>个消息" href="<?php echo Url::to('users/inbox');?>"><span class="envelope-on"> </span></a>
						<?php else:?>
						<a title="没有新消息" href="<?php echo Url::to('users/inbox');?>"><span class="envelope-off"> </span></a>
						<?php endif;?>
					<?php endif;?>
						<?php if (!Yii::$app->user->isGuest) :?>
						<?php echo Html::a(Yii::$app->user->identity->username,array('users/view','id'=>Yii::$app->user->getId()),array('class'=>'profile-link'));?>
						<span class="profile-triangle">▾</span>
						<a href="<?php echo Url::to('privileges/index');?>"/>
							<span title="查看威望权限" class="reputation-score"><?php echo Yii::$app->user->identity->reputation;?></span>
						</a>
						<?php else:?>
						<?php echo Html::a("注册",array('users/register'));?>
						<span class="lsep">|</span>
						<?php echo Html::a("登录",array('users/login'));?>
						<?php endif;?>
						<span class="lsep">|</span>
					</span>
					<span id="hlinks-nav">
					</span>
					<span id="hlinks-custom">
						<a href="<?php echo Url::to('faq/index');?>">FAQ</a>
						<?php if (Yii::$app->user->identity && (Yii::$app->user->identity->isAdmin() || Yii::$app->user->identity->isMod())):?>
						<span class="lsep">|</span>
						<a href="<?php echo Url::to('modtools/index');?>">版主工具</a>
						<?php endif;?>
						<?php if (Yii::$app->user->identity && Yii::$app->user->identity->isAdmin()):?>
						<span class="lsep">|</span>
						<a href="<?php echo Url::to('admin/index');?>">后台</a>
						<?php endif;?>
					</span>
				</div>
				<div id="hsearch">
					<form method="get" action="<?php echo Url::to('search/index');?>" id="search">
						<div>
							<input type="text" value="search" size="28" maxlength="140" onfocus="if (this.value=='search') this.value = ''" tabindex="1" class="textbox" name="q"/>
						</div>
					</form>
				</div>
			</div>
			<br class="cbt">
			<div id="hlogo"><a href="<?php echo Url::to('index/index');?>"><?php echo Html::encode(Yii::$app->name); ?></a></div>
			<div id="hmenus">
                <?php
                    NavBar::begin([
                        'brandLabel' => 'My Company',
                        'brandUrl' => Yii::$app->homeUrl,
                        'options' => [
                            'class' => 'navbar-inverse navbar-fixed-top',
                        ],
                    ]);
                    echo Nav::widget([
                        'options' => ['class' => 'navbar-nav navbar-right'],
                        'items' => [
                            array('label'=>'首页', 'url'=>array('/index/index'),'active'=>Yii::$app->controller->id=='index'),
                            array('label'=>'问题', 'url'=>array('/questions/index'),'active'=>Yii::$app->controller->id=='questions'),
                            array('label'=>'标签', 'url'=>array('/tags/index'),'active'=>Yii::$app->controller->id=='tags'),
                            array('label'=>'用户', 'url'=>array('/users/index'),'active'=>Yii::$app->controller->id=='users'),
                            array('label'=>'徽章', 'url'=>array('/badges/index'),'active'=>Yii::$app->controller->id=='badges'),
                            array('label'=>'等待回答', 'url'=>array('unanswered/index'),'active'=>Yii::$app->controller->id=='unanswered'),
                            Yii::$app->user->isGuest ?
                                ['label' => 'Login', 'url' => ['/site/login']] :
                                ['label' => 'Logout (' . Yii::$app->user->identity->username . ')',
                                    'url' => ['/site/logout'],
                                    'linkOptions' => ['data-method' => 'post']],
                        ],
                    ]);
                    NavBar::end();
                ?>
					
				<div class="nav askquestion">
					<ul>
						<li><?php echo Html::a('提问',array('questions/ask'),array('id'=>'nav-askquestion'));?></li>
					</ul>
				</div>
			</div>
		</div>

		<?php echo $content; ?>

	</div>
	<div id="footer">
		<div class="footerwrap">
			<div id="footer-menu">
				<a href="<?php echo Url::to('about/index');?>">关于</a>
				|
				<a href="http://blog.ilewen.com">博客</a>
				|
				<a href="mailto:services@ilewen.com">联系我们</a>				
			</div>
			<div id="copy">
			&copy; <?php echo date('Y'); ?> 乐问&nbsp;&nbsp;
				本站内容采用&nbsp;<a href="http://creativecommons.org/licenses/by-sa/2.5/cn/" rel="license"><img src="http://i.creativecommons.org/l/by-sa/2.5/cn/80x15.png" class="cc-icon" alt="知识共享许可协议"> <strong>知识共享署名-相同方式共享 2.5 中国大陆许可协议</strong></a>
			 &nbsp;&nbsp;<?php // echo $this->options['miibeian'];?>
			</div>
			<span class="right"><?php // echo Yii::$app->params['version']['release'];?></span>
		</div>

	</div>
	<?php
//	if ($this->options['statcode']) {
//		echo $this->options['statcode'];
//	}
	?>
<?php $this->endBody() ?>
</body>
<?php
//if ($this->options['uservoice']) {
//	echo $this->options['uservoice'];
//}
?>
</html>
<?php $this->endPage() ?>