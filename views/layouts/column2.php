<?php $this->beginContent('@app/views/layouts/main.php'); ?>
<div class="container">
	<div class="span-18">
		<div id="content">
			<?php echo $content; ?>
		</div><!-- content -->
	</div>
	<div id="sidebar">
	</div>
</div>
<?php $this->endContent(); ?>