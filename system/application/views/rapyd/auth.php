
<?php
/*
  this is a view file with some auth & language helper
  
  - is_logged() check user status
  - check_role() ""   ""   role
  - get_user_data()  get & decrypt user data
  - anchor_lang()  is a replacement of anchor from url helper,
    that add/keep "language-segment" in the uri
*/
?>

  <div>

    <h2>Auth sample (status)</h2>


<?php if(is_logged()):?>

  you are logged as: <?php echo get_user_data("user_name");?> 
  
  <?php if(check_role(3)):?> 
    (administrator) 
  <?php endif;?> | 
  
  <?php echo anchor_lang("rapyd/auth/logout","Log Out")?> 
  
<?php else:?>

  you are not logged in, <?php echo anchor_lang("rapyd/auth/login","Log in")?>
  
<?php endif;?>





  </div>
