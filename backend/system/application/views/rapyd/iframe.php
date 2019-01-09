<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<title>iframe</title>
<style type="text/css">


<?php echo $style?>


</style>


<?php echo $head?>

<script language="javascript" type="text/javascript">
function autofit_iframe(id){
 if(document.getElementById) {
   parent.document.getElementById(id).style.height = "150px";
	 parent.document.getElementById(id).style.height = this.document.body.scrollHeight+"px"
 }
}
</script>
</head>

<body class="iframe" onload="<?php echo $onload?>">
<?php echo $content?>

</body>
</html>