<div id="mainbar-full">
	<div class="subheader">
		<h1 id="edit-title"><?php echo $this->me->username;?> - 邮件通知</h1>
	</div>
	<?php $form=$this->beginWidget('CActiveForm',array('id'=>"user-edit-form")); ?>
		<ul class="setting-notify">
            <li>
                <input type="checkbox" <?php if ($this->me->notify['question_answered']==1):?>checked=""<?php endif;?> value="1" id="setting-question-answered" name="notify[question_answered]" tabindex="1">
                <label class="inline" for="setting-question-answered">当有其他人回答我的问题时</label>
			</li>
			<li>
                <input type="checkbox" <?php if ($this->me->notify['commented']==1):?>checked=""<?php endif;?> value="1" id="setting-commented" name="notify[commented]" tabindex="1">
                <label class="inline" for="setting-commented">当有人对我发布的内容评论时</label><br>
			</li>
			<li class="mt15"><p class="description">出现以上情形时发邮件提醒我</p></li>
			<li><input type="submit" value="提交设置" name="submit" tabindex="2" /></li>
		</ul>
    <?php $this->endWidget(); ?>
</div>