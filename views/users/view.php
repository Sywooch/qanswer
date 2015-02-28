<div id="mainbar-full">
    <div class="subheader">
        <h1 id="user-displayname"><?php echo $user->username; ?></h1>
        <?php
        if (!isset($_GET['tab']))
            $_GET['tab'] = 'stats';
            $menu = array(
                'items' => array(
                    array('label' => '基本信息', 'url' => array('users/view', 'id' => $user->id, 'tab' => 'stats'), 'options' => array('title' => '基本信息和统计')),
                    array('label' => '活动', 'url' => array('users/view', 'id' => $user->id, 'tab' => 'activity'), 'options' => array('title' => '最近活动')),
                    array('label' => '威望', 'url' => array('users/view', 'id' => $user->id, 'tab' => 'reputation'), 'options' => array('title' => '威望历史记录')),
                    array('label' => '收藏', 'url' => array('users/view', 'id' => $user->id, 'tab' => 'favorites'), 'options' => array('title' => '收藏的问题')),
                ),
                'options' => ['id' => 'tabs', 'class' => 'nav nav-tabs']
            );
            echo \yii\widgets\Menu::widget($menu);
        ?>
    </div>
    <?php
    switch ($tab) {
        case 'activity':
            echo $this->render('_view-activity', array('submenu' => $submenu, 'activities' => $activities, 'pages' => $pages));
            break;
        case 'reputation':
            echo $this->render('_view-reputation', array('reputes' => $reputes, 'pages' => $pages));
            break;
        case 'favorites':
            echo $this->render('_stats-favs', array('favs' => $favs, 'submenu' => $submenu, 'pages' => $pages));
            break;
        default:
            echo $this->render('_view-info', array(
                'user' => $user,
                'answers' => $answers,
                'aPages' => $aPages,
                'aSubmenu' => $aSubmenu,
                'questions' => $questions,
                'qSubmenu' => $qSubmenu,
                'qPages' => $qPages,
                'tPages' => $tPages,
                'usertags' => $usertags,
                'awards' => $awards
            ));
            break;
    }
    ?>
</div>