<?php 
  include_once 'navs.php';
  session_start();
  session_unset();
  $_SESSION["message"]="You have logged out successfully";
  $_SESSION["message_tag"]="alert-success";
  header("Location: ".$home);
?>
