function create_menu(basepath)
{
	var base = (basepath == 'null') ? '' : basepath;

	document.write(
		'<table cellpadding="0" cellspaceing="0" border="0" style="width:98%"><tr>' +
		'<td class="td" valign="top">' +

		'<h3>Basic Info</h3>' +
		'<ul>' +
			'<li><a href="'+base+'general/requirements.html">Server Requirements</a></li>' +
			'<li><a href="'+base+'changelog.html">Change Log</a></li>' +
			'<li><a href="'+base+'general/credits.html">Credits</a></li>' +
		'</ul>' +	
		
		'<h3>Installation</h3>' +
		'<ul>' +
			'<li><a href="'+base+'installation/downloads.html">Downloading Rapyd Library</a></li>' +
			'<li><a href="'+base+'installation/index.html">Installation Instructions</a></li>' +
		'</ul>' +
		
		'</td><td class="td_sep" valign="top">' +
		
		'<h3>Introduction</h3>' +
		'<ul>' +
			'<li><a href="'+base+'overview/at_a_glance.html">Rapyd at a Glance</a></li>' +
			'<li><a href="'+base+'overview/features.html">Supported Features</a></li>' +
		'</ul>' +	
				
		'<h3>General Topics</h3>' +
		'<ul>' +
			'<li><a href="'+base+'general/index.html">Getting Started</a></li>' +
			'<li><a href="'+base+'general/concepts.html" class="importante">General Concepts</a></li>' +
			'<li><a href="'+base+'general/views.html">Rapyd Views/Themes</a></li>' +
		'</ul>' +	
		
		'</td><td class="td_sep" valign="top">' +
		
		'<h3>Main Classes</h3>' +
		'<ul>' +
		'<li><a href="'+base+'classes/session.html">Session Class</a></li>' +
		'<li><a href="'+base+'classes/uri.html">URI Class</a></li>' +
		'<li><a href="'+base+'classes/language.html">Language Class</a></li>' +
		'<li><a href="'+base+'classes/authorization.html">Authorization Class</a></li>' +    
		'</ul>' +	

		'<h3>Presentation Components</h3>' +
		'<ul>' +
		'<li><a href="'+base+'classes/dataset.html">DataSet Class</a></li>' +
		'<li><a href="'+base+'classes/datatable.html">DataTable Class</a></li>' +
		'<li><a href="'+base+'classes/datagrid.html">DataGrid Class</a></li>' +		
		'</ul>' +	
    
		'</td><td class="td_sep" valign="top">' +
		'<h3>Editing Components</h3>' +
		'<ul>' +
		'<li><a href="'+base+'classes/fields.html">Field Classes</a></li>' +
		'<li><a href="'+base+'classes/dataobject.html">DataObject Class</a></li>' +
		'<li><a href="'+base+'classes/dataform.html">DataForm Class</a></li>' +		
		'<li><a href="'+base+'classes/dataedit.html">DataEdit Class</a></li>' +
		'<li><a href="'+base+'classes/datafilter.html">DataFilter Class</a></li>' +
		'</ul>' +
		
		'</td></tr></table>');
}



function create_header(basepath)
{
	var base = (basepath == 'null') ? '' : basepath;

		document.write(
		'<div id="nav"><div id="nav_inner">');+
		create_menu(base);
		document.write(
		'</div></div>'+
		'<div id="nav2"><a name="top"></a><a href="javascript:void(0);" onclick="myHeight.toggle();" ><img id="toggle_button" src="'+base+'images/nav_toggle.jpg" width="153" height="44" border="0" title="Toggle Table of Contents" alt="Toggle Table of Contents" style="vertical-align:middle" /></a></div>'+
		'<div id="masthead">'+
		'<table cellpadding="0" cellspacing="0" border="0" style="width:100%">'+
		'<tr>'+
		'<td><h1 style="float:left;">Rapyd Library Guide Version 0.9.8</h1> <div style="float:left; margin-left:10px; padding-top:3px"> (for Code Igniter 1.5.4)</div></td>'+
		'<td id="breadcrumb_right"><a href="'+base+'toc.html">Table of Contents</a></td>'+
		'</tr>'+
		'</table>'+
		'</div>');
}


function create_footer()
{
		document.write(
		'<div id="footer">'+
		'<p><a href="#top">Top of Page</a><p>'+
		'<p>Rapyd Library is an Open Source Project see <a href="http://www.rapyd.com/main/authors">details</a>'+
		'<br/><a href="http://www.codeigniter.com">Code Igniter</a> is a free php framework of <a href="http://ellislab.com">Ellislab, Inc.</a></p>'+
		'</div>');
}


function create_search()
{
		document.write('<form method="get" action="http://www.google.com/search"><input type="hidden" name="as_sitesearch" id="as_sitesearch" value="www.rapyd.com/rapyd_guide/" />Search Rapyd Guide&nbsp; <input type="text" class="input" style="width:200px;" name="q" id="q" size="31" maxlength="255" value="" />&nbsp;<input type="submit" class="submit" name="sa" value="Go" /></form>');
}



window.onload = function() {
	myHeight = new fx.Height('nav', {duration: 400});
	//myHeight.hide();
  
  dp.SyntaxHighlighter.addControls = false;
  dp.SyntaxHighlighter.HighlightAll('code'); 

}

