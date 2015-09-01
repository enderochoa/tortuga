<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>Rapyd Components - Samples</title>
<style type="text/css">


<?php echo $style?>


</style>

<?php echo $rapyd_head?>
</head>
<body>

	<div id="content">
  
  
    <h1>Rapyd Samples</h1>
		<div>
      <div style="float:left; width:230px">
        Rapyd <?php echo RAPYD_VERSION?> | CI 1.5.4
      </div>
      <div style="float:left; width:250px">
      </div>
      <div style="float:right; width150px;">
       current language: <?php echo $this->config->item("language")?>&nbsp;<?php echo $language_links?>
      </div>
      <div style="float:right; width150px;margin-right:20px">
       current theme: <?php echo $theme?>
       <?php echo anchor("rapyd/utils/theme/default","default")?>
       <?php echo anchor("rapyd/utils/theme/clean","clean")?>
       <?php echo anchor("rapyd/utils/theme/black","black")?>
      </div>
      <div style="clear:both"></div>
    </div>
  
		<div class="line"></div>


    <div class="left">

      <div>&lt; <?php echo anchor("","Welcome")?></div>
      <div>&lt; <a href="<?php echo base_url()?>user_guide/">User Guide</a></div>
      <div>&lt; <a href="<?php echo base_url()?>rapyd_guide/">Rapyd Guide</a></div>
      
      <br />
      <div><?php echo anchor("rapyd/samples/index","Index")?></div>

      <div class="line"></div>

      <h3>data presentation</h3>
      <div><?php echo anchor("rapyd/samples/dataset","DataSet")?></div>
      <div><?php echo anchor("rapyd/samples/datatable","DataTable")?></div>
      <div><?php echo anchor("rapyd/samples/datagrid","DataGrid")?></div>
      
      <h3>data editing</h3>
      <div><?php echo anchor("rapyd/datam/dataform","DataForm")?></div>
      <div><?php echo anchor("rapyd/crudsamples/filteredgrid","DataFilter + DataGrid")?></div>
      <br />
      <div><?php echo anchor("rapyd/crudsamples/dataedit/show/1","DataEdit")?> + one-to-many</div>
      <div><?php echo anchor("rapyd/supercrud/dataedit/show/1","DataEdit")?> + many-to-many</div>
      <br />
      <div><?php echo anchor("rapyd/crudworkflow/gridedit/osp/0","DataGrid + DataEdit")?></div>

      
      <h3>prototype &amp; ajax</h3>
      <div><?php echo anchor("rapyd/ajaxsamples/ajaxsearch","DataFilter + Ajax")?></div>
      
      <div class="line"></div>
      
      <h3>orm &amp; dataobject</h3>
      <div><?php echo anchor("rapyd/datam/dataobject","DataObject")?> &amp; rel support</div>
      <div><?php echo anchor("rapyd/datam/prepostprocess","DataObject")?> &amp; callbacks</div>

      <div class="line"></div>
      
      <h3>auth class &amp; helper</h3>
      <div><?php echo anchor("rapyd/auth","Auth")?> login, logged, logout</div>

      <div class="line"></div>
      
      <h3>lang class &amp; helper</h3>
      <div><?php echo anchor("rapyd/lang","Lang")?> switch &amp; browser detect</div>
      
      <div class="line"></div>
      
      <h3>Support</h3>
      <div><a href="http://www.rapyd.com">Rapyd Website</a></div>
      <div><a href="http://www.rapyd.com/main/support">Donate</a></div>

      <div class="line"></div>

      <div><?php //anchor("rapyd/tests","tests (dev)")?></div>
      
      <div class="line"></div>
      
      note: from DataGrid sample a <?php echo anchor("rapyd/samples/index","test database")?> is required<br />

      <div class="line"></div> 
    </div>
    
    
		<div class="right">

      <?php echo $content?>
      
      <div class="line"></div>
      
      <div class="code"><?php echo $code?></div>

		</div>
    
    <div class="line"></div>
    
		<div class="footer">
			<p>rendered in {elapsed_time} seconds | ver <?php echo RAPYD_VERSION?> | <a href="http://www.codeigniter.com">Code Igniter Home</a> | <a href="http://www.rapyd.com">Rapyd Library Home</a> |  <a href="http://www.skinsoftware.com">SkinSoftware.com</a></p>
		</div>
    
	</div>

  
</body>
</html>