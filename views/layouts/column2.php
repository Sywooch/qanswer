<?php $this->beginContent('@app/views/layouts/main.php'); ?>
<div class="container">
    <div class="row">
	<div class="col-md-9">
		<?php echo $content; ?>
	</div>
	<div id="sidebar" class="col-md-3">
	</div>
</div>
<?php $this->endContent(); ?>