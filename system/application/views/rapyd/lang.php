
  <?php
  /*
    this is a view file with some language helper fnc.
    - lang($key) is a call to $this->lang->line($key [,$current lang])
    - site_url_lang($key)  is the same of site_url()  but with "first segment" support
  */
  ?>

  <div>

    <h2>Lang sample</h2>


  this is a text from CI language: 
  <strong><?php echo lang("date_days");?></strong>, 
  <strong><?php echo lang("date_hours");?></strong>,
  <strong><?php echo lang("date_minutes");?></strong>
  
  <br/>
  (try to switch language).<br/><br/>
  
  
  current system uri is <strong><?php echo site_url("rapyd/lang");?></strong><br/>
  current language uri is <strong><?php echo site_url_lang("rapyd/lang");?></strong>
  




  </div>
