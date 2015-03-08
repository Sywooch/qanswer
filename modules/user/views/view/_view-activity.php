<div id="history-table" class="user-activity-table">
    <table style="margin-left: -50px; width: 100%;" class="summary-title">
        <tbody>
            <tr>
            <td style="width: 100%;">
                <?= \yii\widgets\Menu::widget($submenu); ?>
            </td>
            </tr>
        </tbody>
    </table>

    <table class="history-table">
        <tbody>
            <?php
            foreach ($activities as $activity) {
                switch ($activity->type) {
                    case 'ask':
                    case 'answer':
                        echo $this->render('_activity-posts', array('activity' => $activity));
                        break;
                    case 'comment':
                        echo $this->render('_activity-comments', array('activity' => $activity));
                        break;
                    case 'award' :
                        echo $this->render('_activity-badges', array('activity' => $activity));
                        break;
                    case 'accept' :
                        echo $this->render('_activity-accepts', array('activity' => $activity));
                        break;
                    case 'revise' :
                        echo $this->render('_activity-revisions', array('activity' => $activity));
                        break;
                    default :
                        echo $this->render('_activity-badges', array('activity' => $activity));
                        break;
                }
            }
            ?>
        </tbody>
    </table>
    <?= yii\widgets\LinkPager::widget(['pagination' => $pages, 'options' => ['id' => 'activity-pager', 'class' => 'pagination']]); ?>
    <script type="text/javascript">
		var activityFilter = 'all';
		var activityPageSize = 30;

		$(function () {
			expandPostBody('.history-table td.async-load', '<td/><td/>');
		});
    </script>

</div>