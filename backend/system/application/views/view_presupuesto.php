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
              <td width="139" class="littletablerowth"><?=$form->numero->label ?></td>
              <td width="204" class="littletablerow"><?=$form->numero->output ?></td>
              <td width="96" 	class="littletablerowth"><?=$form->cliente->label ?></td>
              <td width="137" class="littletablerow"><?=$form->cliente->output ?></td>
              <td width="50" 	class="littletablerowth"><?=$form->rifci->label ?></td>
              <td width="300" colspan="2" class="littletablerow"><?=$form->rifci->output ?></td>
            </tr>
            <tr>
              <td class="littletablerowth"><?=$form->fecha->label ?></td>
              <td class="littletablerow"><?=$form->fecha->output ?></td>
              <td class="littletablerowth"><?=$form->nombre->label ?></td>
              <td colspan="5" class="littletablerow"><?=$form->nombre->output ?></td>
            </tr>
            <tr>
              <td class="littletablerowth"><?=$form->vende->label ?></td>
              <td class="littletablerow"><?=$form->vende->output ?></td>
              <td class="littletablerowth"><?=$form->direc->label ?></td>
              <td colspan="3" class="littletablerow"><?=$form->direc->output ?></td>
            </tr>
            <tr>
              <td class="littletablerowth"><?=$form->peso->label ?></td>
              <td class="littletablerow"><?=$form->peso->output ?></td>
              <td class="littletablerowth"><?=$form->dire1->label ?></td>
              <td colspan="3" class="littletablerow"><?=$form->dire1->output ?></td>
            </tr>
	     </table>
          <?php echo $form->detalle->output ?>
          <?php //echo $detalle ?>
         <table  width="100%" style="margin:0;width:100%;" > 
	    <tr>                                                           
	  	  <td colspan=9 class="littletableheader"><div align=""> Totales</div></td>      
	    </tr>                                                          
	    <tr>                                                 
          <td width="265" class="littletablerow"><span class="littletablerow">
            <?=$form->condi1->output ?>
          </span></td>
		  <td width="123" class="littletablerowth"><?=$form->iva->label ?></td>
		  <td width="136" class="littletablerow"><?=$form->iva->output ?></td>
		  <td width="97" class="littletablerowth" ><?=$form->subtotal->label ?></td>
		  <td width="328" class="littletablerow" ><?=$form->subtotal->output ?></td>
		 </tr>
       <tr>
         <td class="littletablerow"><span class="littletablerow">
           <?=$form->condi2->output ?>
         </span></td>
	 	 <td class="littletablerowth"><?=$form->inicial->label ?></td>
	 	 <td class="littletablerow"><?=$form->inicial->output ?></td>
		 <td class="littletablerowth" ><?=$form->total->label ?></td>
		 <td class="littletablerow"><?=$form->total->output ?></td>
		 </tr>
</table>

<?php echo $form_end?>
<?php echo $container_bl ?>
<?php echo $container_br ?>
	  <td>
	<tr>
<table>
<?php endif; ?>
