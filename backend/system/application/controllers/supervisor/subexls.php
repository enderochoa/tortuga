<?php
//la funcion ejecuta() es la que actualiza de la tabla sinvactu a la tabla prueba
class subexls extends Controller{
	var $upload_path;
	function subexls(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->load->library("path");
		$path=new Path();
		$path->setPath($this->config->item('uploads_dir'));
		$path->append('/archivos');
		$this->upload_path =$path->getPath().'/';
	}

	function index(){
		redirect("supervisor/subexls/load");
	}

	function actualiza(){

		if((isset($_POST['cols'])) && (isset($_POST['dir']))){
			$cols=explode(',',$_POST['cols']);
			$dir=$_POST['dir'];
			$this->load->library("Spreadsheet_Excel_Reader");

			$data = new Spreadsheet_Excel_Reader();
			$data->setOutputEncoding('CP1251');
			$data->read($dir);
			error_reporting(E_ALL ^ E_NOTICE);

			$colss=array();
			for ($i = 0; $i <= $data->sheets[0]['numRows']; $i++) {
					$ref=0;
					for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
						$t=$j-1;
						$campo="campo$t";
						if($_POST[$campo]!='ignorar'){
							$campo2=$_POST[$campo];
							$colss[$campo]=$campo2;
						}
						if(!empty($data->sheets[0]['cells'][$i][$j])){ $ref++; }

					}
					if($ref>2){break;}
				}

			$fields=implode(',',$colss);
			$mSQL="truncate sinvactu";
			$mSQL=$this->db->query($mSQL);
		//print_r($colss);
		$decimal13=array("base1","base2","base3","base4","precio1","precio2","precio3","precio4","margen1","margen2","margen3","margen4","costo");
		$decimal6=array("iva");
		//print_r($decimal13);
		$mSQL='';
		if(in_array('codigo',$colss)){
				for ($i = 0; $i <= $data->sheets[0]['numRows']; $i++) {
						$ref=0;
						$values='';
						$colss='';
						for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
							$t=$j-1;
							$campo="campo$t";
							if($_POST[$campo]!='ignorar'){
								$colss[$campo]=$data->sheets[0]['cells'][$i][$j];
								if((in_array($_POST[$campo],$decimal13))){
									if(is_numeric($data->sheets[0]['cells'][$i][$j])&&($data->sheets[0]['cells'][$i][$j]!=0)){
										$colss[$campo]=$data->sheets[0]['cells'][$i][$j];
									}else
									{
										$colss[$campo]='null';
									}
								}elseif(in_array($_POST[$campo],$decimal6))
								{
									if(is_numeric($data->sheets[0]['cells'][$i][$j]))
									{
										$colss[$campo]=$data->sheets[0]['cells'][$i][$j];
									}else
									{
										$colss[$campo]='null';
									}
								}else
								{
									$colss[$campo]="'".$data->sheets[0]['cells'][$i][$j]."'";
								}
							}
							if(!empty($data->sheets[0]['cells'][$i][$j])){ $ref++; }
						}
						if($ref>2){
							$values=implode(",",$colss);
							$mSQL="INSERT IGNORE INTO sinvactu ($fields) VALUES ($values)";
							$mSQL2=$this->db->query($mSQL);
						}


				}
				if($this->ejecuta()==1){
					$resp= "s.i";
				}else{
					$resp= "ERROR: En este momento no se puede relizar la operacion.";
				}
			}else{
				$resp="ERROR: Debe de Seleccionar El valor Codigo para algun campo";
			}
		}else{
		$resp= "ERROR: En este momento no se puede relizar la operacion.";
		}
		//$resp=$_POST['campo0'];
		echo $resp;
		//redirect("supervisor/sinvactu/load");
	}

	function ejecuta(){
		if($this->respaldo()==1)
		{
			$this->db->select("a.grupo,a.descrip,a.descrip2,a.costo,a.codigo,a.precio1,a.precio2,a.precio3,a.precio4,a.margen1,a.margen2,a.margen3,a.margen4,a.base1,a.base2,a.base3,a.base4,a.antmargen1,a.antmargen2,a.antmargen3,a.antmargen4,a.antcosto,a.iva,a.clave,a.antiva");
			$this->db->from("sinvactu AS a");
			$this->db->join("sinv AS b","b.codigo=a.codigo");
			$query = $this->db->get();
			$data=array();
			foreach ($query->result_array() as $row)
			{
				$ban=false;
				$ban3=false;
				$salida=$row['codigo'];
				$update="UPDATE sinv SET";
				$codigo=$row['codigo'];
				$update.=" codigo='$codigo'";

				if(!empty($row['descrip']))
				{
					$ban=true;
					$descrip=$row['descrip'];
					$update.=", descrip='$descrip'";
					$salida.="descrip";
				}
				if(!empty($row['descrip2']))
				{
					$ban=true;
					$descrip2=$row['descrip2'];
					$update.=", descrip2='$descrip2'";
					$salida.="descrip2";
				}
				if(!empty($row['grupo']))
				{
					$ban=true;
					$grupo=$row['grupo'];
					$update.=", grupo='$grupo'";
					$salida.="grupo";
				}
				if(!empty($row['clave']))
				{
					$ban=true;
					$clave=$row['clave'];
					$update.=", clave='$clave'";
					$salida.="clave";
				}


				if(!empty($row['iva']))
					$iva=$row['iva'];
				elseif(!empty($row['antiva'])){
					$ban3=true;
					$iva=$row['antiva'];
					}else
				 		$iva=null;

				if(!empty($iva)){
					if((!empty($row['costo']))&&$row['costo']!=0)$costo=$row['costo'];
					for($i=1;$i <= 4;$i++){
						$margen=null;
						$precio=null;
						$base=null;
						$ban2=false;

						if( ((!empty($row['costo']))||$row['costo']!=0)&&((!empty($row["precio$i"]))||$row["precio$i"]!=0)){
							$precio=$row["precio$i"];
							$base=$this->_base($iva,$precio);
							$margen=$this->_margen($costo,$base);
							$ban2=true;
							$ban=true;
						}elseif(!empty($row['costo'])){
							if(!empty($row["margen$i"])){
								$margen=$row["margen$i"];
								$base=$this->_base2($costo,$margen);
								$precio=$this->_precio($iva,$base);
								$ban2=true;
								$ban=true;
							}elseif(!empty($row["base$i"])){
								$base=$row["base$i"];
								$margen=_margen($costo,$base);
								$precio=_precio($iva,$base);
								$ban2=true;
								$ban=true;
							}elseif(!empty($row["antmargen$i"])){
								$margen=$row["antmargen$i"];
								$base=$this->_base2($costo,$margen);
								$precio=$this->_precio($iva,$base);
								$ban2=true;
								$ban=true;
							}elseif(!empty($row["antbase$i"])){
								$base=$row["antbase$i"];
								$margen=_margen($costo,$base);
								$precio=_precio($iva,$base);
								$ban2=true;
								$ban=true;
							}else{
							}
						}elseif(!empty($row["precio$i"])){
							$precio=$row["precio$i"];
							if(!empty($row["antcosto"])){
								$costo=$row["antcosto"];
								$base=$this->_base($iva,$precio);
								$margen=$this->_margen($costo,$base);
								$ban2=true;
								$ban=true;
							}elseif(!empty($row["margen$i"])){
								$margen=$row["margen$i"];
								$base=_base($iva,$precio);
								$costo=_costo($base,$margen);
								$ban2=true;
								$ban=true;
								$ban3=true;
							}elseif(!empty($row["base$i"])){
								//NO SE PUEDE CALCULAR LOS 3
							}elseif(!empty($row["antmargen$i"])){
								$margen=$row["antmargen$i"];
								$base=_base($iva,$precio);
								$costo=_costo($base,$margen);
								$ban2=true;
								$ban=true;
								$ban3=true;
							}elseif(!empty($row["antbase$i"])){
								//NO SE PUEDE CALCULAR LOS 3
							}else{
							}
						}else{
						}
						if($ban2){
							$update.=", base$i=";
							if(is_null($base))$update.='null';else $update.=$base;
							$update.=", margen$i=";
							if(is_null($margen))$update.='null';else $update.=$margen;
							$update.=", precio$i=";
							if(is_null($precio))$update.='null';else $update.=$precio;
							$salida.=", base$i, margen$i, precio$i";
						}
					}

					if($ban3){
						$salida.=", ultimo, iva";
						$update.=", ultimo=$costo";
						$update.=", iva=";
						if(is_null($iva))$update.='null';$update.=$iva;
					}
				}else{
				}
				$update.=" WHERE codigo='$codigo'";
				if($ban){
					//echo $salida."</br>";
					//echo $update."</br>";
					$mSQL2=$this->db->query($update);
				}
			}
			return 1;
		}else{
			return 0;
		}
	}

	function respaldo(){
		$actualizo=$this->db->query("
		UPDATE sinvactu AS a
		LEFT JOIN sinv b
		ON a.codigo = b.codigo
		SET
		a.antdescrip =b.descrip,
		a.antdescrip2=b.descrip2,
		a.antclave   =b.clave,
		a.antgrupo   =b.grupo,
		a.antprecio1 =b.precio1,
		a.antprecio2 =b.precio2,
		a.antprecio3 =b.precio3,
		a.antprecio4 =b.precio4,
		a.antmargen1 =b.margen1,
		a.antmargen2 =b.margen2,
		a.antmargen3 =b.margen3,
		a.antmargen4 =b.margen4,
		a.antbase1   =b.base1,
		a.antbase2   =b.base2,
		a.antbase3   =b.base3,
		a.antbase4   =b.base4,
		a.antcosto   =b.ultimo,
		a.antiva     =b.iva
		");
		return $actualizo;
	}

	function deshacer(){
		$actualizo=$this->db->query("
		UPDATE sinv AS a
		JOIN sinvactu b
		ON a.codigo = b.codigo
		SET
		a.descrip  = b.antdescrip,
		a.descrip2 = b.antdescrip2,
		a.clave    = b.antclave,
		a.grupo    = b.antgrupo,
		a.precio1  = b.antprecio1,
		a.precio2  = b.antprecio2,
		a.precio3  = b.antprecio3,
		a.precio4  = b.antprecio4,
		a.margen1  = b.antmargen1,
		a.margen2  = b.antmargen2,
		a.margen3  = b.antmargen3,
		a.margen4  = b.antmargen4,
		a.base1    = b.antbase1,
		a.base2    = b.antbase2,
		a.base3    = b.antbase3,
		a.base4    = b.antbase4,
		a.ultimo   = b.antcosto,
		a.iva      = b.antiva
		");
		echo $actualizo;
	}

	function _costo($base,$margen){
		$return=($base* (100-$margen)) / 100;
		return $return;
	}

	function _base2($costo,$margen){
		$return=($costo*100)/(100-$margen);
		return $return;
	}

	function _base($iva,$precio){
	  $return=($precio*100)/(100+$iva);
		return $return;
	}

	function _margen($costo,$base){
		$return=100-($costo*100)/$base;
		return $return;
	}

	function _precio($iva,$base){
		$return=$base*(($iva+100)/100);
		return $return;
	}

	function load(){
		$this->rapyd->load("dataform");
		$link=site_url('supervisor/subexls/deshacer');

		$script ='
		function deshacer(){
			a=confirm("�Esta Seguro que de desea deshacer la ultima actualizaci&oacute;n realizada?");
			if(a){
				$.ajax({
					url: "'.$link.'",
					success: function(msg){
						if(msg){
							alert("Fue realizada exitosamente la operaci&oacute;n");
						}
						else{
							alert("La operaci&oacute;n no pudo ser completada. Intente mas tarde");
						}
					}
				});
			}
		}';

		$form = new DataForm("supervisor/subexls/read");
		$form->title('Cargar Archivo de Productos (xls)');
		$form->script($script);

		$form->archivo = new uploadField("Archivo","archivo");
		$form->archivo->upload_path   = $this->upload_path;
		$form->archivo->allowed_types = "xls";
		$form->archivo->delete_file   =false;
		$form->archivo->rule   ="required";

		$form->submit("btnsubmit","Cargar");
		$form->build_form();

		$salida='<a href="javascript:deshacer();" title="Haz Click para Deshacer La Ultima Actualizaci&oacute;n" onclick="">Deshacer La Ultima Actualizaci&oacute;n</a>';

		$data['content'] = $form->output.$salida;
		$data['title']   = "<h1>Actualizaci&oacute;n de Inventario</h1>";
		$data["head"]    = script("jquery.pack.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function read($action='',$numero=''){
		$this->load->library("Spreadsheet_Excel_Reader");
		$this->rapyd->load("datagrid2","dataform");

		$salida=anchor('supervisor/subexls', 'Atras');
		$type='';
		if(isset($_FILES['archivoUserFile']['type']))$type=$_FILES['archivoUserFile']['type'];
		//print_r($_FILES);
		if( $type=='application/vnd.ms-excel'){
			$name=$_FILES['archivoUserFile']['name'];
			$dir=".././".$name;

			$name=$_FILES['archivoUserFile']['name'];


			if (copy($_FILES['archivoUserFile']['tmp_name'], 'uploads/'.$name)){
				$uploadsdir =getcwd().'/uploads/';
				$filedir    =$uploadsdir.$name;
				$tmp=$dir;
				$tmp=$filedir;
				//$_FILES['archivoUserFile']['tmp_name'];
				$data = new Spreadsheet_Excel_Reader();
				$data->setOutputEncoding('CP1251');
				$data->read($tmp);
				error_reporting(E_ALL ^ E_NOTICE);
				$cols=array();

				for ($i = 0; $i <= $data->sheets[0]['numRows']; $i++) {
					$ref=0;
					for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
						$cols[$j-1]=$data->sheets[0]['cells'][$i][$j];
						if(!empty($data->sheets[0]['cells'][$i][$j])){ $ref++; }
					}
					if($ref>2){break;}
				}

				$c=0;
				for ($i = 0; $i <= $data->sheets[0]['numRows']; $i++) {
					$ref=0;
					$j=1;
					foreach ($cols as $col) {
						$data3[$i][$col]= $data->sheets[0]['cells'][$i][$j];
						if(!empty($data->sheets[0]['cells'][$i][$j])){ $ref++; }
						$j++;
					}

					if(($ref>2)&&((implode(' ',$data3[$i]))!=(implode(' ',$cols)))){
						$data4[$c]=$data3[$i];
						$c++;
					}
				}

				switch($action){
					case 'ITFAC2':{

						$this->itfac2($data4,$numero,'itfac2');
						break;
					}
					case 'itfac3':{

						$this->itfac2($data4,$numero,'itfac3');
						break;
					}
				}

			}

		}else{
			$data2['content'] = $salida;
		}

		if(isset($data2['content'])){
			$data2['title']   = "<h1>Actualizaci&oacute;n de Inventario</h1>";
			$data2["head"]    = script("jquery.pack.js").$this->rapyd->get_head();
			$this->load->view('view_ventanas', $data2);
		}
	}

	function itfac2($data,$numero,$retorna){
//print_r($data);
//		exit();

		$this->rapyd->load("dataobject");

		$do = new DataObject("ocompra");
		$do->rel_one_to_many('itfac', 'itfac', array('numero'=>'nocompra'));
		$do->load($numero);

		$reteiva_prov=$do->get('reteiva_prov');
		$creten      =$do->get('creten');
		$total2      =$do->get('total2');
		$ivag        =$do->get('ivag');
		$ivar        =$do->get('ivar');
		$ivaa        =$do->get('ivaa');

		$i=$ttotal2=$ttemp=0;
		$error='';
		$ivaplica=$this->datasis->ivaplica();

		foreach($data as $row){

			$temp=array();
			foreach($row as $cols){
				$temp[]=$cols;
			}
			if(strlen($temp[3])>0){
				$do->set_rel('itfac','nocompra'  ,                   $numero    ,$i);
				$do->set_rel('itfac','factura'   ,                   $temp[3]   ,$i);
				$do->set_rel('itfac','controlfac',                   $temp[4]   ,$i);
				$do->set_rel('itfac','fechafac'  ,  human_to_dbdate($temp[2])   ,$i);

				$temp[11]=1*$temp[11];

				$do->set_rel('itfac','exento'    ,  0           ,$i);
				$do->set_rel('itfac','ivar'      ,  0           ,$i);
				$do->set_rel('itfac','ivaa'      ,  0           ,$i);
				$do->set_rel('itfac','ivag'      ,  0           ,$i);
				$do->set_rel('itfac','uivar'     ,  'N'         ,$i);
				$do->set_rel('itfac','uivaa'     ,  'N'         ,$i);
				$do->set_rel('itfac','uivag'     ,  'N'         ,$i);
				$do->set_rel('itfac','ureten'    ,  'N'         ,$i);
				$do->set_rel('itfac','uimptimbre',  'N'         ,$i);

				$ttemp=$temp[11]+round($ttemp,2);
				$temp[9] =str_replace(',','',$temp[9]);
				$temp[11]=str_replace(',','',$temp[11]);
				switch(1*$temp[10]*100){
					case $ivaplica['redutasa']:{
						$do->set_rel('itfac','ivar'     ,  1*$temp[11]   ,$i);
						$do->set_rel('itfac','uivar'    ,  'S'         ,$i);
						break;
					}
					case $ivaplica['tasa']:{
						$do->set_rel('itfac','ivag'     ,  1*$temp[11]   ,$i);
						$do->set_rel('itfac','uivag'    ,  'S'         ,$i);
						break;
					}
					case $ivaplica['sobretasa']:{
						$do->set_rel('itfac','ivaa'     ,  1*$temp[11]   ,$i);
						$do->set_rel('itfac','uivag'    ,  'S'         ,$i);
						break;
					}
				}
				$reteiva  =1*$temp[11]*$reteiva_prov/100;
				$total2_t =1*$temp[9]+1*$temp[11];
				$ttotal2  =$total2_t+round($ttotal2,2);
				$do->set_rel('itfac','reteiva'  ,  $reteiva   ,$i);
				$do->set_rel('itfac','subtotal' ,  1*$temp[9] ,$i);
				$do->set_rel('itfac','total2'   ,  $total2_t  ,$i);

				$rete=$this->datasis->damerow("SELECT base1,tari1,pama1 FROM rete WHERE codigo='$creten'");
				if(substr($creten,0,1)=='1')$reten=round($temp[9]*$rete['base1']*$rete['tari1']/10000,2);
				else $reten=round(($temp[9]-$rete['pama1'])*$rete['base1']*$rete['tari1']/10000,2);

				if($reten < 0)$reten=0;
				$do->set_rel('itfac','reten'  , $reten ,$i);
				$do->set_rel('itfac','ureten' ,  'S'   ,$i);

				$do->set_rel('itfac','total'  ,  $total2_t-$reten-$reteiva   ,$i);
				$i++;
			}
		}
		//exit("ttotal2:".$ttemp);
		//if(abs($ttotal2-$total2)>=0.4)
		//$error.="El monto total cargado ($ttotal2) es diferente al Comprometido ($total2) ";
		//if(((round((round($s,2) -round($subtotal,2)),2) > 0.02))|| (round(round($subtotal,2)-(round($s ,2)),2) > 0.02) )$error.="<div class='alert'><p>La Suma de los Subtotales ($subtotal) de las facturas es diferente al subtotal ($s) de la orden de pago</p></div>";


		if(empty($error)){
			$do->save();
			redirect("presupuesto/$retorna/dataedit/modify/$numero");
		}else{
			//logusu('ocompra',"Comprometio Orden de Compra Nro $id. con ERROR:$error ");
			$data['content'] = "<div class='alert'>".$error."</div>".anchor("presupuesto/$retorna/load/$numero",'Regresar');
			$data['title']   = " Error al cargar archivo de facturas ";
			$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js");
			$this->load->view('view_ventanas', $data);
		}
	}

	function mostrar($data4){
		$this->rapyd->load("datagrid2","dataform");
		$form = new DataForm("/supervisor/subexls/actualiza");
		$form->free = new freeField("Campos.Archivo xls","free","Campos de El Sistema");
		//var_dump($data4);
//print_r($data4);
		$grid = new DataGrid2("Archivo Cargado",$data4);

		$j=0;
		$campos.='cols="+$("#cols").val()+"&&dir="+$("#dir").val()';
		foreach ($cols as $col){
			$grid->column("$col" ,"<#$col#>");
			$campo="campo$j";
			$form->$campo = new dropdownField("$col", "campo$j");
			$form->$campo->option("ignorar"   ,"ignorar");
			$form->$campo->option("codigo"    , "codigo");
			$form->$campo->option("costo"     , "costo");
			$form->$campo->option("descrip"   , "descrip");
			$form->$campo->option("descrip2"  , "descrip2");
			$form->$campo->option("clave"     , "clave");
			$form->$campo->option("grupo"     , "grupo");
			$form->$campo->option("iva"     , "iva");

			for($i=1;$i<=3;$i++){
				$form->$campo->option("base$i"   , "Base$i");
				$form->$campo->option("margen$i" , "Margen$i");
				$form->$campo->option("precio$i" , "Precio$i");
			}

			$form->$campo->style='width:150px;';
			$j++;
			$campos.="+"."\"&&".$campo."=\"+$(\"#".$campo."\").val()";
		}
		//echo $campos;
		$form->cols = new inputField("","cols");
		$form->cols->insertValue=implode(',',$cols);
		$form->cols->type='hidden';

		$form->dir = new inputField("","dir");
		$form->dir->insertValue=$dir;
		$form->dir->type='hidden';

		//$form->submit("btnsubmit","Actualizar");
		$link=site_url('supervisor/subexls/actualiza');

		$script ='
		function actu(){
			a=confirm("�Esta Seguro que de desea Actualizar el Inventario ?");
			if(a){
				 $.ajax({
				 type: "POST",
				 processData:false,
					url: "'.$link.'",
					data: "'.$campos.',
					success: function(msg){
						if(msg=="s.i"){
							alert("El Inventario fue Actualizado.");
						}
						else{
							alert(msg);
						}
					}
				});
			}
		}
		';

		$form->script($script);
		$form->button("actualiza","Actualizar","javascript:actu();");

		$form->build_form();
		$grid->build();
		$data2['content'] = $form->output.$salida.$grid->output;
	}

	function instalar(){
		$mSQL='
			CREATE TABLE /*!32312 IF NOT EXISTS*/ `sinvactu` (
		  `codigo` varchar(15) NOT NULL default "",
		  `descrip` varchar(45) default NULL,
		  `clave` varchar(8) default NULL,
		  `descrip2` varchar(45) default NULL,
		  `antdescrip2` varchar(45) default NULL,
		  `grupo` varchar(4) default NULL,
		  `costo` decimal(13,2) unsigned default NULL,
		  `precio1` decimal(13,2) unsigned default NULL,
		  `antcosto` decimal(13,2) unsigned default NULL,
		  `antprecio1` decimal(13,2) unsigned default NULL,
		  `iva` decimal(6,2) unsigned default NULL,
		  `antiva` decimal(6,2) unsigned default NULL,
		  `precio2` decimal(13,2) default NULL,
		  `precio3` decimal(13,2) default NULL,
		  `precio4` decimal(13,2) unsigned default NULL,
		  `base1` decimal(13,2) unsigned default NULL,
		  `base2` decimal(13,2) default NULL,
		  `base3` decimal(13,2) unsigned default NULL,
		  `base4` decimal(13,2) unsigned default NULL,
		  `margen1` decimal(13,2) unsigned default NULL,
		  `margen2` decimal(13,2) unsigned default NULL,
		  `margen3` decimal(13,2) unsigned default NULL,
		  `margen4` decimal(13,2) unsigned default NULL,
		  `antdescrip` varchar(45) default NULL,
		  `antclave` varchar(8) default NULL,
		  `antgrupo` varchar(4) default NULL,
		  `antprecio2` decimal(13,2) unsigned default NULL,
		  `antprecio3` decimal(13,2) unsigned default NULL,
		  `antprecio4` decimal(13,2) unsigned default NULL,
		  `antbase1` decimal(13,2) unsigned default NULL,
		  `antbase2` decimal(13,2) unsigned default NULL,
		  `antbase3` decimal(13,2) unsigned default NULL,
		  `antbase4` decimal(13,2) unsigned default NULL,
		  `antmargen1` decimal(13,2) unsigned default NULL,
		  `antmargen2` decimal(13,2) unsigned default NULL,
		  `antmargen3` decimal(13,2) unsigned default NULL,
		  `antmargen4` decimal(13,2) unsigned default NULL,
		  PRIMARY KEY  (`codigo`)
		)';
		$this->db->simple_query($mSQL);
	}
}
?>
