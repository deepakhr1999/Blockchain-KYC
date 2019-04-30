<?php 
  include_once 'navs.php';
  include 'con.php';
  session_start();
  $cmd = "NULL";
  if(! isset($_SESSION["username"])){
    $_SESSION["message"] = "Please login first";
    $_SESSION["message_tag"]="alert-danger";
    header("Location: ".$home);
  }

  if ($_SERVER['REQUEST_METHOD']=="POST") {
      $cmd = "docker exec cli scripts/".$_POST["name"]." 2>&1";
      $result1 = shell_exec($cmd);
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
        width: 80%;
        margin: 1em;
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
          <a class="nav-link" href="#">
            <?php
              echo $_SESSION["username"]."@".$_SESSION["bank"];
            ?>
          </a>
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
    <form class="m-3 p-3 terminal" method="Post"
      action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
      <legend>Get User</legend><hr>
      <div class="form-group">
          <label>Name:</label>
          <input type="text" class="form-control" placeholder="Full name" name="name">
      </div>     
      <button class="btn btn-primary">Submit</button>
    </form>
    <br><br>
    
     <div class="d-flex justify-content-around w-100 flex-wrap">
       <table class="table table-striped">
         <thead class="thead-dark">
           <tr>
              <th scope="col">Result</th>
            </tr>
         </thead>
         <tbody>
            <?php 
                echo "<tr><td>".$cmd."</td></tr>";
                if(isset($result1)){
                  echo "<tr><td>".$result1."</td></tr>";
                }else{
                  echo "<tr><td>Error</td></tr>";
                }
             ?>
         </tbody>
       </table>
    </div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>