<?php if ($title!=""):?>
<table style="margin:0;width:100%;border-collapse:collapse;padding:0;">
	<tr>
		<td class="mainheader"><?php echo $title?></td>
		<td class="mainheader" align="right"><?php echo $container_tr?></td>
	</tr>
</table>
<?php endif;?>
<table width="100%" cellspacing="0" cellpadding="0">
<?php if (count($trs)>0)://table-rows?>
<?php foreach ($trs as $tds):?>
  <tr>
<?php foreach ($tds as $td):?>
    <td <?php echo $td["attributes"]?>><?php echo $td["content"]?></td>
<?php endforeach;?>
  </tr>
<?php endforeach;?>
<?php endif;//table-rows?>
</table>
<?php if (isset($pager)):?>
<div class="mainbackground"><div class="pagenav"><?php echo $pager?></div></div>
<?php endif?>
<?php if ($title!=""):?>
<div class="mainfooter">
	<div>
		<div style="float:left"><?php echo $container_bl?></div>
		<div style="float:right"><?php echo $container_br?></div>
	</div><div style="clear:both;"></div>
</div>
<?php endif;?>
