<?php echo $form_begin?>
<table style="margin:0;width:100%;border-collapse:collapse;padding:0;">
  <tr>
    <td>
<?php if($title!=""):?>
      <table style="margin:0;width:100%;border-collapse:collapse;padding:0;">
        <tr>
          <td class="mainheader" align="left"><?php echo $container_tl?></td>
          <td class="mainheader" align="right"><?php echo $container_tr?></td>
        </tr>
        <tr>
        	<td colspan=2 class="mainheader"><?php echo $title?></td>
        </tr>
      </table>
<?php endif;?>
      <div class="mainbackground" style="padding:2px;clear:both">
      <div class="alert"><?php echo $error_string?></div>
      <table style="margin:0;width:98%;">      
<?php if (isset($groups)):?>
<?php foreach ($groups as $group)://groups?>
<?php //@BOFS ?>
  <?php if ($group["group_name"] != "ungrouped"):?>
      <tr <?php echo $group["group_tr"]?>>
        <td colspan="2" style="padding:0;">
        
        <table style="margin:0;width:100%;">
          <tr>
            <td colspan="2" class="micro"><?php echo $group["group_name"]?></td>
          </tr>
  <?php endif?>
  <?php foreach ($group["series"] as $field_series)://field_series?>

    <?php if($field_series["is_hidden"]):?>
    
      <?php foreach ($field_series["fields"] as $field):?>
       <?php echo $field["field"]?>
      <?php endforeach;?>
      
    <?php else://non hidden?>
          <tr <?php echo $field_series["series_tr"]?>>
  
      <?php if(isset($field_series["fields"])): ?>
        <?php $first_field=true?>
        <?php foreach ($field_series["fields"] as $field)://fields?>

          <?php if($first_field):?>
            <?php $first_field=false?>
            <?php if (($field["type"] == "container")||($field["type"] == "iframe")):?>
              <td colspan="2">
              <?php echo $field["field"]?>
            <?php else:?>
              <td style="width:120px;" class="littletableheader"><?php echo $field["label"]?></td>
              <td style="padding:1px;" class="littletablerow" <?php echo $field["field_td"]?>>
              <?php echo $field["field"]?>&nbsp;
            <?php endif;?>
          <?php else:?>
            <?php echo $field["field"]?>&nbsp;
          <?php endif;?>
        <?php endforeach;//fields?>
      <?php endif;?>
              </td>
            </tr>
    <?php endif;//hidden?>
  <?php endforeach;//field_series?>
          
  <?php if ($group["group_name"] != "ungrouped"):?>
            <tr>
              <td colspan="2"></td>
            </tr>
          </table>

        </td>
      </tr>
  <?php endif;?>
<?php //@EOFS ?>
<?php endforeach;//groups?>
<?php endif;?>
        <tr id='__UTPL__'>
          <td colspan="2" ></td>
        </tr>
<?php if(isset($message)):?>
        <tr>
          <td colspan="2" class="tablerow"><?php echo $message?></td>
        </tr>
<?php endif;?>
      </table>
      <?php echo $form_scripts?>
      </div>
      <div class="mainfooter">
        <div>
          <div style="float:left"><?php echo $container_bl ?></div>
          <div style="float:right"><?php echo $container_br?></div>
        </div><div style="clear:both;"></div>
      </div>
    </td>
  </tr>
</table>
<?php echo $form_end?>