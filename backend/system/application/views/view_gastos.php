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
<table style="margin:0;width:98%;">
	<tr>
		<td colspan=5 class="littletableheader">Encabezado</td>
	</tr>
	<tr>
		<td class="littletablerowth"><?=$form->fecha->label ?></td>
		<td class="littletablerow"><?=$form->fecha->output ?></td>
		<td class="littletablerowth" align='center'><center><?=$form->status->label ?></center></td>
		<td class="littletablerowth"><?=$form->descrip->label ?></td>
		<td class="littletablerow" style="width:300px;" ><?=$form->descrip->output ?></td>
	</tr>
	<tr>	
		<td class="littletablerowth"><?=$form->numero->label ?></td>
		<td class="littletablerow"><?=$form->numero->output ?></td>
		<td class="littletablerow" align='center'><?=$form->status->output ?></td>
		<td class="littletablerowth">Cuenta</td>
		<td class="littletablerow">&nbsp;</td>
	</tr>
</table>
<?php echo $form->detalle->output ?> <?php //echo $detalle ?>
<table style="margin:0;width:98%;">
	<tr>
		<td colspan=6 class="littletableheader">Totales</td>
	</tr>
	<tr>
		<td class="littletablerowth"><?=$form->debe->label ?></td>
		<td class="littletablerow" ><?=$form->debe->output?></td>
		<td class="littletablerowth"><?=$form->haber->label   ?></td>
		<td class="littletablerow"><?=$form->haber->output  ?></td>
		<td class="littletablerowth"><?=$form->total->label   ?></td>
		<td class="littletablerow"><?=$form->total->output  ?></td>
	</tr>
</table>

<?php echo $form_end?>
<?php echo $container_bl ?>
<?php echo $container_br ?>
		<td>
	<tr>
<table>
<?php endif; ?>