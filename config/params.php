<?php

// this contains the application parameters that can be maintained via GUI
return array(
	'title'			=> '乐问',
	'sitename'		=> '乐问',
	'adminEmail'	=> 'xue.song@outlook.com',
	'copyrightInfo'	=>'Copyright &copy; 2009 by My Company.',
    'timeoffset'    => '8',
	'site'			=> array(
		'allowMutiAnswer'		=> true,	//允许多次回答一个问题
	),
	'comments'		=> array(
		'allowEditTime'			=> 900,		//单位：秒，表示评论发表完成后多长时间内可以编辑
	),
	'sphinxOn'	=> false,
	'reputations'=>array(
		'createPosts'			=> 1,
		'newUser'				=> 10,
		'communityWiki'			=> 10,
		'voteUp'				=> 15,		//投有用票
		'flag'					=> 15,		//举报
		'flagPost'				=> 15,		//举报贴子
		'comment'				=> 50,		//任何地方评论
		'setBounties'			=> 75,		//设置悬赏
		'editCommunityWiki'		=> 100,		//编辑社区wiki问题
		'voteDown'				=> 125,		//投无用票
		'closeMyQuestions'		=> 250,		//投票关闭自己的问题 (250)
		'createTags'			=> 1500,
		'edit'					=> 2000,	//编辑产生的版本不需要审核
		'closeQuestions'		=> 3000,	//投票关闭任何问题 (3000)
		'approveTagWikiEdits'	=> 5000,	//审核标签wiki编辑
		'moderatorTools'		=> 10000,	//10000,	//接入版主工具
		'protect'				=> 15000,	//设置问题回答受限
		'protectQuestions'		=> 15000,	//保护问题，取代protect
		'trustedUser'			=> 20000,
	),
	'posts'		=> array(
		'maxCloseVotes'			=> 5,		//最大关闭投票数量 正式部署是为5，测试设为1
		'maxDeleteVotes'		=> 5,		//删除需要票数
		'maxFlagVotes'			=> 3,		//隐藏帖子需要的举报票数
		'maxFlagLockVotes'		=> 6,		//把帖子锁定需要的举报票数
		'flagRepLose'			=> 100,		//举报成功扣除的威望值
		'flagLife'				=> 5,		//举报有效期，单位天
		'informModFlags'		=> 10,		//提醒版主注意举报每天限制数量
		'spamFlags'				=> 5,		//垃圾帖子举报每天限制数量（即每个用户每天最多举报5次）
		'rewardLife'			=> 7,		//正常设为7天，悬赏有效期
		'bountyFreezeTime'		=> 12,		//悬赏冻结时间，单位：小时（即问题提问后多久才可能发布悬赏）
		'closeBountyFreezeTime'	=> 24,		//发出悬赏冻结时间，单位：小时(即悬赏发布后多久才可能授予悬赏）
		'deletionVote'			=> 10000,	//可以对帖子发起删除投票和反删除投票的最低威望
		'expiredBountyScores'	=> 2,		//过期悬赏自动授予最低票数，默认2
		'unwikiToWikiCount'		=> 5,		//问题被几个不同用户编辑后自动变为wiki模式
	),
	'pages'		=> array(
		'userQuestionPagesize'	=> 10,		//个人首页问题分页数量
		'userAnswerPagesize'	=> 10,		//个人首页问题分页数量
		'userActivityPagesize'	=> 20,
		'userReputationPagesize'=> 20,		//个人首页威望记录分页
		'userFavoritePagesize'	=> 10,		//个人首页收藏分页
		'userTagsPagesize'		=> 15,		//个人首页标签分页数量
		'tagPagsize'			=> 25,		//标签首页分页数
		'questionsIndex'		=> 30,		//问题列表分页数量
		'answer'				=> 10,
		'userIndexVoters'		=> 5,		//用户首页投票门限
		'userIndexEditors'		=> 5,		//用户首页编辑门限
		'userIndexReps'			=> 5,		//用户首页威望门限
		'userIndexPagesize'		=> 100,		//用户首页分页数量
		'searchPagesize'		=> 30,
		'hotQuestionsPagesize'	=> 20,
		'badgeAwardsPagesize'	=> 24,		//徽章授予记录分页
		'postsPerPage'			=> 30,
	),
	'badges'	=> array(
		'commentorCount'		=> 10,
		'popularQuestionViews'	=> 1000,
		'notableQuestionViews'	=> 2500,
		'famousQuestionViews'	=> 10000,
		'questionFavorites'		=> 25,
		'niceQuestionScores'	=> 10,
		'goodQuestionScores'	=> 25,
		'greatQuestionScores'	=> 100,
		'niceAnswerScores'		=> 10,
		'goodAnswerScores'		=> 25,
		'greatAnswerScores'		=> 100,
		'guruScores'			=> 40,		//权威：答案被采纳且超过40分
		'taxonomistCount'		=> 50,		//分类标签数量
		'SelfLearnerCount'		=> 3,		//自学成才
		'enlightenedCount'		=> 10,
		'StellarQuestionCount'	=> 100,		//万人迷（问题收藏数量）
		'firstEditor'			=> 1,		//校订者
		'chiefEditor'			=> 80,		//主编 (默认：80）
		'seniorEditor'			=> 500,
	),
	'users'		=> array(
		'votesPerDay'			=> 30,		//每天投票数量（指vote up/vote down）
		'closeVotesPerDay'		=> 12,		//每天可投关闭票数量
	),
	'oauth'		=> array(
		'douban'	=> array(
			'key'	=> '123',
			'secret'=> '123',
		),

		'sina'		=> array(
			'key'	=> '123',
			'secret'=> '123',
		),
		'qq'		=> array(
			'key'	=> '123',
			'secret'=> '123',
		)
	),
);
