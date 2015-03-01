<?php

use app\components\Formatter;
use yii\helpers\Html;
?>
<div class="rep-wrapper">
    <div id="stats">
        <div id="rep-page-container">
            <table class="rep-table">
                <tbody>
                    <?php foreach ($reputes as $date => $repute): ?>
                        <tr class="rep-table-row">
                            <td class="rep-cell">
                                <span class="rep-up"><?php echo $repute['total']; ?></span>
                            </td>

                            <td title="<?php echo $date; ?>" class="rep-day ">
                                <a class="load-body expander-arrow-small-hide hide-body expander-arrow-small-show" style=""></a>
                                <?php echo $date; ?>
                            </td>
                        </tr>
                        <tr class="loaded-body">
                        <td style="height: 0px; padding: 0px;" class="body-container body-loaded" colspan="2">
                            <div style="display: block;">
                                <div class="rep-breakdown">
                                    <table class="tbl-reputation">
                                        <tbody>
                                            <?php foreach ($repute['list'] as $item): ?>
                                            <tr class="rep-breakdown-row rep-recent-row expandable-row">
                                                <td class="rep-left">
                                                    <span class="rep-up"><?php echo $item->reputation; ?></span>
                                                </td>
                                                <td title="<?php echo Formatter::time($item->time); ?>" class="rep-time"><?php echo Formatter::ago($item->time); ?></td>
                                                <td class="rep-desc"><?php echo $item->lng; ?></td>
                                                <td class="rep-link async-load load-prepped">
                                                <?php
                                                    $url = $item->question->url;
                                                    if ($item->apostid > 0) {
                                                        $url .= '#' . $item->apostid;
                                                    }
                                                    echo Html::a(Html::encode($item->question->title), $url, array('class' => "answer-hyperlink"));
                                                ?>
                                                </td>
                                            </tr>
                                            <tr class="loaded-body">
                                                <td colspan="4" class="body-container" style="padding: 0px;"></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <script type="text/javascript">
                                    $(function () {
                                        expandPostBody('.tbl-reputation td.rep-link.async-load:not(.load-prepped)', null, null, 4);
                                    });
                                </script>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?= yii\widgets\LinkPager::widget(['pagination' => $pages, 'options' => ['id' => 'reputation-pager', 'class' => 'pagination']]); ?>
            <script type="text/javascript">
                var reputationView = 'post';
                var reputationPageSize = 30;

                $(function () {
                    expandPostBody('.rep-table td.async-load:not(.load-prepped)', '', null, 2);
                    if (!$('.rep-day .expander-arrow-small-hide').length)
                        $(".rep-table .load-body").slice(0, 3).click();
                });
            </script>
        </div>
    </div>
</div>


