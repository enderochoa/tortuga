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
              <td width="84" class="littletablerowth"><?=$form->tipo->label ?></td>
              <td width="96" class="littletablerow"><?=$form->tipo->output ?></td>
              <td width="92" class="littletablerowth"><?=$form->cliente->label ?></td>
              <td width="120" class="littletablerow"><?=$form->cliente->output ?></td>
              <td width="58" class="littletablerowth"><?=$form->rifci->label ?></td>
              <td width="303" class="littletablerow"><?=$form->rifci->output ?></td>
            </tr>
            <tr>
              <td class="littletablerowth"><?=$form->numero->label ?></td>
              <td class="littletablerow"><?=$form->numero->output ?></td>
              <td class="littletablerowth"><?=$form->nombre->label ?></td>
              <td colspan="5" class="littletablerow"><?=$form->nombre->output ?></td>
            </tr>
            <tr>
              <td class="littletablerowth"><?=$form->fecha->label ?></td>
              <td class="littletablerow"><?=$form->fecha->output ?></td>
              <td class="littletablerowth"><?=$form->direc->label ?></td>
              <td colspan="3" class="littletablerow"><?=$form->direc->output ?></td>
            </tr>
            <tr>
              <td class="littletablerowth"><?=$form->orden->label ?></td>
              <td class="littletablerow"><?=$form->orden->output ?></td>
              <td class="littletablerowth"><?=$form->dire1->label ?></td>
              <td colspan="3" class="littletablerow"><?=$form->dire1->output ?></td>
            </tr>
	     </table>
          <?php echo $form->detalle->output ?>
          <?php //echo $detalle ?>
         <table  width="100%" style="margin:0;width:100%;" > 
	    <tr>                                                           
	  	  <td colspan=8 class="littletableheader"><div align=""> Totales</div></td>      
	    </tr>                                                          
	    <tr>                                                 
          <td width="191" class="littletablerowth"><?=$form->vence->label ?></td>
		  <td width="352" class="littletablerowth"><?=$form->observaciones->label ?>  </td>
		  <td width="140" class="littletablerowth" ><?=$form->subtotal->label ?> </td>
		 <td width="286" class="littletablerow" ><?=$form->subtotal->output ?> </td>
       </tr>
       <tr>
  		 <td class="littletablerow" ><?=$form->vence->output ?></td>
		 <td class="littletablerow" ><?=$form->observaciones->output ?></td>
		 <td class="littletablerowth"><?=$form->iva->label ?></td>
		 <td class="littletablerow" ><?=$form->iva->output ?></td>
       </tr>
       <tr>
         <td class="littletablerow" >&nbsp;</td>
		 <td class="littletablerow" ><?=$form->observaciones1->output ?></td>
         <td class="littletablerowth"><?=$form->total->label ?></td>
         <td class="littletablerow" ><?=$form->total->output ?></td>
       </tr>
</table>

<?php echo $form_end?>
<?php echo $container_bl ?>
<?php echo $container_br ?>
	  <td>
	<tr>
<table>
<?php endif; ?>
