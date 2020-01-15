<?php
  include_once 'navs.php';
  include "con.php"; 
  session_start();
  if(isset($_SESSION["username"])){
    header("Location: ".$main);
  }
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      if($_POST["form"]=="login"){
        //then this an attempt to login
        // $sql = "select * from data.peers where Username='%s' and password='%s'";
        // $sql = sprintf($sql, $_POST["username"], md5($_POST["password"]));
        // $result = call($sql);
        $user = $_POST["username"];
        $pass = $_POST["password"];
        if ($user=="Bank1" || $user=="Bank2" || $user=="Bank_admin") {
            
            $_SESSION["username"] = $user;
            $_SESSION["bank"] = $user;
            $_SESSION["peer"] = 0;

            if($user=="Bank1"){
              $_SESSION["org"] = 1;
            }elseif ($user == "Bank1") {
              $_SESSION["org"] = 2;
            }else{
              $_SESSION["org"] = 3;
            }
            header("Location: data.php");
        }
        //failed attempt
        $_SESSION["message_tag"]="alert-danger";
        $_SESSION["message"] = "Please check credentials";
      }
  }
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="https://www.blockchain.com/static/favicon.ico" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
    <title>Online KYC - Login</title>
    <style type="text/css">
      *{
        font-family: 'Lato', sans-serif;
      }
      section{
        margin: 0;
        padding: 3em 1em;
        display: flex; flex-wrap: wrap;
        justify-content: space-around;
        transition: 0.6s ease-in-out;
      }
      section:hover{
      	background-color: #eaeafa;
      }
      .inline{
        width: 30em;
        align-self: center;
        margin: auto 2em;
      }
      .bg{
      	width: 100%;
      	object-fit: cover;
      	height: 10em;
      }


    </style>
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #1c4fc3;">
      <a class="navbar-brand" href="<?php echo $main ?>">Blockchain KYC</a>
      <ul class="navbar-nav mr-auto">
          <li class="nav-item active">
            <a class="nav-link" href="<?php echo $upload ?>">Uploads</a>
          </li>
        </ul>
    </nav>
    <?php
      if( isset($_SESSION["message"]) ){
        if ($_SESSION["message"]!="") {
          echo '<div class="alert '.$_SESSION["message_tag"].'">'.$_SESSION["message"].'</div>';
          $_SESSION["message"] = "";
        }
      }
      ?> 

	<section>
      	<img class="inline" src="images/intro-img.svg">
      <!-- Login as a bank -->
        <form class="inline" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
          <legend>Login as bank</legend><hr>
           <div class="form-group">
            <label>Name:</label>
            <input type="text" class="form-control" name="username">
          </div>
          
          <div class="form-group">
            <label>Password:</label>
            <input type="text" class="form-control" name="password">
          </div>
          
          <p>
            Not a user?
            <a class="ml-2 card-link" href="<?php echo $register; ?>">Register</a></p>
          <button class="btn btn-primary btn-block mr-3" name="form" value="login">
            Login
          </button>
        </form>
    </section>
    <section>
    	<img class="inline" src="images/about-extra-2.svg">
    	<!-- Request a kyc for end users -->
		<form class="inline" method="POST" enctype="multipart/form-data"
		action="<?php echo htmlspecialchars($upload);?>">
			<legend>Request a KYC</legend><hr>
			<div class="form-group">
			  <label>Name:</label>
			  <input type="text" class="form-control" placeholder="Full name" name="name">
			</div>

			<div class="form-group">
			  <label>Date of birth:</label>
			  <input type="date" class="form-control" name="dob" id="date">
			</div>

			<div class="form-group">
			  <label>Phone</label>
			  <input type="text" class="form-control" placeholder="10 digit phone number" name="phone">
			</div>

			<div class="form-group">
			  <label>Aadhar number:</label>
			  <input type="text" class="form-control" placeholder="12 digits" name="aadhar">
			</div>

			<div class="form-group">
			  <label>Photo of document:</label>
			  <input type="file" name="file">
			</div>


			<?php 
			    // $sql = "select distinct Bank from data.peers";
			    // $result = call($sql);
			    // if($result->num_rows>0){
			    //   echo '<div class="form-group">';
			    //   echo '<label>Bank:</label>';
			    //   echo '<select name="bank" class="custom-select" placeholder="Choose your bank">';
			    //   while($row = $result->fetch_assoc()){
			    //     if($row["Bank"] != "Bank_admin"){
			    //       echo '<option value="'.$row["Bank"].'">'.$row["Bank"].'</option>';
			    //     }
			    //   }
			    //   echo '</select></div>';
			    // }
			 ?>
       <div class="form-group">
        <label>Bank:</label>
        <select name="bank" class="custom-select" placeholder="Choose your bank">
          <option value="Bank1">Bank1</option>
          <option value="Bank2">Bank2</option>
        </select>
      </div>
			<button class="btn btn-block btn-primary" name="form" value="kyc">Submit</button>
		</form>
    </section>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #1c4fc3; height: 15em;">
    	
    </nav>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>