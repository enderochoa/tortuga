<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>ProteoERP <?php if(isset($title)) echo ': '.preg_replace('/<[^>]*>/', '', $title); ?></title>
<?php if (isset($head))   echo $head;   ?>

<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/normal.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/ext/resources/css/ext-all.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/ext/ux/css/CheckHeader.css"/>

<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/ext/ux/grid/css/GridFilters.css"/> 
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/ext/ux/grid/css/RangeMenu.css" /> 

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/ext/ext-debug.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/ext/locale/ext-lang-es.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/ext/bootstrap.js"></script>

<?php if (isset($script)) echo $script; ?>

<style type="text/css">
#divgrid {
	background: #e9e9e9;
	border: 1px solid #d3d3d3;
	margin: 20px;
	padding: 20px;
}

	.icon-user     { background-image: url('<?php echo base_url(); ?>assets/icons/fam/user.png') !important;}
	.icon-user-add { background-image: url('<?php echo base_url(); ?>assets/icons/fam/user_add.gif') !important;}
	.icon-save     { background-image: url('<?php echo base_url(); ?>assets/icons/fam/save.gif') !important;}
	.icon-reset    { background-image: url('<?php echo base_url(); ?>assets/icons/fam/stop.png') !important;}
	.icon-grid     { background-image: url('<?php echo base_url(); ?>assets/icons/fam/grid.png') !important;}
	.icon-add      { background-image: url('<?php echo base_url(); ?>assets/icons/fam/add.png') !important;}
	.icon-delete   { background-image: url('<?php echo base_url(); ?>assets/icons/fam/delete.png') !important;}
	.icon-update   { background-image: url('<?php echo base_url(); ?>assets/icons/fam/user_gray.png') !important;}
</style>
<script type="text/javascript">
var BASE_URL   = '<?=base_url() ?>';
var BASE_PATH  = '<?=base_url() ?>';
var BASE_ICONS = '<?=base_url() ?>assets/icons/';
var BASE_UX    = '<?=base_url() ?>assets/js/ext/ux';

Ext.Loader.setConfig({ enabled: true });
Ext.Loader.setPath('Ext.ux', BASE_UX);

Ext.require([
	'Ext.grid.*',
	'Ext.ux.grid.FiltersFeature',
	'Ext.data.*',
	'Ext.util.*',
	'Ext.state.*',
	'Ext.form.*',
	'Ext.window.MessageBox',
	'Ext.tip.*',
	'Ext.ux.CheckColumn',
	'Ext.toolbar.Paging'
]);

	var urlApp = '<?=base_url() ?>';
	var registro;

	// Define our data model
	var Employee = Ext.regModel('Employee', {
		fields: ['p_id','i_id','v_id','b_descrip','id','r_numero','r_fecha','r_tipo','r_monto','r_observa','c_codigo','c_nombre','c_rifci','c_nacionali','c_localidad','c_direccion','c_telefono','p_tarjeta','p_licencia','p_razon','p_dir_neg','p_dir_pro','p_capital','p_monto','p_fecha_es','p_oficio','p_local','p_negocio','p_registrado','p_deuda','p_observa','p_clase','p_tipo','p_catastro','p_publicidad','i_ctainos','i_direccion','i_no_predio','i_sector','i_tipo_in','i_no_hab','i_clase','i_tipo','i_monto','i_registrado','v_clase','v_marca','v_tipo','v_modelo','v_color','v_capaci','v_serial_m','v_placa_ant','v_placa_act','v_ano','v_peso','v_serial_c','v_monto','v_registrado','v_asovehi'],
		/*validations: [
			{ type: 'length', field: 'numero', min: 1  }, 
			{ type: 'length', field: 'observa', min: 1  }
		],
		*/
		proxy: {
			type: 'ajax',
			noCache: false,
			api: {
				read: urlApp + 'ingresos/recibo/grid',
				create : urlApp + 'ingresos/recibo/guardar',
				update : urlApp + 'ingresos/recibo/guardar',
				destroy: urlApp + 'ingresos/recibo/eliminar',
				method : 'POST'
			},
			reader: {
				type: 'json',
				successProperty: 'success',
				root: 'data',
				messageProperty: 'message',
				totalProperty: 'results'
			},
			writer: {
				type: 'json',
				root: 'data',
				writeAllFields: true,
				callback: function( op, suc ) {
					Ext.Msg.Alert('que paso');
				}
			},
			listeners: {
				exception: function(proxy, response, operation){
						a=operation.getError();
						a=a+response.success;
						Ext.MessageBox.show({
							title: 'REMOTE EXCEPTION',
							msg: a,
							icon: Ext.MessageBox.ERROR,
							buttons: Ext.Msg.OK
						});
					}
			}
		}
	});
	
	//Data Store
	var storeEmp = Ext.create('Ext.data.Store', {
		model: 'Employee',
		autoLoad: true,
		autoSync: true,
		method: 'POST',
		pageSize: 50,
		remoteSort: true,
		listeners: {
			write: function(mr,re, op) {
				Ext.Msg.alert('Aviso','Registro Guardado '+re.success);
				
			},
			exception: function(proxy, response, operation){
						Ext.Msg.alert('Aviso','exeption '+response.success)
			},
			writeexception: function(proxy, response, operation){
						//Ext.Msg.alert('Aviso','writeexception '+response.success)
			},
			load: function(proxy, response, operation){
						//Ext.Msg.alert('Aviso','load '+response.success)
			},
			loadexception: function(proxy, response, operation){
						//Ext.Msg.alert('Aviso','loadexception '+response.success)
			},
			scope: function(proxy, response, operation){
						//Ext.Msg.alert('Aviso','scope '+response.success)
			}
		}
	});
var a='';
function print_r(theObj){
   if(theObj.constructor == Array || theObj.constructor == Object){
      a=a+"<ul>";
      for(var p in theObj){
         if(theObj [p] .constructor == Array || theObj [p] .constructor == Object){
            a=a+"<li> ["+p+"]  => "+typeof(theObj)+"</li>";
            a=a+"<ul>";
            print_r(theObj [p] );
            a=a+"</ul>";
         } else {
            a=a+"<li> ["+p+"]  => "+theObj [p] +"</li>";
         }
      }
      a=a+"</ul>";
   }
}

function dump(arr,level) {
var dumped_text = "";
if(!level) level = 0;

//The padding given at the beginning of the line.
var level_padding = "";
for(var j=0;j<level+1;j++) level_padding += " ";

if(typeof(arr) == 'object') { //Array/Hashes/Objects
for(var i=0;i<arr.length;i++) {
var value = arr[i];

if(typeof(value) == 'object') { //If it is an array,
dumped_text += level_padding + "'" + i + "' ==> ";
dumped_text += "{\n" + dump(value,level+1) + level_padding + "}\n";
} else {
dumped_text += level_padding + "'" + i + "' ==> \"" + value + "\"\n";
}
}
} else { //Stings/Chars/Numbers etc.
dumped_text = "===>"+arr+"<===("+typeof(arr)+")\n";
}
return dumped_text;
}

var win;

// Main 
Ext.onReady(function(){
	Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
	
	
	var proxyc_nombre = new Ext.data.ScriptTagProxy({
		url: 'http://localhost/tortuga/ingresos/recibo/pru'
	});
	
	var storec_nombre = new Ext.data.Store({
		proxy: proxyc_nombre,
		reader: new Ext.data.JsonReader({
			id: 'ID',
			totalProperty: 'totalCount',
			root: 'Names'
			},
			[
				{
					name: 'ID'
				},
				{
					name: 'name'
				}
			]
		)
	});
	
	var storec_rifci = new Ext.data.JsonStore({
    	    	url: 'http://localhost/tortuga/ingresos/recibo/pru3',    	totalProperty:'num',

	type: 'ajax',
			noCache: false,
			
    	fields: [
	    {name:'name', mapping:'name'},
	    {name:'desc', mapping:'desc'},
	    {name:'logo', mapping:'logo'}
	  	],
	  	root:'data',
	  	sortInfo:{field: "name", direction: "ASC"}
	});
	//show Form
	function showContactForm() {
		if (!win) {
			var writeForm=Ext.define('MyApp.view.ui.MyForm', {
					extend: 'Ext.form.Panel',
					alias:  'widget.writerform',
					height: 560,
					width: 775,
					bodyPadding: 10,
					title: 'Recibo',
					initComponent: function() {
						var me = this;
						me.dockedItems= [
							{ xtype: 'toolbar', dock: 'bottom', ui: 'footer', 
							items: ['->', 
								{ iconCls: 'icon-reset', itemId: 'close', text: 'Cerrar', scope: this, handler: this.onClose },
								{ iconCls: 'icon-save',  itemId: 'save',  text: 'Guardar',  disabled: false, scope: this, handler: this.onSave }
							]
						}];
					    
					    me.items = [
						{
						    xtype: 'fieldset',
						    height: 99,
						    width: 740,
						    layout: {
							type: 'absolute'
						    },
						    title: 'DATOS DEL CONTRIBUYENTE',
						    items: [
							{
							    xtype: 'combobox',
							    width: 120,
							    name: 'c_codigo',
							    tabIndex: 1,
							    fieldLabel: 'C&oacute;digo',
							    labelWidth: 45,
							    maxLength: 6,
							    minLength: 6,
							    store:[<?=$c_codigo ?>],
							    listeners: 
									{
										select: function(cmb, record, index){
											proveed=cmb.getValue();
											$.post("<?=site_url('ingresos/contribu/to_extjs') ?>",{ contribu:proveed },function(data){
												alert(data);
											});
										}
									}
							},
							{
							    xtype: 'textfield',
							    width: 70,
							    name: 'c_nacionali',
							    tabIndex: 2,
							    fieldLabel: 'RIF/CI',
							    labelWidth: 40,
							    maxLength: 1,
							    minLength: 1,
							    x: 120,
							    y: 0
							},
							{
								store: storec_nombre,
								fieldLabel: 'ComboBox',
								displayField: 'name',
								valueField: 'name', 
								typeAhead: true,
								forceSelection: true,
								mode: 'remote',
								triggerAction: 'all',
								selectOnFocus: true,
								editable: true,
								xtype: 'combo',
								size: 15,
								x: 320,
								y: -1,
								listeners: {
						    
								    // 'change' will be fired when the value has changed and the user exits the ComboBox via tab, click, etc.
								    // The 'newValue' and 'oldValue' params will be from the field specified in the 'valueField' config above.
								    change: function(combo, newValue, oldValue){
									console.log("Old Value: " + oldValue);
									console.log("New Value: " + newValue);
								    },
						    
								    // 'select' will be fired as soon as an item in the ComboBox is selected with mouse, keyboard.
								    select: function(combo, record, index){
									console.log(record.data.name);
									console.log(index);
								    }
								}
							    },
							{
							    xtype: 'textareafield',
							    height: 40,
							    width: 250,
							    name: 'c_telefono',
							    tabIndex: 7,
							    fieldLabel: 'Telefonos',
							    labelWidth: 60,
							    
							    x: 460,
							    y: 30
							},
							{
								
							    xtype: 'combobox',
							    width: 100,
							    name: 'c_rifci',
							    tabIndex: 3,
							    fieldLabel: 'Nacionali',
							    hideLabel: true,
							    labelWidth: 20,
							    maxLength: 8,
							    minLength: 8,
							    x: 200,
							    y: 0,
								store: storec_rifci,
								tpl: '{name}',
								
								minChars:1,
								
								displayField:'name',
								forceSelection:true,
							typeAhead:true,
							valueNotFoundText:'Select a Country!!...',
							mode: 'remote',
							triggerAction: 'all',
							emptyText:'Select a Country...',
							
								
							},
							{
							    xtype: 'textareafield',
							    height: 40,
							    width: 440,
							    name: 'c_direccion',
							    tabIndex: 6,
							    fieldLabel: 'Direcci&oacute;n',
							    labelWidth: 60,
							    x: 0,
							    y: 30
							}
						    ]
						},
						{
						    xtype: 'fieldset',
						    height: 120,
						    width: 739,
						    layout: {
							type: 'absolute'
						    },
						    title: 'DATOS DEL RECIBO',
						    items: [
							{
								xtype: 'datefield',
								width: 140,
								name: 'r_fecha',
								value: new Date(),
								fieldLabel: 'Fecha',
								labelWidth: 35,
								x: 570,
								y: 0,
								submitFormat: 'Y-m-d'
							    },
							    {
								xtype: 'combobox',
								width: 270,
								name: 'r_tipo',
								fieldLabel: 'Concepto',
								labelWidth: 60,
								x: 140,
								y: 0,
								store:[<?=$conceptos ?>]
								
							    },
							    {
								xtype: 'textfield',
								width: 140,
								name: 'r_monto',
								fieldLabel: 'Monto',
								labelWidth: 35,
								x: 420,
								y: 0
							    },
							    {
								xtype: 'textareafield',
								height: 40,
								width: 710,
								name: 'r_observa',
								fieldLabel: 'Observaci&oacute;n',
								labelWidth: 70,
								x: 0,
								y: 30
							    },
							    {
								xtype: 'textfield',
								width: 130,
								fieldLabel: 'Numero',
								labelWidth: 45,
								name:'r_numero'
							    }
						    ]
						},
						{
						    xtype: 'tabpanel',
						    height: 269,
						    width: 739,
						    activeTab: 2,
						    items: [
							{
							    xtype: 'panel',
							    width: 731,
							    layout: {
								type: 'absolute'
							    },
							    title: 'Patente',
							    items: [
								{
								    xtype: 'textfield',
								    itemId: '',
								    width: 140,
								    name: 'p_tarjeta',
								    fieldLabel: 'Tarjeta',
								    labelWidth: 50,
								    x: 420,
								    y: 10
								},
								{
								    xtype: 'textfield',
								    width: 140,
								    name: 'p_licencia',
								    fieldLabel: 'Licencia',
								    labelWidth: 50,
								    x: 580,
								    y: 10
								},
								{
								    xtype: 'textfield',
								    width: 710,
								    name: 'p_razon',
								    fieldLabel: 'Raz&oacute;n Social',
								    labelWidth: 80,
								    x: 10,
								    y: 40
								},
								{
								    xtype: 'textareafield',
								    height: 30,
								    width: 710,
								    name: 'p_dir_neg',
								    fieldLabel: 'Direcci&oacute;n Negocio',
								    labelWidth: 80,
								    x: 10,
								    y: 70
								},
								{
								    xtype: 'textfield',
								    width: 230,
								    fieldLabel: 'Capital',
								    labelWidth: 80,
								    x: 10,
								    y: 110,
								    name:'p_capital'
								},
								{
								    xtype: 'datefield',
								    width: 230,
								    name: 'p_fecha_es',
								    fieldLabel: 'Fecha_es',
								    labelWidth: 80,
								    x: 10,
								    y: 140
								},
								{
								    xtype: 'textfield',
								    width: 230,
								    name: 'p_oficio',
								    fieldLabel: 'Oficio',
								    labelWidth: 80,
								    x: 10,
								    y: 170
								},
								{
								    xtype: 'textfield',
								    width: 230,
								    name: 'p_catastro',
								    fieldLabel: 'Catastro',
								    labelWidth: 80,
								    x: 10,
								    y: 200
								},
								{
								    xtype: 'combobox',
								    width: 210,
								    name: 'p_local',
								    fieldLabel: 'Local',
								    labelWidth: 50,
								    x: 250,
								    y: 110,
								    store: [<?=$local ?>]
								},
								{
								    xtype: 'combobox',
								    width: 210,
								    name: 'p_negocio',
								    fieldLabel: 'Negocio',
								    labelWidth: 50,
								    x: 250,
								    y: 140,
								    store: [<?=$negocio ?>]
								},
								{
								    xtype: 'combobox',
								    width: 210,
								    name: 'p_clase',
								    fieldLabel: 'Clase',
								    labelWidth: 50,
								    x: 250,
								    y: 170,
								    store: [<?=$claseo ?>]
								},
								{
								    xtype: 'combobox',
								    width: 210,
								    name: 'p_tipo',
								    fieldLabel: 'Tipo',
								    labelWidth: 50,
								    x: 250,
								    y: 200,
								    store: [<?=$tipo ?>]
								},
								
								{
								    xtype: 'textfield',
								    width: 250,
								    name: 'p_publicidad',
								    fieldLabel: 'Publicidad',
								    labelWidth: 70,
								    x: 470,
								    y: 140
								},
								{
								    xtype: 'textfield',
								    width: 250,
								    name: 'p_observa',
								    fieldLabel: 'Observaci&oacute;n',
								    labelWidth: 70,
								    x: 470,
								    y: 170
								},
								{
								    xtype: 'textfield',
								    width: 250,
								    name: 'p_registrado',
								    fieldLabel: 'Registrado',
								    labelWidth: 70,
								    x: 470,
								    y: 200
								},
								{
								    xtype: 'textfield',
								    name: 'p_id',
								    fieldLabel: 'Referencia',
								    labelWidth: 80,
								    x: 10,
								    y: 10
								}
							    ]
							},
							{
							    xtype: 'panel',
							    title: 'Inmueble',
							    items: [
								{
								    xtype: 'container',
								    height: 245,
								    width: 737,
								    layout: {
									type: 'absolute'
								    },
								    items: [
									{
									    xtype: 'textfield',
									    width: 240,
									    name: 'i_ctainos',
									    fieldLabel: 'Cuenta INOS',
									    labelWidth: 90,
									    x: 250,
									    y: 10
									},
									{
									    xtype: 'textfield',
									    name: 'i_monto',
									    fieldLabel: 'Monto',
									    labelWidth: 60,
									    x: 210,
									    y: 190
									},
									{
									    xtype: 'textfield',
									    width: 240,
									    name: 'i_no_predio',
									    fieldLabel: 'Nro. Promedio',
									    labelWidth: 90,
									    x: 450,
									    y: 190
									},
									{
									    xtype: 'combobox',
									    width: 220,
									    name: 'i_sector',
									    fieldLabel: 'Sector',
									    labelWidth: 60,
									    x: 10,
									    y: 50,
									    store: [<?=$local ?>]
									},
									{
									    xtype: 'textareafield',
									    height: 40,
									    width: 710,
									    name: 'i_direccion',
									    fieldLabel: 'Direcci&oacute;n',
									    labelWidth: 60,
									    x: 10,
									    y: 80
									},
									{
									    xtype: 'textfield',
									    width: 220,
									    name: 'i_no_hab',
									    fieldLabel: 'Nro. Habitaci&oacute;n',
									    labelWidth: 90,
									    x: -19,
									    y: 130
									},
									{
									    xtype: 'combobox',
									    width: 340,
									    name: 'i_tipo_in',
									    fieldLabel: 'Tipo',
									    labelWidth: 60,
									    x: 10,
									    y: 160,
									    store: [<?=$tipoin ?>]
									},
									{
									    xtype: 'combobox',
									    width: 340,
									    name: 'i_clase',
									    fieldLabel: 'Clase',
									    labelWidth: 60,
									    x: 380,
									    y: 160,
									    store: [<?=$claseo ?>]
									},
									{
									    xtype: 'combobox',
									    width: 180,
									    name: 'i_tipo',
									    fieldLabel: 'TIpo',
									    labelWidth: 60,
									    x: 10,
									    y: 190,
									    store: [<?=$tipo ?>]
									},
									{
									    xtype: 'textfield',
									    width: 160,
									    name: 'i_id',
									    fieldLabel: 'Referencia',
									    labelWidth: 60,
									    x: 10,
									    y: 10
									}
								    ]
								}
							    ]
							},
							{
							    xtype: 'panel',
							    title: 'Vehiculo',
							    items: [
								{
								    xtype: 'container',
								    height: 242,
								    width: 738,
								    layout: {
									type: 'absolute'
								    },
								    items: [
									{
									    xtype: 'combobox',
									    width: 300,
									    name: 'v_clase',
									    fieldLabel: 'Clase',
									    labelWidth: 60,
									    x: 20,
									    y: 40,
									    store: [<?=$clase ?>]
									},
									{
									    xtype: 'combobox',
									    width: 300,
									    name: 'v_marca',
									    fieldLabel: 'Marca',
									    labelWidth: 60,
									    x: 20,
									    y: 70,
									    store: [<?=$marca ?>]
									},
									{
									    xtype: 'textfield',
									    width: 220,
									    name: 'v_id',
									    fieldLabel: 'Referencia',
									    labelWidth: 60,
									    x: 20,
									    y: 10
									},
									{
									    xtype: 'textfield',
									    width: 300,
									    name: 'v_modelo',
									    fieldLabel: 'Modelo',
									    labelWidth: 60,
									    x: 20,
									    y: 130
									},
									{
									    xtype: 'textfield',
									    width: 130,
									    name: 'v_capaci',
									    fieldLabel: 'Capacidad',
									    labelWidth: 60,
									    x: 20,
									    y: 160
									},
									{
									    xtype: 'textfield',
									    width: 150,
									    name: 'v_ano',
									    fieldLabel: 'A&ntilde;o',
									    labelWidth: 60,
									    x: 20,
									    y: 190
									},
									{
									    xtype: 'textfield',
									    width: 150,
									    name: 'v_ano',
									    fieldLabel: 'Peso',
									    labelWidth: 60,
									    x: 170,
									    y: 160
									},
									{
									    xtype: 'textfield',
									    width: 300,
									    name: 'v_tipo',
									    fieldLabel: 'Tipo',
									    labelWidth: 60,
									    x: 20,
									    y: 100,
									    store: [<?=$v_tipo ?>]
									},
									{
									    xtype: 'textfield',
									    name: 'v_placa_act',
									    fieldLabel: 'Placa',
									    labelWidth: 60,
									    x: 360,
									    y: 40
									},
									{
									    xtype: 'textfield',
									    name: 'v_placa_ant',
									    fieldLabel: 'Placa Anterior',
									    x: 360,
									    y: 160
									},
									{
									     xtype: 'textfield',
									     name: 'v_color',
									     fieldLabel: 'Color',
									     labelWidth: 60,
									     x: 360,
									     y: 70
									},
									{
									     xtype: 'textfield',
									     width: 360,
									     name: 'v_serial_m',
									     fieldLabel: 'Serial Motor',
									     x: 360,
									     y: 100
									},
									{
									    xtype: 'textfield',
									    width: 360,
									    name: 'v_serial_c',
									    fieldLabel: 'Serial Carrocer&iacute;a',
									    x: 360,
									    y: 130
									}
								    ]
								}
							    ]
							}
						    ]
						}
					    ];
					    me.callParent(arguments);
					},
					setActiveRecord: function(record){
						this.activeRecord = record;
					},
					onSave: function(){
						var form = this.getForm();
						if (!registro) {
							if (form.isValid()) {
								storeEmp.insert(0, form.getValues());
							} else {
								Ext.Msg.alert('Forma Invalida','Algunos campos no pudieron ser validados<br>los mismos se indican con un cuadro rojo<br> corrijalos y vuelva a intentar');
								return;
							}
						} else {
							var active = win.activeRecord;
							if (!active) {
								Ext.Msg.Alert('Registro Inactivo ');
								return;
							}
							if (form.isValid()) {
								form.updateRecord(active);
							} else {
								Ext.Msg.alert('Forma Invalida','Algunos campos no pudieron ser validados<br>los mismos se indican con un cuadro rojo<br> corrijalos y vuelva a intentar');
								return;
							}
						}
						//this.onReset();
					},
					onReset: function(){
						this.setActiveRecord(null);
						storeEmp.load();
						//Hide Windows 
						win.hide();
					},
					onClose: function(){
						var form = this.getForm();
						form.reset();
						this.onReset();
					}
				    });
			
			
			win = Ext.widget('window', {
				title: '',
				losable: false,
				closeAction: 'destroy',
				width: 780,
				height: 600,
				//minHeight: 300,
				resizable: false,
				modal: true,
				items: [writeForm],
				listeners: {
					beforeshow: function() {
						var form = this.down('writerform').getForm();
						this.activeRecord = registro;
						
						if (registro) {
							form.loadRecord(registro);
							//form.findField('codigo').readOnly = true;
						} else {
							//form.findField('codigo').readOnly = false;
						}
					}
				}
			});
		}
		win.show();
	}
	
	
	


	//Filters
	var filters = {
		ftype: 'filters',
		// encode and local configuration options defined previously for easier reuse
		encode: 'json', // json encode the filter query
		local: false
	};

// Create Grid 
		Ext.define('Employee.Grid', {
			extend: 'Ext.grid.Panel',
			alias: 'widget.writergrid',
			requires: [
					'Ext.grid.*',
					'Ext.data.*',
					'Ext.util.*',
					'Ext.state.*',
					'Ext.form.*',
					'Ext.window.MessageBox',
					'Ext.tip.*',
					'Ext.ux.CheckColumn'
				],
			store: storeEmp,
			initComponent: function(){
				Ext.apply(this, {
					iconCls: 'icon-grid',
					frame: true,
					dockedItems: [{
						xtype: 'toolbar',
						items: [
							{ iconCls: 'icon-add',    text: 'Add', scope: this, handler: this.onAddClick  },
							{ iconCls: 'icon-update', text: 'Update', disabled: true, itemId: 'update', scope: this, handler: this.onUpdateClick }, 
							{ iconCls: 'icon-delete', text: 'Delete', disabled: true, itemId: 'delete', scope: this, handler: this.onDeleteClick }
						]
					}],
					columns: [
						{
							header: 'N&uacute;mero',
							
							width: 80,
							sortable: true,
							dataIndex: 'r_numero',
							field: { type: 'textfield' },
							filter: { type: 'string' }
						},
						{
							header: 'Fecha',
							width:  70,
							sortable: true,
							dataIndex: 'r_fecha',
							field:  { type: 'date'      },
							filter: { type: 'date'    },
							renderer: Ext.util.Format.dateRenderer('d/m/Y')
						},
						{
							header: 'Monto',
							width: 100,
							sortable: true,
							dataIndex: 'r_monto',
							field:  { type: 'numeroc'   },
							filter: { type: 'numeric' },
							align: 'right',
							renderer : Ext.util.Format.numberRenderer('0,000.00')
						}, 
						{
							header: 'RIF/CI',
							width: 100,
							sortable: true,
							dataIndex: 'c_rifci',
							field: { type: 'textfield'  },
							filter: { type: 'string' }
						},
						{
							header: 'Nombre',
							width: 150,
							sortable: true,
							dataIndex: 'c_nombre',
							field: { type: 'textfield'  },
							filter: { type: 'string' }
						},
						{
							header: 'Concepto',
							width: 150,
							sortable: true,
							dataIndex: 'b_descrip',
							field: { type: 'textfield'  },
							filter: { type: 'string' }
						},
						{
							header: 'Observaci&oacute;n',
							width: 150,
							sortable: true,
							dataIndex: 'r_observa',
							field: { type: 'textfield'  },
							filter: { type: 'string' }
						}
						
						],
					// paging bar on the bottom
					bbar: Ext.create('Ext.PagingToolbar', {
						store: storeEmp,
						displayInfo: true,
						displayMsg: 'Page No. {0} - Records {1} of {2}',
						emptyMsg: "No Records Found."
					})
				});
		
				this.callParent();
				this.getSelectionModel().on('selectionchange', this.onSelectChange, this);
			},
			features: [filters],
			onSelectChange: function(selModel, selections){
				this.down('#delete').setDisabled(selections.length === 0);
				this.down('#update').setDisabled(selections.length === 0);
			},
			onUpdateClick: function(){
			var selection = this.getView().getSelectionModel().getSelection()[0];
				if (selection) {
					registro = selection;
					showContactForm();
				}
			},
			onDeleteClick: function(){
				var selection = this.getView().getSelectionModel().getSelection()[0];
				Ext.MessageBox.show({ 
					title: 'Confirm', 
					msg: 'Are you sure?', 
					buttons: Ext.MessageBox.YESNO, 
					fn: function(btn){ 
						if (btn == 'yes'){ 
							if (selection) {
								storeEmp.remove(selection);
							}
							storeEmp.load();
						} 
					}, 
					icon: Ext.MessageBox.QUESTION 
				});
			},
			onAddClick: function(){
				registro = null;
				showContactForm();
				storeEmp.load();
			}
		});

	//Main Container
	var main = Ext.create('Ext.container.Container', {
		padding: '0 0 0 20',
		width: '100%',
		height: 650,
		renderTo: 'divgrid',
		layout: { type: 'vbox', align: 'stretch' },
		items: [{ itemId: 'grid', xtype: 'writergrid', title: 'Employees', flex: 1, store: storeEmp  }]
	});
	
});

</script>
</head>
<body>
<div id="divgrid"></div>
</body>

</html>
