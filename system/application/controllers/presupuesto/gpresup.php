<?php
class gpresup extends Controller {  
	
	var $url='presupuesto/gpresup/';

	function gpresup() {
		parent::Controller();
		$this->load->library("rapyd");
		$this->load->helper('openflash');
		$this->load->database();
	}  
	
	function index(){
		redirect('/presupuesto/gpresup/datagrid');
	}
	
	function datagrid(){
		$this->rapyd->load("datagrid");	  
		$this->rapyd->load("dataform");
		$this->load->helper('openflash');
		
		
		$mSQL = "SELECT a.codigoadm, c.denominacion AS denominacion, sum(a.asignacion+a.aumento-a.disminucion+a.traslados) as presupuesto, 
		sum(a.comprometido) comprometido, 
		sum(a.causado) causado, 
		sum(a.pagado) pagado,
		SUM(a.comprometido)*100/SUM(a.asignacion+a.aumento-a.disminucion+a.traslados) ejecutado,
		SUM((a.asignacion+a.aumento-a.disminucion+a.traslados)-(a.comprometido)) disponible
		FROM presupuesto a JOIN ppla b ON a.codigopres=b.codigo
		JOIN estruadm c ON  c.codigo=a.codigoadm
		WHERE b.movimiento='S'
		GROUP BY codigoadm
		HAVING presupuesto<>0";
	
		$maxval=0;
		$query=$this->db->query($mSQL);
		
		$i=0;
		$cont[0]=$cont[1]=$cont[2]=$cont[3]=$cont[4]=$cont[5]=$cont[6]=0;

		foreach($query->result() as $row ){
			if ($row->presupuesto > $maxval) $maxval=$row->presupuesto;
			$data[$i]['codigoadm']   =$row->codigoadm;
			$data[$i]['denominacion']=$row->denominacion;
			$data[$i]['presupuesto'] =$row->presupuesto;
			$data[$i]['comprometido']=$row->comprometido;
			$data[$i]['causado']     =$row->causado;
			$data[$i]['pagado']      =$row->pagado;			
			$data[$i]['ejecutado']   =$row->ejecutado;
			$data[$i]['disponible']  =$row->disponible;
		 
			$cont[0]+=$row->denominacion;
			$cont[1]+=$row->presupuesto;
			$cont[2]+=$row->comprometido;
			$cont[3]+=$row->causado;
			$cont[4]+=$row->pagado;
			$cont[5]+=$row->ejecutado;
			$cont[6]+=$row->disponible; 
			$i++;
		}
		 $data[$i]['codigoadm']   ='Totales';
		 $data[$i]['denominacion']=$cont[0]; 
		 $data[$i]['presupuesto'] =$cont[1];
		 $data[$i]['comprometido']=$cont[2];
		 $data[$i]['causado']     =$cont[3];
		 $data[$i]['pagado']      =$cont[4];		
		 $data[$i]['ejecutado']   =round($cont[2]*100/$cont[1],2);
		 $data[$i]['disponible']  =$cont[6];
		
		$grid = new DataGrid('Presupuestos',$data);
		
		$uri = anchor($this->url.'datagrid2/<#codigoadm#>','<#codigoadm#>');

		$grid->per_page = count($data);
		$grid->column("C&oacute;digo", $uri                                 ,"align='left'"        );
		$grid->column("Actividad"    , "<#denominacion#>"                   ,"align='left'  NOWRAP");
		$grid->column("Presupuesto"  , "<nformat><#presupuesto#></nformat>" ,"align='right' NOWRAP");
		$grid->column("Comprometido" , "<nformat><#comprometido#></nformat>","align='right' NOWRAP");
		$grid->column("Causado"      , "<nformat><#causado#></nformat>"     ,"align='right' NOWRAP");
		$grid->column("Pagado"       , "<nformat><#pagado#></nformat>"      ,"align='right' NOWRAP");
		$grid->column("%"            , "<nformat><#ejecutado#></nformat>"   ,"align='right' NOWRAP");
		$grid->column("Disponible"   , "<nformat><#disponible#></nformat>"  ,"align='right' NOWRAP");

		$grid->build();
		 
		$grafico = open_flash_chart_object(900,450, site_url("presupuesto/gpresup/grafico/"));
		$data['content']  = $grafico;
		$data['content'] .= $grid->output;
		$data["head"]     = $this->rapyd->get_head();
		$data['title']    = $this->rapyd->get_head()."Presupuestos";
		$this->load->view('view_ventanas', $data);
	}
	
	function grafico(){
		$this->load->library('Graph');
		$mSQL = "SELECT codigoadm, 
					SUM(asignacion+aumento-disminucion+traslados) as presupuesto, 
		                        SUM(comprometido) comprometido, 
		                        SUM(causado) causado, 
		                        SUM(pagado) pagado
		FROM presupuesto a JOIN ppla b ON a.codigopres=b.codigo
		WHERE b.movimiento='S'
		GROUP BY codigoadm
		HAVING presupuesto<>0";
	
	
		$maxval=0;
		$query=$this->db->query($mSQL);
		
		foreach($query->result() as $row ){
		if ($row->presupuesto > $maxval) $maxval=$row->presupuesto;
		 $codigoadm[]=$row->codigoadm;
		 $data_1[]=$row->presupuesto;
		 $data_2[]=$row->comprometido;
		 $data_3[]=$row->causado;
		 $data_4[]=$row->pagado;

		}
						
		$om=1;while($maxval/$om>100) $om=$om*10;
		
		$bar_1 = new bar(75, '#0053A4');
		$bar_2 = new bar(75, '#9933CC');
		$bar_3 = new bar(75, '#639F45');
		$bar_4 = new bar(75, '#C34F33');
		
		$bar_1->key('Presupuesto',10);
		$bar_2->key('Comprometido',10);
		$bar_3->key('Causado'  ,10);
		$bar_4->key('Pagado'  ,10);
		
    
		for($i=0;$i<count($data_1);$i++ ){
			$bar_1->add_data_tip($data_1[$i]/$om, graph::esc( number_format($data_1[$i],2,',','.')));  
			$bar_2->add_data_tip($data_2[$i]/$om, graph::esc( number_format($data_2[$i],2,',','.')));
			$bar_3->add_data_tip($data_3[$i]/$om, graph::esc( number_format($data_3[$i],2,',','.')));
			$bar_4->add_data_tip($data_4[$i]/$om, graph::esc( number_format($data_4[$i],2,',','.')));
			
			$bar_1->links[]= site_url("presupuesto/gpresup/datagrid2/".$codigoadm[$i]);
			$bar_2->links[]= site_url("presupuesto/gpresup/datagrid2/".$codigoadm[$i]);
			$bar_3->links[]= site_url("presupuesto/gpresup/datagrid2/".$codigoadm[$i]);
			$bar_4->links[]= site_url("presupuesto/gpresup/datagrid2/".$codigoadm[$i]);	
		}

		$g = new graph();
		$g->set_is_decimal_separator_comma(1);
		if($maxval>0){
		$g->data_sets[] = $bar_1;
		$g->data_sets[] = $bar_2;
		$g->data_sets[] = $bar_3;
		$g->data_sets[] = $bar_4;
				
		$g->set_x_labels($codigoadm);
		$g->set_x_label_style( 10, '#000000', 2, 1 );
		$g->set_x_axis_steps( 10 );
		$g->set_x_legend('Presupuestos', 14, '#004381' );        
		
		$g->bg_colour = '#FFFFFF';
		$g->set_tool_tip( '#key#<br>Codigo: #x_label# <br>Monto: #tip#' );
		$g->set_y_max(ceil($maxval/$om));
		$g->y_label_steps(5);
		$g->set_y_legend('Presupuesto x '.number_format($om,0,'','.').' (Bs)', 16, '#004381' );
		}else
		$g->title( 'No existen Presupuestos','{font-size:18px; color: #d01f3c}');
		$g->bg_colour='#FFFFFF';
		 echo utf8_encode($g->render());
	}
	
	function datagrid2($codigoadm){
		$this->rapyd->load("datagrid");
		$this->rapyd->load("dataform");
		$this->load->helper('openflash');
		
		$mSQL = "SELECT MID(codigopres,1,7) codigopres, (SELECT MID(denominacion,1,30) e FROM ppla e WHERE e.codigo=MID(a.codigopres,1,7) ) denominacion,
					SUM(asignacion+aumento-disminucion+traslados) as presupuesto, 
		                        SUM(comprometido) comprometido, 
		                        SUM(causado) causado, 
		                        SUM(pagado) pagado, 
					SUM(a.comprometido)*100/SUM(a.asignacion+a.aumento-a.disminucion+a.traslados) ejecutado,
					SUM((a.asignacion+a.aumento-a.disminucion+a.traslados)-(a.comprometido)) disponible
		FROM presupuesto a JOIN ppla b ON a.codigopres=b.codigo
		WHERE b.movimiento='S' AND a.codigoadm='$codigoadm'
		GROUP BY MID(codigopres,1,7)
		HAVING presupuesto<>0";

		$volver = anchor("presupuesto/gpresup/datagrid/","Regresar");

	
		$maxval=0;
		$query=$this->db->query($mSQL);
		
		$i=0;
		$cont[0]=$cont[1]=$cont[2]=$cont[3]=$cont[4]=$cont[5]=0;

		foreach($query->result() as $row ){
		if ($row->presupuesto > $maxval) $maxval=$row->presupuesto;
		 $data[$i]['codigopres']    =$row->codigopres;
		 $data[$i]['denominacion']  =$row->denominacion;
		 $data[$i]['presupuesto']   =$row->presupuesto;
		 $data[$i]['comprometido']  =$row->comprometido;
		 $data[$i]['causado']       =$row->causado;
		 $data[$i]['pagado']        =$row->pagado;
		 $data[$i]['ejecutado']     =$row->ejecutado;
		 $data[$i]['disponible']    =$row->disponible;
		 
		 $cont[0]+=$row->presupuesto;
		 $cont[1]+=$row->comprometido;
		 $cont[2]+=$row->causado;
		 $cont[3]+=$row->pagado;
		 $cont[4]+=$row->ejecutado;
		 $cont[5]+=$row->disponible;
		 
		$i++;
		}
		 $data[$i]['codigopres']   = 'Totales';
		 $data[$i]['denominacion'] = 'Denominacion';
		 $data[$i]['presupuesto']  = $cont[0];
		 $data[$i]['comprometido'] = $cont[1];
		 $data[$i]['causado']      = $cont[2];
		 $data[$i]['pagado']       = $cont[3];
		 $data[$i]['ejecutado']    = round($cont[1]*100/$cont[0],2);
		 $data[$i]['disponible']   = $cont[5];

		$grid = new DataGrid('Presupuestos',$data);
		
		$uri = anchor($this->url."datagrid3/$codigoadm/<#codigopres#>",'<#codigopres#>');

		$grid->per_page = count($data);
		$grid->column("C&oacute;digo", $uri                                 ,'align=left');
		$grid->column("Denominacion" , "denominacion",'align=left');
		$grid->column("Presupuesto"  , "<nformat><#presupuesto#></nformat>", 'align=right' );
		$grid->column("Comprometido" , "<nformat><#comprometido#></nformat>",'align=right' );
		$grid->column("Causado"      , "<nformat><#causado#></nformat>",     'align=right' );
		$grid->column("Pagado"       , "<nformat><#pagado#></nformat>",      'align=right' );
		$grid->column("%"            , "<nformat><#ejecutado#></nformat>",   'align=right' );
		$grid->column("Disponible"   , "<nformat><#disponible#></nformat>",  'align=right' );

		$grid->build();
		 
		$grafico = open_flash_chart_object(900,450, site_url("presupuesto/gpresup/grafico2/$codigoadm"));
		$data['content']  = $grafico;
		$data['content'] .= $volver.$grid->output;
		$data["head"]     = $this->rapyd->get_head();
		$data['title']    = $this->rapyd->get_head()." Presupuestos ";
		$this->load->view('view_ventanas', $data);
	}

	function grafico2($codigoadm){
		$this->load->library('Graph');
		
		$mSQL = "SELECT MID(codigopres,1,7) codigopres, 
					SUM(asignacion+aumento-disminucion+traslados) as presupuesto, 
		                        SUM(comprometido) comprometido, 
		                        SUM(causado) causado, 
		                        SUM(pagado) pagado
		FROM presupuesto a JOIN ppla b ON a.codigopres=b.codigo
		WHERE b.movimiento='S' AND a.codigoadm='$codigoadm'
		GROUP BY MID(codigopres,1,7)
		HAVING presupuesto<>0";

		$maxval=0;
		$query=$this->db->query($mSQL);
		
		foreach($query->result() as $row ){
		if ($row->presupuesto > $maxval) $maxval=$row->presupuesto;
		 $codigopres[]=$row->codigopres;
		 $data_1[]=$row->presupuesto;
		 $data_2[]=$row->comprometido;
		 $data_3[]=$row->causado;
		 $data_4[]=$row->pagado;

		}
						
		$om=1;while($maxval/$om>100) $om=$om*10;
		
		$bar_1 = new bar(75, '#0053A4');
		$bar_2 = new bar(75, '#9933CC');
		$bar_3 = new bar(75, '#639F45');
		$bar_4 = new bar(75, '#C34F33');
		
		$bar_1->key('Presupuesto',10);
		$bar_2->key('Comprometido',10);
		$bar_3->key('Causado'  ,10);
		$bar_4->key('Pagado'  ,10);
		
    
		for($i=0;$i<count($data_1);$i++ ){
			$bar_1->add_data_tip($data_1[$i]/$om, graph::esc( number_format($data_1[$i],2,',','.')));  
			$bar_2->add_data_tip($data_2[$i]/$om, graph::esc( number_format($data_2[$i],2,',','.')));
			$bar_3->add_data_tip($data_3[$i]/$om, graph::esc( number_format($data_3[$i],2,',','.')));
			$bar_4->add_data_tip($data_4[$i]/$om, graph::esc( number_format($data_4[$i],2,',','.')));
			
			$bar_1->links[]= site_url("presupuesto/gpresup/datagrid3/".$codigoadm."/".$codigopres[$i]);
			$bar_2->links[]= site_url("presupuesto/gpresup/datagrid3/".$codigoadm."/".$codigopres[$i]);
			$bar_3->links[]= site_url("presupuesto/gpresup/datagrid3/".$codigoadm."/".$codigopres[$i]);
			$bar_4->links[]= site_url("presupuesto/gpresup/datagrid3/".$codigoadm."/".$codigopres[$i]);
		}
		
		$g = new graph();
		$g->set_is_decimal_separator_comma(1);
		if($maxval>0){
		$g->data_sets[] = $bar_1;
		$g->data_sets[] = $bar_2;
		$g->data_sets[] = $bar_3;
		$g->data_sets[] = $bar_4;
				
		$g->set_x_labels($codigopres);
		$g->set_x_label_style( 10, '#000000', 2, 1 );
		$g->set_x_axis_steps( 10 );
		$g->set_x_legend('Presupuestos', 14, '#004381' );        
		
		$g->bg_colour = '#FFFFFF';
		$g->set_tool_tip( '#key#<br>Codigo: #x_label# <br>Monto: #tip#' );
		$g->set_y_max(ceil($maxval/$om));
		$g->y_label_steps(5);
		$g->set_y_legend('Presupuesto x '.number_format($om,0,'','.').' (Bs)', 16, '#004381' );
		}else
		$g->title( 'No existen Presupuestos','{font-size:18px; color: #d01f3c}');
		$g->bg_colour='#FFFFFF';
		 echo utf8_encode($g->render());
	}


	function datagrid3( $codigoadm, $partida){
		$this->rapyd->load("datagrid");
		$this->rapyd->load("dataform");
		$this->load->helper('openflash');
		
		$mSQL = "SELECT a.codigopres, MID(b.denominacion,1,30) denominacion,
					SUM(asignacion+aumento-disminucion+traslados) as presupuesto, 
		                        SUM(comprometido) comprometido, 
		                        SUM(causado) causado, 
		                        SUM(pagado) pagado, 
					SUM(a.comprometido)*100/SUM(a.asignacion+a.aumento-a.disminucion+a.traslados) ejecutado,
					SUM((a.asignacion+a.aumento-a.disminucion+a.traslados)-(a.comprometido)) disponible
		FROM presupuesto a JOIN ppla b ON a.codigopres=b.codigo
		WHERE b.movimiento='S' AND a.codigoadm='$codigoadm'  AND MID(codigopres,1,7)='$partida'
		GROUP BY codigopres
		HAVING presupuesto<>0";

		$volver = anchor("presupuesto/gpresup/datagrid2/$codigoadm","Regresar");

	
		$maxval=0;
		$query=$this->db->query($mSQL);
		
		$i=0;
		$cont[0]=$cont[1]=$cont[2]=$cont[3]=$cont[4]=$cont[5]=0;

		foreach($query->result() as $row ){
		if ($row->presupuesto > $maxval) $maxval=$row->presupuesto;
		 $data[$i]['codigopres']    =$row->codigopres;
		 $data[$i]['denominacion']  =$row->denominacion;
		 $data[$i]['presupuesto']   =$row->presupuesto;
		 $data[$i]['comprometido']  =$row->comprometido;
		 $data[$i]['causado']       =$row->causado;
		 $data[$i]['pagado']        =$row->pagado;
		 $data[$i]['ejecutado']     =$row->ejecutado;
		 $data[$i]['disponible']    =$row->disponible;
		 
		 $cont[0]+=$row->presupuesto;
		 $cont[1]+=$row->comprometido;
		 $cont[2]+=$row->causado;
		 $cont[3]+=$row->pagado;
		 $cont[4]+=$row->ejecutado;
		 $cont[5]+=$row->disponible;
		 
		$i++;
		}
		 $data[$i]['codigopres']   = 'Totales';
		 $data[$i]['denominacion'] = 'Denominacion';
		 $data[$i]['presupuesto']  = $cont[0];
		 $data[$i]['comprometido'] = $cont[1];
		 $data[$i]['causado']      = $cont[2];
		 $data[$i]['pagado']       = $cont[3];
		 $data[$i]['ejecutado']   =round($cont[1]*100/$cont[0],2);
		 $data[$i]['disponible']   = $cont[5];

		$grid = new DataGrid('Presupuestos',$data);

		$uri = anchor($this->url."datagrid4/$codigoadm/<#codigopres#>",'<#codigopres#>');
		
		$grid->per_page = count($data);
		$grid->column("C&oacute;digo", $uri          ,'align=left');
		$grid->column("Denominacion" , "denominacion",'align=left');
		$grid->column("Presupuesto"  , "<nformat><#presupuesto#></nformat>", 'align=right' );
		$grid->column("Comprometido" , "<nformat><#comprometido#></nformat>",'align=right' );
		$grid->column("Causado"      , "<nformat><#causado#></nformat>",     'align=right' );
		$grid->column("Pagado"       , "<nformat><#pagado#></nformat>",      'align=right' );
		$grid->column("%"            , "<nformat><#ejecutado#></nformat>",   'align=right' );
		$grid->column("Disponible"   , "<nformat><#disponible#></nformat>",  'align=right' );

		$grid->build();
		 
		 
		$grafico = open_flash_chart_object(900,450, site_url("presupuesto/gpresup/grafico3/$codigoadm/$partida"));
		$data['content']  = $grafico;
		$data['content'] .= $volver.$grid->output;
		$data["head"]     = $this->rapyd->get_head();
		$data['title']    = $this->rapyd->get_head()." Presupuestos ";
		$this->load->view('view_ventanas', $data);
	}

	function grafico3($codigoadm, $partida){
		$this->load->library('Graph');
		
		$mSQL = "SELECT codigopres, 
					SUM(asignacion+aumento-disminucion+traslados) as presupuesto, 
		                        SUM(comprometido) comprometido, 
		                        SUM(causado) causado, 
		                        SUM(pagado) pagado
		FROM presupuesto a JOIN ppla b ON a.codigopres=b.codigo
		WHERE b.movimiento='S' AND a.codigoadm='$codigoadm' AND MID(a.codigopres,1,7)='$partida'
		GROUP BY codigopres
		HAVING presupuesto<>0";

		$maxval=0;
		$query=$this->db->query($mSQL);
		
		foreach($query->result() as $row ){
		if ($row->presupuesto > $maxval) $maxval=$row->presupuesto;
		 $codigopres[]=$row->codigopres;
		 $data_1[]=$row->presupuesto;
		 $data_2[]=$row->comprometido;
		 $data_3[]=$row->causado;
		 $data_4[]=$row->pagado;

		}
						
		$om=1;while($maxval/$om>100) $om=$om*10;
		
		$bar_1 = new bar(75, '#0053A4');
		$bar_2 = new bar(75, '#9933CC');
		$bar_3 = new bar(75, '#639F45');
		$bar_4 = new bar(75, '#C34F33');
		
		$bar_1->key('Presupuesto',10);
		$bar_2->key('Comprometido',10);
		$bar_3->key('Causado'  ,10);
		$bar_4->key('Pagado'  ,10);
		
    
		for($i=0;$i<count($data_1);$i++ ){
			$bar_1->add_data_tip($data_1[$i]/$om, graph::esc( number_format($data_1[$i],2,',','.')));  
			$bar_2->add_data_tip($data_2[$i]/$om, graph::esc( number_format($data_2[$i],2,',','.')));
			$bar_3->add_data_tip($data_3[$i]/$om, graph::esc( number_format($data_3[$i],2,',','.')));
			$bar_4->add_data_tip($data_4[$i]/$om, graph::esc( number_format($data_4[$i],2,',','.')));
			
			$bar_1->links[]= site_url("presupuesto/gpresup/datagrid4/".$codigoadm."/".$codigopres[$i]);
			$bar_2->links[]= site_url("presupuesto/gpresup/datagrid4/".$codigoadm."/".$codigopres[$i]);
			$bar_3->links[]= site_url("presupuesto/gpresup/datagrid4/".$codigoadm."/".$codigopres[$i]);
			$bar_4->links[]= site_url("presupuesto/gpresup/datagrid4/".$codigoadm."/".$codigopres[$i]);
		}
		
		$g = new graph();
		$g->set_is_decimal_separator_comma(1);
		if($maxval>0){
		$g->data_sets[] = $bar_1;
		$g->data_sets[] = $bar_2;
		$g->data_sets[] = $bar_3;
		$g->data_sets[] = $bar_4;
				
		$g->set_x_labels($codigopres);
		$g->set_x_label_style( 10, '#000000', 2, 1 );
		$g->set_x_axis_steps( 10 );
		$g->set_x_legend('Presupuestos', 14, '#004381' );        
		
		$g->bg_colour = '#FFFFFF';
		$g->set_tool_tip( '#key#<br>Codigo: #x_label# <br>Monto: #tip#' );
		$g->set_y_max(ceil($maxval/$om));
		$g->y_label_steps(5);
		$g->set_y_legend('Presupuesto x '.number_format($om,0,'','.').' (Bs)', 16, '#004381' );
		}else
		$g->title( 'No existen Presupuestos','{font-size:18px; color: #d01f3c}');
		$g->bg_colour='#FFFFFF';
		 echo utf8_encode($g->render());
	}

	function datagrid4( $codigoadm, $partida){
		$this->rapyd->load("datagrid");
		$this->rapyd->load("dataform");
		$this->load->helper('openflash');
		
		$mSQL = "SELECT a.codigopres,a.ordinal, MID(a.denominacion,1,30) denominacion,
					SUM(asignacion+aumento-disminucion+traslados) as presupuesto, 
		                        SUM(comprometido) comprometido, 
		                        SUM(causado) causado, 
		                        SUM(pagado) pagado, 
					SUM(a.comprometido)*100/SUM(a.asignacion+a.aumento-a.disminucion+a.traslados) ejecutado,
					SUM((a.asignacion+a.aumento-a.disminucion+a.traslados)-(a.comprometido)) disponible
		FROM ordinal a JOIN ppla b ON a.codigopres=b.codigo
		WHERE b.movimiento='S' AND a.codigoadm='$codigoadm'  AND codigopres like '$partida%'
		GROUP BY codigopres,a.ordinal
		HAVING presupuesto<>0";

		$volver = anchor("presupuesto/gpresup/datagrid3/$codigoadm/".substr($partida,0,7),"Regresar");

		$maxval=0;
		$query=$this->db->query($mSQL);
		
		$i=0;
		$cont[0]=$cont[1]=$cont[2]=$cont[3]=$cont[4]=$cont[5]=0;

		foreach($query->result() as $row ){
		if ($row->presupuesto > $maxval) $maxval=$row->presupuesto;
		 $data[$i]['codigopres']    =$row->codigopres;
		 $data[$i]['ordinal']       =$row->ordinal;
		 $data[$i]['denominacion']  =$row->denominacion;
		 $data[$i]['presupuesto']   =$row->presupuesto;
		 $data[$i]['comprometido']  =$row->comprometido;
		 $data[$i]['causado']       =$row->causado;
		 $data[$i]['pagado']        =$row->pagado;
		 $data[$i]['ejecutado']     =$row->ejecutado;
		 $data[$i]['disponible']    =$row->disponible;
		 
		 $cont[0]+=$row->presupuesto;
		 $cont[1]+=$row->comprometido;
		 $cont[2]+=$row->causado;
		 $cont[3]+=$row->pagado;
		 $cont[4]+=$row->ejecutado;
		 $cont[5]+=$row->disponible;
		 
		$i++;
		}
		 $data[$i]['codigopres']   = 'Totales';
		 $data[$i]['ordinal']      = '';
		 $data[$i]['denominacion'] = 'Denominacion';
		 $data[$i]['presupuesto']  = $cont[0];
		 $data[$i]['comprometido'] = $cont[1];
		 $data[$i]['causado']      = $cont[2];
		 $data[$i]['pagado']       = $cont[3];
		 $data[$i]['ejecutado']    =($cont[0]>0)?round($cont[1]*100/$cont[0],2):0;
		 $data[$i]['disponible']   = $cont[5];

		$grid = new DataGrid('Presupuestos',$data);

		$grid->per_page = count($data);
		$grid->column("C&oacute;digo", "codigopres"  ,'align=left');
		$grid->column("Ordinal"      , "ordinal"     ,'align=left');
		$grid->column("Denominacion" , "denominacion",'align=left');
		$grid->column("Presupuesto"  , "<nformat><#presupuesto#></nformat>",  'align=right' );
		$grid->column("Comprometido" , "<nformat><#comprometido#></nformat>", 'align=right' );
		$grid->column("Causado"      , "<nformat><#causado#></nformat>",      'align=right' );
		$grid->column("Pagado"       , "<nformat><#pagado#></nformat>",       'align=right' );
		$grid->column("%"            , "<nformat><#ejecutado#></nformat>",    'align=right' );
		$grid->column("Disponible"   , "<nformat><#disponible#></nformat>",   'align=right' );

		$grid->build();
		 
		 
		$grafico = open_flash_chart_object(900,450, site_url("presupuesto/gpresup/grafico4/$codigoadm/$partida"));
		$data['content']  = $grafico;
		$data['content'] .= $volver.$grid->output;
		$data["head"]     = $this->rapyd->get_head();
		$data['title']    = $this->rapyd->get_head()." Presupuestos ";
		$this->load->view('view_ventanas', $data);
	}

	function grafico4($codigoadm, $partida){
		$this->load->library('Graph');
		
		$mSQL = "SELECT codigopres,a.ordinal, 
					SUM(asignacion+aumento-disminucion+traslados) as presupuesto, 
		                        SUM(comprometido) comprometido, 
		                        SUM(causado) causado, 
		                        SUM(pagado) pagado
		FROM ordinal a JOIN ppla b ON a.codigopres=b.codigo
		WHERE b.movimiento='S' AND a.codigoadm='$codigoadm' AND codigopres LIKE '$partida%'
		GROUP BY codigopres,a.ordinal
		HAVING presupuesto<>0";

		$maxval=0;
		$query=$this->db->query($mSQL);
		
		foreach($query->result() as $row ){
		if ($row->presupuesto > $maxval) $maxval=$row->presupuesto;
		 $codigopres[]=$row->ordinal;
		 $data_1[]=$row->presupuesto;
		 $data_2[]=$row->comprometido;
		 $data_3[]=$row->causado;
		 $data_4[]=$row->pagado;

		}
						
		$om=1;while($maxval/$om>100) $om=$om*10;
		
		$bar_1 = new bar(75, '#0053A4');
		$bar_2 = new bar(75, '#9933CC');
		$bar_3 = new bar(75, '#639F45');
		$bar_4 = new bar(75, '#C34F33');
		
		$bar_1->key('Presupuesto',10);
		$bar_2->key('Comprometido',10);
		$bar_3->key('Causado'  ,10);
		$bar_4->key('Pagado'  ,10);
		
    
		for($i=0;$i<count($data_1);$i++ ){
			$bar_1->add_data_tip($data_1[$i]/$om, graph::esc( number_format($data_1[$i],2,',','.')));  
			$bar_2->add_data_tip($data_2[$i]/$om, graph::esc( number_format($data_2[$i],2,',','.')));
			$bar_3->add_data_tip($data_3[$i]/$om, graph::esc( number_format($data_3[$i],2,',','.')));
			$bar_4->add_data_tip($data_4[$i]/$om, graph::esc( number_format($data_4[$i],2,',','.')));
		}
		
		$g = new graph();
		$g->set_is_decimal_separator_comma(1);
		if($maxval>0){
		$g->data_sets[] = $bar_1;
		$g->data_sets[] = $bar_2;
		$g->data_sets[] = $bar_3;
		$g->data_sets[] = $bar_4;
				
		$g->set_x_labels($codigopres);
		$g->set_x_label_style( 10, '#000000', 2, 1 );
		$g->set_x_axis_steps( 10 );
		$g->set_x_legend('Presupuestos', 14, '#004381' );        
		
		$g->bg_colour = '#FFFFFF';
		$g->set_tool_tip( '#key#<br>Codigo: #x_label# <br>Monto: #tip#' );
		$g->set_y_max(ceil($maxval/$om));
		$g->y_label_steps(5);
		$g->set_y_legend('Presupuesto x '.number_format($om,0,'','.').' (Bs)', 16, '#004381' );
		}else
		$g->title( 'No existen Presupuestos','{font-size:18px; color: #d01f3c}');
		$g->bg_colour='#FFFFFF';
		 echo utf8_encode($g->render());
	}
}

?>