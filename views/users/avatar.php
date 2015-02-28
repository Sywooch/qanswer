<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/crop/jquery.Jcrop.css" />
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/jquery.Jcrop.js');?>

<script language="Javascript">
function updateCoords(c){
	$('#x').val(c.x);
	$('#y').val(c.y);
	$('#w').val(c.w);
	$('#h').val(c.h);
};
function checkCoords(){
	if (parseInt($('#w').val())) return true;
	alert('请选择一个区域');
	return false;
};

function showPreview(coords){
	if (parseInt(coords.w) > 0)	{
		var rx = 128 / coords.w;
		var ry = 128 / coords.h;

		$('#preview').css({
			width: Math.round(rx * $('#big').attr('width')) + 'px',
			height: Math.round(ry * $('#big').attr('height')) + 'px',
			marginLeft: '-' + Math.round(rx * coords.x) + 'px',
			marginTop: '-' + Math.round(ry * coords.y) + 'px'
		});
	}
}

$(function() {
	$("#upload_big").submit(function() {
		var fname = $(this).attr('name');

		$('#notice').text('上传中..').fadeIn();

		$('#upload_target').unbind().load( function(){
			var img = $('#upload_target').contents().find('body ').html();

			$('#previewWrap').html(img);

			var img_id = 'big';
			$('.img_src').attr('value',img)
			$('#previewWrap').html('<img id="preview" src="'+img+'" />');

			$('#div_'+fname).html('<img id="'+img_id+'" src="'+img+'" />');

			if($(img).attr('class') != 'uperror'){
				$('#upload_thumb').show();
			}else{
				$('#upload_thumb').hide();
			}

			$('#big').Jcrop({
				aspectRatio: 1,
				onSelect: updateCoords,
				onChange: showPreview,
			});

			$('#notice').fadeOut();

		});
	});
});
</script>
<div id="mainbar-full">
	<div class="subheader">
		<h1 id="edit-title"><?php echo $this->me->username;?> - 更新头像</h1>
	</div>

	<div id="uploader">
		<div id="big_uploader">
            <form name="upload_big" id="upload_big" method="post" enctype="multipart/form-data"	action="<?php echo yii\helpers\Url::to('users/avatar',array('do'=>'upload'));?>" target="upload_target">
				<label for="photo">
					1. 上传照片 :
					<input name="photo" id="file" size="27" type="file" />
				</label>
				<input type="submit" name="action" value="上传" />
			</form>
		</div>
		<div id="notice">
		</div>
		<table>
			<tr>
				<td>
					<div id="uploaded" style="width:620px;margin-right:10px;">
						<p>2. 裁剪</p>
						<div id="div_upload_big">
						</div>
                        <form id="upload_thumb" action="<?php echo yii\helpers\Url::to('users/avatar',array('do'=>'crop'));?>" method="post" onsubmit="return checkCoords();">
							<input type="hidden" id="x" name="x" />
							<input type="hidden" id="y" name="y" />
							<input type="hidden" id="w" name="w" />
							<input type="hidden" id="h" name="h" />
							<input type="submit" value="裁剪头像" />
						</form>
					</div>
				</td>
				<td>
					<div id="thumbnail">
						<h3>
							预览
						</h3>
						<div id="previewWrap" style="width:128px;height:128px;overflow:hidden;">
						</div>
					</div>
				</td>
			</tr>
		</table>
		<iframe id="upload_target" name="upload_target" src="" style="width:100%;height:400px;border:1px solid #ccc; display:none"></iframe>	</div>
</div>