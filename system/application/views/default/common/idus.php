<div id="form_usr"><?=$idus."<br>DB: ".$this->db->database;
if(isset($_SERVER['REMOTE_ADDR'])){
echo ' IP: '.$_SERVER['REMOTE_ADDR'];
}
?></div>