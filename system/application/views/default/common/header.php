	<div id="header">
	<table align="center" border=0 width="99%" cellpadding=0 cellspacing=0>
	<tr>
	    <td align='center'><h2><?=$this->datasis->traevalor("TITULO1")  ?></h2></td>
		<td align='center'><h2><?="EJERCICIO ".$this->datasis->traevalor("EJERCICIO")  ?></h2></td>
	</tr>
	<tr>
	    <td align='center'><p class="miniblanco1"><?=$this->datasis->traevalor("TITULO2")."<br>".$this->datasis->traevalor("TITULO3")."<br>RIF ".$this->datasis->traevalor("RIF")?><p></td>
	    
	    <td><?=$idus ?></td>
	</tr>
	

	
	</table>
	
	
	</div>
	<?php echo $menu ?>
