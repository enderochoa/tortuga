<table style="margin:0;width:100%;border-collapse:collapse;padding:0;">
  <tr>
    <td>
      <table style="margin:0;width:100%;border-collapse:collapse;padding:0;">
        <tr>
          <td class="mainheader"><?=(isset($container_tl)?$container_tl:'');?><?php echo $title?></td>
          <td class="mainheader" align="right"><?php echo $container_tr;?></td>
        </tr>
      </table>
      <div class="mainbackgroundtable" style="padding:2px;clear:both;">
      <table width="100%" cellpadding="1" id='tablagrid' >
	  <thead>
        <tr>

<?php foreach ($headers as $column):  //table-header?>
<?php if (in_array($column["type"], array("orderby","detail"))):?>

          <th class="tableheader" >
            <table style="width:100%; border-collapse:collapse;">
              <tr>
                <td class="tableheader_clean"><?php echo $column["label"]?></td>
                <td class="tableheader_clean" style="width:28px">
                  <a href="<?php echo $column["orderby_asc_url"]?>"><img src="<?php echo $this->rapyd->get_elements_path('orderbyasc.gif')?>" border="0"></a><a href="<?php echo $column["orderby_desc_url"]?>"><img src="<?php echo $this->rapyd->get_elements_path('orderbydesc.gif')?>" border="0"></a>
                </td>
              </tr>
            </table>
          </th>

<?php elseif ($column["type"] == "clean"):?>
          <th <?php echo $column["attributes"]?>><?php echo $column["label"]?></th>
<?php elseif (in_array($column["type"], array("normal"))):?>
          <th class="tableheader" <?php echo $column["attributes"]?>><?php echo $column["label"]?></th>
<?php endif;?>
<?php endforeach;//table-header?>
        </tr>
	
		</thead>
<?php if (count($rows)>0)://table-rows?>
  <?php $rowcount=0;?>
<?php foreach ($rows as $row):?>
  <?php $rowcount++;?>
        <tr <?php if($rowcount % 2){ echo 'class="odd"';}else{ echo 'class="even"';} echo 'id="row_'.$rowcount.'"' ?> >
<?php foreach ($row as $cell):?>
<?php if ($cell["type"] == "detail"):?>
          <td <?php echo $cell["attributes"]?> class="littletablerow" ><a href="<?php echo $cell["link"]?>"><?php echo $cell["field"]?><img src="<?php echo $this->rapyd->get_elements_path('elenco.gif')?>" width="16" height="16" border="0" align="absmiddle" /></a></td>
<?php elseif ($cell["type"] == "clean"):?>
          <td <?php echo $cell["attributes"]?>><?php echo $cell["field"]?></td>
<?php else:?>
          <td <?php echo $cell["attributes"]?> class="littletablerow"><?php echo $cell["field"]?>&nbsp;</td>
<?php endif;?>
<?php endforeach;?>
        </tr>
<?php endforeach;?>
<?php endif;//table-rows?>

      </table>
      </div>
      <div class="mainbackground"><div class="pagenav"><?php echo $pager;?></div></div>
      <div class="mainfooter">
        <div>
          <div style="float:left" ><?php echo $container_bl?></div>
          <div style="float:right"><?php echo $container_br?></div>
        </div>
        <div style="clear:both;"></div>
        <div >Total de Registros Encontrados:<?php echo $total_rows ?></div>
        
      </div>

    </td>
  </tr>
</table>
