<?php
$this->title = '提问';
?>
<h1>我要提问</h1>
<div class="row">
    <div id="mainbar" class="col-md-9">
        <script type="text/javascript">
        $(function(){
            initFadingHelpText();
            moveScroller();
        });
        </script>
        <?php echo $this->render('/post/_form', array('model'=>$model,'type'=>'ask')); ?>
    </div>
    <div class="col-md-3">
    <?= $this->render('/post/_ask-sidebar');?>
    </div>
</div>