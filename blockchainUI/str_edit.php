<?php 
function geekify($field){
	return str_replace(" ", "_", $field);
}

function ungeekify($field){
	return str_replace("_", " ", $field);
}

?>