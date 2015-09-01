<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
function numletra($numero){
    require_once("Numletra.php");

    $numa = new numletra();
    $numa->setNumero($numero);
    $numa->setSufijo("Bs.");
    $numa->setSufijo("Centimos");
    return $numa->letra();
}
?>