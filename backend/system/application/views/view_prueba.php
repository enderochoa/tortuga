<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=ISO-8859-1" />
<title>Sistemas Tortuga</title>
<link rel="stylesheet" href="/proteoerp/assets/default/css/ventanas.css" type="text/css" media="all" />
<link rel="stylesheet" href="/proteoerp/system/application/rapyd/libraries/jscalendar/calendar.css" type="text/css" />
<link rel="stylesheet" href="/proteoerp/system/application/rapyd/elements/proteo/css/rapyd_components.css" type="text/css" />
<script language="javascript" type="text/javascript" src="/proteoerp/system/application/rapyd/libraries/jscalendar/calendar.js"></script>
<script language="javascript" type="text/javascript" src="/proteoerp/system/application/rapyd/libraries/jscalendar/calendar-setup.js"></script>
<script language="javascript" type="text/javascript">
          Calendar._DN = new Array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
          Calendar._SMN = new Array("Ene", "Feb", "Mar", "Abr", "Mayo", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");
          Calendar._SDN = new Array("Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa", "Do");
          Calendar._MN = new Array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
          Calendar._TT = {};
          Calendar._TT["TODAY"] = "Hoy";

					
</script>
<style>
</style>

</head>
<body>
<div id='encabe'></div>
<div id='contenido'>
	<h1>Compras</h1>	
	<table width="95%" border=0 align="center">
		<tr>
			<td valign=top></td>
			<td><form action="/proteoerp/compras/add/dataedit/create/process" method="post" id="df1"><table style="margin:0;width:100%;border-collapse:collapse;padding:0;">
  <tr>
    <td>

      <div class="mainbackground" style="padding:2px;clear:both">
      <div class="alert"></div>
      <table style="margin:0;width:98%;">      
    
              <tr id="tr_proveedor">
  
                      
                                                <td style="width:120px;" class="littletableheader">Beneficiario*</td>
              <td style="padding:1px;" class="littletablerow" id="td_proveed">
              
<input type="text" name="proveed" value="" id="proveed" maxlength="5" size="10" onClick="" onChange="" class="input" style="" readonly="readonly"  />
<span class="micro"><a href='javascript:void(0);' onClick="vent=window.open('/proteoerp/buscar/index/7967','ventbuscarsprv','width=800,height=600,scrollbars=Yes,status=Yes,resizable=Yes,screenx=5,screeny=5'); vent.focus(); document.body.setAttribute('onUnload','vent=window.open(\'about:blank\',\'ventbuscarsprv\');vent.close();');"><img src="/proteoerp/assets/default/images/system-search.png"  alt="Buscar Beneficiario" title="Buscar Beneficiario" border="0" /></a></span>
&nbsp;
                              
                      

<input type="text" name="nombre" value="" id="nombre" maxlength="40" size="50" onClick="" onChange="" class="input" style="" readonly="readonly"  />

&nbsp;
                                      </td>
            </tr>
      
              <tr id="tr_fecha">
  
                      
                                                <td style="width:120px;" class="littletableheader">Fecha</td>
              <td style="padding:1px;" class="littletablerow" id="td_fecha">
              <input type="text" name="fecha" value="26/10/2009" id="fecha" size="11" onClick="" onChange="" class="input" style=""  />
 <img src="/proteoerp/system/application/rapyd/libraries/jscalendar/calender_icon.gif" id="fecha_button" border="0" style="vertical-align:middle;" /><script language="javascript" type="text/javascript">
         Calendar.setup({
        inputField  : "fecha",
        ifFormat    : "%d/%m/%Y",
        button      : "fecha_button",
        align       : "Bl",
        singleClick : false,
        mondayFirst : true,
        weekNumbers : false
       });</script>

&nbsp;
                                                  </td>
            </tr>
      
              <tr id="tr_numero">
  
                      
                                                <td style="width:120px;" class="littletableheader">N&uacute;mero*</td>
              <td style="padding:1px;" class="littletablerow" id="td_numero">
              
<input type="text" name="numero" value="" id="numero" maxlength="8" size="15" onClick="" onChange="" class="input" style=""  />

&nbsp;

                                                  </td>
            </tr>
      
              <tr id="tr_cfis">
  
                      
                                                <td style="width:120px;" class="littletableheader">Control fiscal*</td>
              <td style="padding:1px;" class="littletablerow" id="td_nfiscal">
              
<input type="text" name="nfiscal" value="" id="nfiscal" maxlength="12" size="15" onClick="" onChange="" class="input" style=""  />

&nbsp;
                                                  </td>

            </tr>
      
              <tr id="tr_almacen">
  
                      
                                                <td style="width:120px;" class="littletableheader">Almacen*</td>
              <td style="padding:1px;" class="littletablerow" id="td_depo">
              <select name="depo" id="depo" style="width:150px;" class="select">
<option value="" selected="selected">Seleccionar</option>
<option value="0001">PRINCIPAL1</option>
<option value="002 ">SECUNDARIO                    </option>

</select>&nbsp;
                                                  </td>
            </tr>
      
              <tr id="tr_tipo">
  
                      
                                                <td style="width:120px;" class="littletableheader">Tipo*</td>
              <td style="padding:1px;" class="littletablerow" id="td_tipo_doc">
              <select name="tipo_doc" id="tipo_doc" style="width:150px;" class="select">
<option value="FC">Factura Credito</option>
<option value="NC">Nota Credito</option>

<option value="NE">Nota Entrega</option>
</select>&nbsp;
                                                  </td>
            </tr>
      
              <tr id="tr_codigo">
  
                      
                                                <td style="width:120px;" class="littletableheader">Codigo*</td>
              <td style="padding:1px;" class="littletablerow" id="td_codigo">
              
<input type="text" name="codigo" value="" id="codigo" maxlength="15" size="16" onClick="" onChange="" class="input" style="" readonly="readonly"  />

&nbsp;
                              
                      
<input type="text" name="descrip" value="" id="descrip" maxlength="40" size="41" onClick="" onChange="" class="input" style="" readonly="readonly"  />

&nbsp;
                                      </td>
            </tr>
      
              <tr id="tr_costo">
  
                      
                                                <td style="width:120px;" class="littletableheader">Costo*</td>
              <td style="padding:1px;" class="littletablerow" id="td_costo">
              

<input type="text" name="costo" value="0.0" id="costo" maxlength="20" size="15" onClick="" onChange="" class="inputnum" style=""  />

&nbsp;
                                                  </td>
            </tr>
      
              <tr id="tr_cantidad">
  
                      
                                                <td style="width:120px;" class="littletableheader">Cantidad*</td>
              <td style="padding:1px;" class="littletablerow" id="td_cantidad">
              
<input type="text" name="cantidad" value="0" id="cantidad" maxlength="15" size="10" onClick="" onChange="" class="inputnum" style=""  />

&nbsp;
                                                  </td>
            </tr>
      
              <tr id="tr_importe">
  
                      
                                                <td style="width:120px;" class="littletableheader">Importe*</td>
              <td style="padding:1px;" class="littletablerow" id="td_importe">
              
<input type="text" name="importe" value="0.0" id="importe" maxlength="20" size="15" onClick="" onChange="" class="inputnum" style=""  />

&nbsp;
                                                  </td>

            </tr>
      
              <tr id="tr_flote">
  
                      
                                                <td style="width:120px;" class="littletableheader">Fecha de lote</td>
              <td style="padding:1px;" class="littletablerow" id="td_flote">
              <input type="text" name="flote" value="26/10/2009" id="flote" size="11" onClick="" onChange="" class="input" style=""  />
 <img src="/proteoerp/system/application/rapyd/libraries/jscalendar/calender_icon.gif" id="flote_button" border="0" style="vertical-align:middle;" /><script language="javascript" type="text/javascript">
         Calendar.setup({
        inputField  : "flote",
        ifFormat    : "%d/%m/%Y",
        button      : "flote_button",
        align       : "Bl",
        singleClick : false,
        mondayFirst : true,
        weekNumbers : false
       });</script>
&nbsp;
                                                  </td>

            </tr>
                
  
      </table>
      <script language="javascript" type="text/javascript"></script>
      </div>
      <div class="mainfooter">
        <div>
          <div style="float:left"></div>
          <div style="float:right"></div>

        </div><div style="clear:both;"></div>
      </div>
    </td>
  </tr>
</table>
</form>
</td>
		</tr>
	</table>
	
	<div class="footer">

		<a href="#" onClick="window.close()">Cerrar</a>
		<p>Tiempo de la consulta 0.1619 seg | Proteo ERP </p>
	</div>
</div>
</body>
</html>