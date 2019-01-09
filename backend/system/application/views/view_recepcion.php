<?php

$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:
 
if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

echo $form_scripts;
echo $form_begin?>
<table align='center'>
	<tr>
		<td align=right>
			<?php echo $container_tr?>
		</td>
	</tr>
	<tr>
		<td>
          <table width="100%"  style="margin:0;width:100%;">
            <tr>
            <td colspan=13 class="littletableheader">Encabezado</td>
            </tr>
            <tr>
              <td width="100" class="littletablerowth"><?=$form->huesped->label ?></td>
              <td colspan="3" class="littletablerow"><?=$form->huesped->output ?></td>
              <td class="littletablerow"><span class="littletablerowth">
                <?=$form->folio->label ?>
              </span></td>
              <td colspan="3" class="littletablerow"><?=$form->folio->output ?></td>
            </tr>
            <tr>
              <td class="littletablerowth"><?=$form->fecha_in->label ?></td>
              <td width="100" class="littletablerow"><?=$form->fecha_in->output ?></td>
              <td width="119" class="littletablerowth"><?=$form->cuenta->label ?></td>
              <td class="littletablerow"><?=$form->cuenta->output ?></td>
              <td class="littletablerow"><span class="littletablerowth">
                <?=$form->otro->label ?>
              </span></td>
              <td colspan="3" class="littletablerow"><?=$form->otro->output ?></td>
            </tr>
            <tr>
              <td class="littletablerowth"><?=$form->fecha_ou->label ?></td>
              <td class="littletablerow"><?=$form->fecha_ou->output ?></td>
              <td class="littletablerowth"><?=$form->habit->label ?></td>
              <td class="littletablerow"><?=$form->habit->output ?></td>
              <td class="littletablerow"><span class="littletablerowth">
                <?=$form->total->label ?>
              </span></td>
              <td width="78" class="littletablerow"><?=$form->total->output ?></td>
            </tr>
        </table>  
        
        <?php echo $form->detalle->output ?>
        <?php //echo $detalle ?>
<table  width="100%" style="margin:0;width:100%;" > 
	 <tr>                                                           
	  	<td colspan=13 class="littletableheader">Totales</td>      
	 </tr>                                                          
	 <tr>                                                 
	    <td width="59" class="littletablerowth"><?=$form->saldo->label ?>       &nbsp;</td>
	   	<td width="18" class="littletablerow"  ><?=$form->saldo->output ?>      &nbsp;</td>	
    </tr>
	  <td>
	<tr>
<table>
<?php echo $container_bl ?>
<?php echo $container_br ?>
<?php endif; ?>