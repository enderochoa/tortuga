<tr>
			  	<td class="littletablerowth"><?=$form->fecha->label  ?>*&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->fecha->output ?>&nbsp; </td>
			    <td class="littletablerowth"><?=$form->status->label       ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->status->output      ?>&nbsp; </td>
				</tr>
			  <tr>
			    <td class="littletablerowth">                <?=$form->codigoadm->label  ?>*&nbsp;</td>
			    <td class="littletablerow" id='td_codigoadm'><?=$form->codigoadm->output ?>&nbsp;</td>
			    <td class="littletablerowth">                <?=$form->fondo->label     ?>*&nbsp;</td>
			    <td class="littletablerow" >                 <?=$form->fondo->output    ?>&nbsp;</td>
			  </tr>
			  <tr>
			    <td class="littletablerowth">         <?=$form->descrip->label ?>&nbsp;</td>
			    <td class="littletablerow" colspan=3 ><?=$form->descrip->output ?>&nbsp;</td>
			  </tr>
			  
			  
			  <tr>
     			<td class="littletableheaderb">Partida           </td>
     			<td class="littletableheaderb">Ordinal           </td>
					<td class="littletableheaderb">Cuenta            </td>
     			<td class="littletableheaderb">Referencia        </td>
			    <td class="littletableheaderb">Concepto          </td>
			    <td class="littletableheaderb">Monto             </td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletableheaderb">&nbsp;</td>
			    <?php } ?>
			  </tr>