<?php if(isset($form_scripts)) echo $form_scripts ?>
<?php echo $cant ?>
<table style="margin:0;border-collapse:collapse;padding:0;width:100%;" id='detalles_<?php echo $id ?>'>
  <tr>
    <td>
      <table style="margin:0;border-collapse:collapse;padding:0;width:100%;">
        <tr>
          <td class="mainheader"><?php echo $title?></td>
          <td class="mainheader" align="right"><?php echo $container_tr;?></td>
        </tr>
      </table>

      <div class="mainbackground" style="padding:2px;clear:both;">
      <table width="100%" cellpadding="1">
        <tr>
<?php foreach ($headers as $column)://table-header?>
<?php if (in_array($column["type"], array("orderby","detail"))):?>
          <td class="tableheader">
            <table style="width:100%; border-collapse:collapse;">
              <tr>
                <td class="tableheader_clean"><?php echo $column["label"]?></td>
                <td class="tableheader_clean" style="width:28px">
                  <a href="<?php echo $column["orderby_asc_url"]?>"><img src="<?php echo $this->rapyd->get_elements_path('orderbyasc.gif')?>" border="0"></a><a href="<?php echo $column["orderby_desc_url"]?>"><img src="<?php echo $this->rapyd->get_elements_path('orderbydesc.gif')?>" border="0"></a>
                </td>
              </tr>
            </table>
          </td>
<?php elseif ($column["type"] == "clean"):?>
          <td <?php echo $column["attributes"]?>><?php echo $column["label"]?></td>
<?php elseif (in_array($column["type"], array("normal"))):?>
          <td class="tableheader" <?php echo $column["attributes"]?>><?php echo $column["label"]?></td>
<?php endif;?>
<?php endforeach;//table-header 1  ?>
        </tr>
        <tbody>
<?php 
$columcount=1;
if (count($rows)>0)://table-rows 
	$rowcount=0;
	foreach ($rows as $row):
    $conten['row']=$row;
		$conten["id"]=$id;
    echo str_replace('<#i#>',$rowcount,$this->load->view('datadetallefila', $conten, true));
		$rowcount++;
	endforeach;
endif;//table-rows
?>			<tr id='tr_detalle_pie_<?php echo $id ?>'>
				<td colspan=<?php echo $columcount ?> ></td>
			</tr></tbody>
      </table>
      </div>
      <div class="mainbackground"><div class="pagenav"><?php echo $pager;?></div></div>
      <div class="mainfooter">
        <div>
          <div style="float:left"><?php echo $container_bl?></div>
          <div style="float:right"><?php echo $container_br?></div>
        </div><div style="clear:both;"></div>
      </div>

    </td>
  </tr>
</table>