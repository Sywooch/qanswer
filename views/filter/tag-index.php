<table id="tags-browser">
	<tbody>
		<tr>
            <?php foreach($tags as $i=>$v):?>
            <?= $this->render("/tags/_index_td",array('data'=>$v,'weeks'=>$weeks,'days'=>$days));?>
            <?php if ($i % 4==0 && $i>0):?>
            	<?= "</tr><tr>";?>
            <?php endif;?>
		<?php endforeach;?>
		</tr>
	</tbody>
</table>