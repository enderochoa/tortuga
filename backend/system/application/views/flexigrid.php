<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Flexigrid Implemented in CodeIgniter</title>
<?=style("style.css"); ?>
<?=style("flexigrid.css"); ?>

<?=script("jquery.js"); ?>
<?=script("flexigrid.pack.js"); ?>

</head>
<body>
<?=$js_grid; ?>
<script type="text/javascript">

function test(com,grid)
{
    if (com=='Select All')
    {
		$('.bDiv tbody tr',grid).addClass('trSelected');
    }
    
    if (com=='DeSelect All')
    {
		$('.bDiv tbody tr',grid).removeClass('trSelected');
    }
    
    if (com=='Delete')
        {
           if($('.trSelected',grid).length>0){
			   if(confirm('Delete ' + $('.trSelected',grid).length + ' items?')){
		            var items = $('.trSelected',grid);
		            var itemlist ='';
		        	for(i=0;i<items.length;i++){
						itemlist+= items[i].id.substr(3)+",";
					}
					$.ajax({
					   type: "POST",
					   url: "<?=site_url("/flexigrid/deletec");?>",
					   data: "items="+itemlist,
					   success: function(data){
					   	$('#flex1').flexReload();
					  	alert(data);
					   }
					});
				}
			} else {
				return false;
			} 
        }          
} 
</script>
<div style="font-size:18px; text-align:center"><a href="<?=site_url("/flexigrid/index");?>">Demo</a> | <a href="<?=site_url("/flexigrid/example");?>">Documentation</a> | <a href="http://flexigrid.eyeviewdesign.com/<?=$download_file;?>">Download</a></div>


<table id="flex1" style="display:none"></table>


</body>
</html>