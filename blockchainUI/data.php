<?php 
  include_once 'navs.php';
  include 'con.php';
  session_start();
  include 'str_edit.php';
  if(! isset($_SESSION["username"])){
    $_SESSION["message"] = "Please login first";
    $_SESSION["message_tag"]="alert-danger";
    header("Location: ".$home);
  }

  if ($_SERVER['REQUEST_METHOD']=="POST") {
    if ($_POST["decision"]=="accept") {
        $_SESSION["message"] = "Accepted KYC!";
        $_SESSION["message_tag"]  = "alert-success";
        $sql = "update data.people set Bank='Bank_admin', is_pending=0 where Id = ".$_POST["Id"];
        $out = call($sql);
        $result = call("select * from data.people where Id = ".$_POST["Id"]);
        $row = $result->fetch_assoc();
        $Name = geekify($row["Name"]);
        $Id = $Name;
        $Dob = $row["Dob"];           $Phone = $row["Phone"];
        $Aadhar = $row["Aadhar"];     $Bank = geekify($row["Bank"]);
        $File = $row["Filename"];     $Hash = $row["Hash"];   
        $peer = $_SESSION["peer"];    $org = $_SESSION["org"];
        $Bank = $_SESSION["bank"];    $Coll = "KYCDataOne";
        if($Bank == "Bank_admin"){
          //only endorse this record
          shell_exec("docker exec cli scripts/endorse.sh $peer $org $Id"); 
          $_SESSION["message"] = "Endorsed KYC!";
          $sql = "delete from data.people where Id = ".$_POST["Id"];
          $out = call($sql);
        }else{
          if($bank == "BankTwo"){
            $Coll = "KYCDataTwo";
          }
          $out =
            shell_exec("docker exec cli scripts/apply.sh $peer $org $Id $Name $Dob $Bank $Phone $Aadhar $File $Hash $Coll");
        }
    }
    else{
        $_SESSION["message"] = "Rejected KYC!";
        $_SESSION["message_tag"]  = "alert-danger";
        $sql = "delete from data.people where Id = ".$_POST["Id"];
    }
    $result = call($sql);
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

    <title>Online KYC - Data</title>
    <style type="text/css">
      #file{
        padding: 1em;
        width: 40em;
        height: 37em;display: flex; justify-content: center; align-items: center;
        overflow-y: auto;
      }
      img{
        max-width: 38em;
      }
      .data-table{
        width: 30em;
      }
      @media only screen and (max-width: 660px) {
        img{
          height: 10em;
        }
        #file{
          padding: 0.5em;
          height: 11em;
        }
        .data-table{
          width: 20em;
        }
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
    <div class="m-3 p-3 d-flex flex-wrap align-items-center justify-content-around">
      
      <div class="data-table">
      <table class="table table-striped w-100">
        <thead class="thead-dark">
          <tr>
            <th scope="col">Profile</th>
            <th scope="col"> </th>
          </tr>
        </thead>
        <?php 
          $sql =
          "select * from data.people where Bank='".$_SESSION["bank"]."' and is_pending = 1 limit 1";
          if($_SESSION["bank"] == "Bank_admin"){
            $sql = "select * from data.people where Bank='Bank_admin' and is_pending = 0 limit 1";
          }
          $result = call($sql);
          $image = "images/default.png";
          if($row = $result->fetch_assoc()){
            $image = $row["Filename"];
            echo '<tr><th>Name:</th> <td>'.$row["Name"].'</td></tr>';
            echo '<tr><th>Date of Birth:</th> <td>'.$row["Dob"].'</td></tr>';
            echo '<tr><th>Phone:</th> <td>'.$row["Phone"].'</td></tr>';
            echo '<tr><th>Aadhar:</th> <td>'.$row["Aadhar"].'</td></tr>';
          }else{
            echo '<tr><th>Error:</th> <td>No more rows</td></tr>';
          }
         ?>
      </table>

      <?php 
        if($image == "images/default.png"){
          goto skipform;
        }
       ?>
      <form class="m-3 p-3" method="Post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
         <input type="text" name="Id" hidden value='<?php echo $row["Id"] ?>'>
         <button class="btn m-3 btn-primary" name="decision" value="accept">Accept</button>
         <button class="btn m-3 btn-danger" name="decision" value="decline">Decline</button>
      </form>
      <?php skipform: ?>
      </div>

      <div id="file" class="m-3">
        <img src="<?php echo $image; ?>" >
      </div>
    </div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>