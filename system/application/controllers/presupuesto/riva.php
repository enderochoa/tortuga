<?php

class Riva extends Controller {
	
	var $titp  = 'Retenciones de IVA';      
	var $tits  = 'Retencion de IVA';
	var $url   = 'presupuesto/riva/';    
	
	function Riva(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id('20B',1);
	}
	
	function index() {
		$this->rapyd->load("datagrid","datafilter2");

		$mSPRV=array(
				'tabla'   =>'sprv',
				'columnas'=>array(
				'proveed' =>'C&oacute;odigo',
				'nombre'=>'Nombre',
				'contacto'=>'Contacto'),
				'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre'),
				'retornar'=>array('proveed'=>'codprov' ),
				'titulo'  =>'Buscar Beneficiario');
			
		$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");
		
		$filter = new DataFilter2($this->titp,"riva");
		
		//$filter->db->select(array("a.numero AS numero","a.opago AS opago","a.mbanc AS mbanc","a.ocompra AS ocompra","a.codprov AS codprov","a.fecha AS fecha","a.factura AS factura","a.controlfac AS controlfac","a.fechafac AS fechafac","a.status AS status","b.nombre proveed"));
		//$filter->db->from("riva AS a");
		//$filter->db->join("sprv AS b"    ,"b.proveed=a.codprov", "LEFT");
		//$filter->db->_escape_char='';
		//$filter->db->_protect_identifiers=false;
		
		$filter->nrocomp   = new inputField("N&uacute;mero","nrocomp");
		$filter->nrocomp->size    = 15;
		//$filter->onrcomp->db_name = "a.numero";
		$filter->nrocomp->clause="likerigth";
		
		$filter->ocompra   = new inputField("Orden Compra","ocompra");
		$filter->ocompra->size   = 15;
		//$filter->ocompra->db_name = "a.ocompra";
		$filter->ocompra->clause="likerigth";
		
		$filter->opago   = new inputField("Orden Pago","opago");
		$filter->opago->size   = 15;
		//$filter->opago->db_name = "a.opago";
		$filter->opago->clause="likerigth";
		
		$filter->clipro = new inputField("Beneficiario", 'clipro');
		//$filter->clipro->db_name = "a.codprov";
		$filter->clipro->size = 6;
		$filter->clipro->append($bSPRV);
		
		$filter->emision = new dateonlyField("F. Emision de Retenci&oacute;n", "emision");
		//$filter->emision->db_name = "a.fecha";
		$filter->emision->size=12;
		
		$filter->periodo = new dateonlyField("Periodo", "periodo");
		$filter->periodo->dbformat = "Ym";
		$filter->periodo->size=12;
		
		$filter->ffactura = new dateonlyField("F. Factura", "ffactura");
		//$filter->ffactura->db_name = "a.fecha";
		$filter->ffactura->size=12;
		
		$filter->numero   = new inputField("Factura","numero");
		//$filter->numero->db_name = "a.factura";
		$filter->numero->size=15;
		$filter->numero->clause="likerigth";
		
		$filter->nfiscal   = new inputField("N&uacute;mero Control","nfiscal");
		//$filter->nfiscal>db_name = "a.controlfac";
		$filter->nfiscal->size=15;
		$filter->nfiscal->clause="likerigth";
		
		$filter->status = new dropdownField("Estado","status");
		//$filter->status->db_name = "a.status";
		$filter->status->option("","");
		$filter->status->option("A","Sin Desembolsar");
		$filter->status->option("B","Ordenado Pago");
		$filter->status->option("C","Desembolsado");
		$filter->status->style="width:150px";
		
		$filter->buttons("reset","search");
		$filter->build();
		
		function sta($status){
			switch($status){
				case "A":return "Sin Desembolsar";break;
				case "B":return "Ordenado Pago";break;
				case "C":return "Desembolsado";break;
				//case "T":return "Causado";break;
				//case "O":return "Ordenado Pago";break;
				//case "O":return "Ordenado Pago";break;
				//case "A":return "Anulado";break;
			}
		}
		
		$uri = anchor($this->url.'/dataedit/show/<#nrocomp#>','<#nrocomp#>');	
		
		$grid = new DataGrid();
		$grid->order_by("nrocomp","asc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad','sta');
		
		$grid->column("N&uacute;mero"      ,$uri);
		$grid->column("O. Compra"          ,"ocompra"                                          ,"align='center'");
		$grid->column("O. Pago"            ,"odirect"                                          ,"align='center'");		
		$grid->column("Beneficiario"          ,"clipro"                                           ,"align='left'"  );
		$grid->column("F. emision"         ,"<dbdate_to_human><#emision#></dbdate_to_human>"   ,"align='center'");
		$grid->column("Periodo"            ,"<dbdate_to_human><#periodo#></dbdate_to_human>"   ,"align='center'");
		$grid->column("Fecha Fac"          ,"<dbdate_to_human><#ffactura#></dbdate_to_human>"  ,"align='center'");
		$grid->column("Fecha"              ,"numero"                                           ,"align='center'");
		$grid->column("Control Fiscal"     ,"nfiscal"                                          ,"align='center'");
		$grid->column("Estado"             ,"<sta><#status#></sta>"                            ,"align='center'");
		
		$grid->add($this->url."/dataedit/create");
		$grid->build();
		
		//echo $grid->db->last_query();
		$data['content'] =$filter->output.$grid->output;
		$data["head"]    = $this->rapyd->get_head();
		$data['title']   =" ".$this->tits." ";
		$this->load->view('view_ventanas', $data);
	}
	
	function dataedit(){
 		$this->rapyd->load("dataedit");
 		
 		$script='
		$(function() {
			$(".inputnum").numeric(".");
			});
		';
 		
		$edit = new DataEdit($this->tits,"riva");
		
		$edit->back_url = $this->url."index/filteredgrid";
		
		$edit->script($script,"create");
		$edit->script($script,"modify");
		
		$edit->nrocomp   = new inputField("N&uacute;mero","nrocomp");
		$edit->nrocomp->size    = 15;
		$edit->nrocomp->mode    = "autohide";
		$edit->nrocomp->rule    = "callback_chexiste"; 
		$edit->nrocomp->css_class='inputnum';
		//$edit->nrocomp->when =array('show');

		$edit->ocompra   = new inputField("Orden Compra","ocompra");
		$edit->ocompra->size   = 15;
		$edit->ocompra->css_class='inputnum';
		//$edit->ocompra->when =array('show');
		
		$edit->odirect   = new inputField("Orden Pago","odirect");
		$edit->odirect->size   = 15;
		$edit->odirect->css_class='inputnum';
		//$edit->odirect->when =array('show');
		
		$edit->clipro   = new inputField("Beneficiario","clipro");
		$edit->clipro->size   = 15;
		//$edit->clipro->when =array('show');
		$edit->clipro->readonly = true;
		//exento,tasa,general,geneimpu,tasaadic,adicional,adicimpu,tasaredu,reducida,reduimpu,stotal,impuesto,gtotal,reiva,transac,estampa,hora,usuario,ffactura,status,
		
		$edit->emision = new dateonlyField("F. Emision", "emision");
		$edit->emision->size=12;
		//$edit->emision->when =array('show');
		
		$edit->periodo = new dateonlyField("Periodo", "periodo","m/Y");
		$edit->periodo->dbformat = "Ym";
		$edit->periodo->size=12;
		//$edit->periodo->when =array('show');
		
		$edit->ffactura = new dateonlyField("Fecha Factura", "ffactura");
		$edit->ffactura->size=12;
		//$edit->ffactura->when =array('show');
		
		$edit->numero   = new inputField("Factura","numero");
		$edit->numero->size=15;
		//$edit->numero->when =array('show');
		
		$edit->nfiscal   = new inputField("N&uacute;mero Control","nfiscal");
		$edit->nfiscal->size=15;
		//$edit->nfiscal->when =array('show');
		
		
		
		$edit->tasa   = new inputField("Tasa","tasa");
		$edit->tasa->size   = 15;
		
		$edit->general   = new inputField("General","general");
		$edit->general->size   = 15;
		$edit->general->in     = "tasa";
		
		$edit->geneimpu   = new inputField("Geneimpu","geneimpu");
		$edit->geneimpu->size   = 15;
		$edit->geneimpu->in     = "tasa";
		
		$edit->tasaredu   = new inputField("Tasa Reducida","tasaredu");
		$edit->tasaredu->size   = 15;
		
		$edit->reducida   = new inputField("Reducida","reducida");
		$edit->reducida->size   = 15;
		$edit->reducida->in     = "tasaredu";
		
		$edit->reduimpu   = new inputField("Reduimpu","reduimpu");
		$edit->reduimpu->size   = 15;
		$edit->reduimpu->in     = "tasaredu";
		
		$edit->tasaadic   = new inputField("Tasa Adicional","tasaadic");
		$edit->tasaadic->size   = 15;
		
		$edit->adicional   = new inputField("Adicional","adicional");
		$edit->adicional->size   = 15;
		$edit->adicional->in     = "tasaadic";
		
		$edit->adicimpu   = new inputField("Adicimpu","adicimpu");
		$edit->adicimpu->size   = 15;
		$edit->adicimpu->in     = "tasaadic";
		
		$edit->gtotal   = new inputField("Total Factura","gtotal");
		$edit->gtotal->size   = 15;
		
		$edit->impuesto   = new inputField("IVA","impuesto");
		$edit->impuesto->size   = 15;
		//$edit->impuesto->when =array('show');
		
		$edit->reiva   = new inputField("Retenci&oacute;n","reiva");
		$edit->reiva->size   = 15;
		//$edit->reiva->when =array('show');
//tipo_doc,fecha,numero,nfiscal,afecta,clipro,nombre,rif,exento,tasa,general,geneimpu,tasaadic,adicional,adicimpu,tasaredu,reducida,reduimpu,stotal,impuesto,gtotal,reiva,transac,estampa,hora,usuario,reteiva_prov,ffactura,status,mbanc,banc,numcuent,codbanc,pagado,
		$edit->exento   = new inputField("Exento","exento");
		$edit->exento->size   = 15;
		
		$edit->buttons( "undo", "back","save","modify");
		$edit->build();
		
		$smenu['link']   = barra_menu('20B');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$data['content'] = $edit->output;
		$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$data['title']   = " ".$this->tits." ";
		$this->load->view('view_ventanas', $data);
	}	
	
	function chexiste(){
		$nrocomp=$this->input->post('nrocomp');
		
		$chek=$this->datasis->dameval("SELECT COUNT(*) FROM riva WHERE nrocomp='$nrocomp'");
		if ($chek > 0){
			$this->validation->set_message('chexiste',"La Retencion $nrocomp ya existe ");
			return FALSE;
		}else {
			$chek=$this->datasis->dameval("SELECT nrocomp FROM riva ORDER BY nrocomp DESC");
			if ($nrocomp > $chek ){
				$this->validation->set_message('chexiste',"No debe agregar un comprobante que no halla sido realizado manualmente");
				return FALSE;
			}else {
  			return TRUE;
  		}
		}
	}
	
}
?>

