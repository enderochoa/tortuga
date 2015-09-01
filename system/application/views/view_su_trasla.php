<?php
$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:
$link=site_url('presupuesto/requisicion/getadmin'); 

foreach($form->detail_fields['su_ittrasla'] AS $ind=>$data)
	$campos[]=$data['field'];
$campos='<tr id="tr_su_ittrasla_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
$campos=str_replace("\n",'',$campos);
$campos.=' <td class="littletablerow"><a href=# onclick="del_su_ittrasla(<#i#>);return false;">'.image('delete.jpg',"Haz Click aqui para eliminar este item",array("border"=>0,"class"=>"eliminait")).'</a></td></tr>';
$campos=$form->js_escape($campos);
if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;
echo $form_begin; 
if($form->_status!='show'){
    $uri  =$this->datasis->get_uri();
    $idt=$this->datasis->dameval("SELECT id FROM modbus WHERE idm='view_su_itsumi' AND uri='$uri'");
    $modblink=site_url('/buscar/index/'.$idt.'/<#i#>');
?>

<style >
.ui-autocomplete {
    max-height: 150px;
    overflow-y: auto;
    max-width: 600px;
  }
   html.ui-autocomplete {
    height: 150px;
    width: 600px;
  }
</style>
<script language="javascript" type="text/javascript">
su_ittrasla_cont=<?=$form->max_rel_count['su_ittrasla'] ?>;
	datos    ='';
	com=false;
	
  $(function() {
  	$(".inputnum").numeric(".");
  	
  	$(document).keydown(function(e){
		if (18 == e.which) {
			com=true;
			//c = String.fromCharCode(e.which);
			return false;
		}
		if (com && (e.which == 61 || e.which == 107)){
		  add_su_ittrasla();
		  a=su_ittrasla_cont-1;
		  $("#codigo_"+a).focus();
			com=false;
			return false;
			}else if (com && e.which != 16 && e.which == 17){
				com=false;
			}
			return true;
	});
		
  	$.post("<?=site_url('suministros/sumi/autocomplete')?>",{ partida:"" },function(data){
	    datos=jQuery.parseJSON(data);
	});
  });
  
    function modbusdepen(i){
	    var id = i.toString();
	    var caub;
	    caub=$("#de").val();
	    var link='<?=$modblink ?>/'+caub;
	    link =link.replace(/<#i#>/g,id);
	    vent=window.open(link,'ventbuscarsumi','width=800,height=600,scrollbars=Yes,status=Yes,resizable=Yes,screenx=5,screeny=5'); 
	    vent.focus(); 
	    document.body.setAttribute('onUnload','vent=window.open(\'about:blank\',\'ventbuscarppla\');vent.close();');
    }
  
    function auto(){
	$("input[name^='codigo_']").focus(function(){
		id=this.name.substr(8,100);
		$( "#codigo_"+id).autocomplete({
			minLength: 0,
			source: datos,
			focus: function( event, ui ) {
			    $( "#codigo_"+id).val( ui.item.codigo );
			    $( "#descrip2_"+id).val( ui.item.descrip );
			    return false;
			},
			select: function( event, ui ) {
				$( "#codigo_"+id).val( ui.item.codigo );
				return false;
			}
		})
		.data( "autocomplete" )._renderItem = function( ul, item ) {
		    return $( "<li></li>" )
		    .data( "item.autocomplete", item )
		    .append( "<a>" + item.codigo+ " - " + item.descrip + "</a>" )
		    .appendTo( ul );
		};
	});
	
	$("input[name^='codigo_']").focusout(function(){
		id=this.name.substr(7,100);
		$( "#cant_"+id).focus();
	});
    }

  $(document).ready(function(){
	auto();	
  });

function cal_total(){
	arr=$('input[name^="codigo_"]');
	total=0;
	jQuery.each(arr, function() {
		nom=this.name
		pos=this.name.lastIndexOf('_');
		if(pos>0){
		    id= this.name.substring(pos+1);
		    if((isNaN($("#cant_"+id).val()))||($("#cant_"+id).val()==''))$("#cant_"+id).val(0);
		    cant=parseFloat($("#cant_"+id).val());
		    total+=cant;
		}
	});
	$("#total").val(total);
}

function add_su_ittrasla(){
	var htm = <?=$campos ?>;
	can = su_ittrasla_cont.toString();
	con = (su_ittrasla_cont+1).toString();
	cin = (su_ittrasla_cont-1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__UTPL__").before(htm);
	$("#cant_"+can).numeric(".");
	
	su_ittrasla_cont=su_ittrasla_cont+1;
	auto();
}
					
function del_su_ittrasla(id){
	id = id.toString();
	$('#tr_su_ittrasla_'+id).remove();
}
</script>
<?php } ?>

<table align='center'width="80%" >
	<tr>
		<td>
			<table width="100%">
				<tr>
					<td align=left>
						<?php echo $container_tl?>
					</td>
					<td align=right>
						<?php echo $container_tr?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table width="100%"  style="margin:0;width:100%;">
			  <tr>
			    <td colspan=6 class="bigtableheader">Traslado Nro. <?php  echo str_pad(trim($form->numero->output),8,0,STR_PAD_LEFT) ?></td>
			  </tr>
			  <tr>
				<td class="littletablerowth"><?=$form->de->label    ?>&nbsp;</td>
				<td class="littletablerow">  <?=$form->de->output   ?>&nbsp;</td>
				<td class="littletablerowth"><?=$form->fecha->label ?>&nbsp;</td>
				<td class="littletablerow">  <?=$form->fecha->output?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletablerowth"><?=$form->para->label   ?>&nbsp;</td>
				<td class="littletablerow">  <?=$form->para->output  ?>&nbsp;</td>
				<td class="littletablerowth">&nbsp;</td>
				<td class="littletablerow">  &nbsp;</td>
			</tr>
			    <td class="littletablerowth">        <?=$form->concepto->label     ?>&nbsp;</td>
			    <td class="littletablerow" colspan=3><?=$form->concepto->output    ?>&nbsp;</td>
			</tr>
			  
	    </table><br />

		<table width='100%'>
     		<tr>
			<td class="littletableheaderb">C&oacute;digo       </td>
			<td class="littletableheaderb">Descripci&oacute;n  </td>
			<td class="littletableheaderb">Cantidad            </td>
			<?php if($form->_status!='show') {?>
			<td class="littletableheaderb">&nbsp;</td>
			<?php } ?>
			  </tr>
			  <?php for($i=0;$i<$form->max_rel_count['su_ittrasla'];$i++) {
				$obj0="codigo_$i";
				$obj1="descrip2_$i"; 
				$obj2="cant_$i";
			  ?>
			  <tr id='tr_su_ittrasla_<?=$i ?>'>
			    <td class="littletablerow"><?=$form->$obj0->output ?></td>
			    <td class="littletablerow"><?=$form->$obj1->output ?></td>
			    <td class="littletablerow" align="right"><?=$form->$obj2->output ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow"><a href=# onclick='del_su_ittrasla(<?=$i ?>);return false;'><?=image('delete.jpg','Haz Click aqui para eliminar este item.</br>De esta manera se eliminara toda la fila seleccionada',array("border"=>0,"class"=>"eliminait"))?></a></td>
			    <?php } ?>
			  </tr>
			  <?php } ?>

			  <tr id='__UTPL__'>
			    <td class="littletablefooterb" >&nbsp;</td>
			    <td class="littletablefooterb" >&nbsp;</td>
			    <td class="littletablefooterb" align="right"><?=$form->total->output  ?>&nbsp;</td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablefooterb" align="right">&nbsp;</td>
			    <?php } ?>
			  </tr>
	    </table>
		<?php echo $form_end     ?>
		<?php echo $container_bl ?>
		<?php echo $container_br ?>
		<td>
	<tr>
<table>
<?php endif; ?>
