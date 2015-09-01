<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
       "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>phpFreeChat demo</title>
    <?php if(isset($head)) echo $head; ?>
  	<?=style("ventanas.css");?>
  </head>
  <body>
  	<div id='encabe'></div>
    <?php if(isset($content)) echo $content; ?>
   <div class="footer">
		<a href="#" onclick="window.close()">Cerrar</a>
		<p>Tiempo de la consulta {elapsed_time} seg | Tortuga </p>
	</div>
  </body>
  
</html>