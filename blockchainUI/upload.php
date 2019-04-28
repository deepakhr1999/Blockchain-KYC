<?php 
    include_once "navs.php";
    session_start();
    include "con.php";
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_SESSION["person"] = $_POST["name"];
        $_SESSION["bank"] = $_POST["bank"];
        $_SESSION["dob"] = $_POST["dob"];
        $_SESSION["phone"] = $_POST["phone"];
        $_SESSION["aadhar"] = $_POST["aadhar"];

        $sql = 'select auto_increment from information_schema.tables where table_schema="data" and table_name="people"';
        $result = call($sql);
        $row = $result->fetch_assoc();
        $n = $row["auto_increment"];

        //initialize values
        $target_file = 'uploads/image'.$n;
        $_SESSION["message_tag"]="alert-danger";

        $uploadOk = getimagesize($_FILES["file"]["tmp_name"]);
        if($uploadOk== false){
            $_SESSION["message"] = "Please upload an image.";
        }

        // Check file size
        if ($_FILES["file"]["size"] > 500000) {
            $_SESSION["message"] = "File is too large.";
            $uploadOk = 0;
        }

        $uploaded = false;
        
        //move if no error
        if($uploadOk == true){
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                $uploaded = true;
                $_SESSION["message_tag"]="alert-success";
                $_SESSION["message"] = "The file ". basename($_FILES["file"]["name"]). " has been uploaded.";
            } else {
                session_unset();
                $_SESSION["message"] = "Sorry, there was an error uploading your file.";
            }
        }
        $_SESSION["file"] = $target_file;

        if($uploaded == true){
            $sql = 'insert into data.people (Name, Dob, Phone, Aadhar, Bank, Filename, Hash, is_pending) values ("%s", "%s", "%s", "%s", "%s", "%s", "%s", 1)';
            $sql = sprintf($sql,
                            $_SESSION["person"],
                            $_SESSION["dob"],
                            $_SESSION["phone"],
                            $_SESSION["aadhar"],
                            $_SESSION["bank"],
                            $target_file,
                            md5_file($target_file));

            include_once 'creds.php';

            $result = call($sql);     
        }
    }

    if (! isset($_SESSION["person"])){
        // header("Location: ".$home);
        die("error");
    }
 ?>
 <!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <title></title>
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
    <div class="d-flex p-3 m-3">
        <img src="<?php echo $_SESSION["file"] ?>" style="height:27em;">
        <table class="table table-striped m-3">
            <thead class="thead-dark">
              <tr>
                <th scope="col">Profile</th>
                <th scope="col"> </th>
              </tr>
            </thead>
            <tbody>
                <?php 
                    echo '<tr><th>Name:</th> <td>'.$_SESSION["person"].'</td></tr>';
                    echo '<tr><th>Bank:</th> <td>'.$_SESSION["bank"].'</td></tr>';
                    echo '<tr><th>Date of Birth:</th> <td>'.$_SESSION["dob"].'</td></tr>';
                    echo '<tr><th>Phone:</th> <td>'.$_SESSION["phone"].'</td></tr>';
                    echo '<tr><th>Aadhar:</th> <td>'.$_SESSION["aadhar"].'</td></tr>';
                 ?>
            </tbody>
        </table>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>