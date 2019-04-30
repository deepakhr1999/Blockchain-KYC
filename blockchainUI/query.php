<?php 
  include_once 'navs.php';
  include_once 'str_edit.php';
  include 'con.php';
  session_start();

  if(! isset($_SESSION["username"])){
    $_SESSION["message"] = "Please login first";
    $_SESSION["message_tag"]="alert-danger";
    header("Location: ".$home);
  }

  if ($_SERVER['REQUEST_METHOD']=="POST") {
      $peer = $_SESSION["peer"];
      $org = $_SESSION["org"];
      $name = geekify($_POST["name"]);
      $bank = $_SESSION["bank"];
      $result1 = shell_exec("docker exec cli scripts/query.sh public $peer $org $name $bank");
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

    <title>Online KYC - Query</title>
    <style type="text/css">
      .terminal, .table{
        width: 27em;
        margin: 1em;
      }
      #img{
        width: 100%;
      }
    </style>
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #1c4fc3;">
      <a class="navbar-brand" href="<?php echo $main ?>">Blockchain KYC</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item active">
          <a class="nav-link" href="<?php echo $main ?>">
            <?php
              echo $_SESSION["username"]."@".$_SESSION["bank"];
            ?>
          </a>
          </li>
          <li class="nav-item active">
            <a class="nav-link" href="<?php echo $query ?>">Query</a>
          </li>
        </ul>
        <?php 
            if(isset($_SESSION["username"])){
              echo '<form class="form-inline my-2 my-lg-0">
                      <ul class="navbar-nav mr-auto">
                        <li class="nav-item active">
                          <a class="nav-link" href="'.$logout.'">Logout</a>
                        </li>
                      </ul>
                    </form>';
            }
         ?>
      </div>
    </nav>
    <?php
        if ($_SESSION["message"]!="") {
          echo '<div class="alert '.$_SESSION["message_tag"].'">'.$_SESSION["message"].'</div>';
          $_SESSION["message"] = "";
        }
      ?> 
    <div class="w-100 clearfix">
      <form class="m-3 p-3 terminal float-left" method="Post"
        action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <legend>Get User</legend><hr>
        <div class="d-flex">
          <input type="text" class="form-control mr-3" placeholder="Full name" name="name">
          <button class="btn btn-primary">Submit</button>
        </div>     
      </form>
    </div>
    
     <div class="d-flex justify-content-around w-100 flex-wrap">
       <table class="table table-striped">
         <thead class="thead-dark">
           <tr>
              <th scope="col">Public details</th>
              <th scope="col"> </th>
            </tr>
         </thead>
         <tbody>
            <?php 
                if(isset($result1)){
                  $result_public = json_decode($result1, true);
                  if(! isset($result_public["name"])){
                    echo "<tr><th>Error:</th><td>Client Not Found</td></tr>";
                  }else{
                    echo "<tr><th>Name:</th><td>".ungeekify($result_public["name"])."</td></tr>";
                    echo "<tr><th>Date of birth:</th><td>".$result_public["dob"]."</td></tr>";
                    echo "<tr><th>Status:</th><td>".$result_public["flag"]."</td></tr>";
                    echo "<tr><th>Bank:</th><td>".$result_public["bank"]."</td></tr>";
                    $bank = $result_public["bank"];
                    $result2 =
                    shell_exec("docker exec cli scripts/query.sh private $peer $org $name $bank");
                  }
                }else{
                  echo "<tr><th>NULL</th><td>NULL</td></tr>";
                }
             ?>
         </tbody>
       </table>
       <table class="table table-striped">
         <thead class="thead-dark">
           <tr>
              <th scope="col">Private details</th>
              <th scope="col"> </th>
            </tr>
         </thead>
         <tbody>
            <?php 
                $image = "images/default.png";
                if(! isset($result2)){
                  echo "<tr><th>NULL</th><td>NULL</td></tr>";
                }
                elseif(strpos($result2, "endorsement failure during query") !== False){
                  echo "<tr><th>Error:</th><td>Could not get private details</td></tr>";
                }else{
                  $result_private = json_decode($result2, true);
                  $rimg = $result_private["file"];
                  if(file_exists($rimg)){
                    if(md5_file($rimg) == $result_private["hash"]){
                        $image = $rimg;
                    }
                  }else{
                    $image = "images/data_breach.jpg";
                  }
                  echo "<tr><th>Aadhar:</th><td>".$result_private["aadhar"]."</td></tr>";
                  echo "<tr><th>Phone:</th><td>".$result_private["phone"]."</td></tr>";
                  echo "<tr><th>Phone:</th><td>".$result_private["file"]."</td></tr>";
                  echo "<tr><th>Hash:</th><td>".$result_private["hash"]."</td></tr>";
                }
             ?>
         </tbody>
       </table>
    </div>
    <hr>
    <div class="m-5 text-center">
      <img id="img" src="<?php echo $image ?>">
    </div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>