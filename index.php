<?php
include './config/config.php';

use Firebase\JWT\JWT;

require_once('./vendor/autoload.php');
$key = "DPKNnipvwpiwVEPOJIVEWPJWVDjiopswvdJIPWVEDNPKwvdJPOIWVDnkp";

$username = '';
$role = '';

if (!$_COOKIE['token']) {
  header("Location: login.php");
} else {
  $jwt = $_COOKIE['token'];
  $decoded = JWT::decode($_COOKIE['token'], $key, array('HS512'));
  if ($decoded->exp > time()) {
    $username = $decoded->username;
    $role = $decoded->role;

    echo "Logged user: ";
    echo $username;
    echo "<br/> Role: ";
    echo $role;
  } else {

    header("Location: login.php");
  }


  if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if ($_POST['id'] && $_POST['title']) {
      
      $updateSql = "UPDATE todo set title = '" . $_POST['title'] . "' WHERE id  = '" . $_POST['id'] . "'";
      $conn->query($updateSql);
      header("Location: index.php");
    }else if ($_POST['id']) {
      $deleteSql = "DELETE FROM todo where id = '" . $_POST['id'] . "'";
      $conn->query($deleteSql);
      header("Location: index.php");
    } else {
      $postSql = "INSERT INTO todo(title) value('" . $_POST['title'] . "') ";
      $conn->query($postSql);
      header("Location: index.php");
    }
  }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome</title>
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <link rel="stylesheet" href="css/index.css">
</head>

<body>

  <?php

  $sql = 'SELECT * FROM todo';
  $result = $conn->query($sql);

  echo "<div class='container'>";
  if ($role == 'ADMIN') {
    echo "<div class='text-right'><button  class='btn btn-primary' data-toggle='modal' data-target='#exampleModal'>Dodaj</button></div>";
    echo "<div class='modal fad' id='exampleModal' tabindex='-1' role='dialog' aria-labelledby='exampleModalLabel' aria-hidden='true'>
        <div class='modal-dialog' role='document'>
          <div class='modal-content'>
            <div class='modal-header'>
              <h5 class='modal-title' id='exampleModalLabel'>Dodaj </h5>
              <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                <span aria-hidden='true'>&times;</span>
              </button>
            </div>
            <div class='modal-body'>
              <form method='POST'>
                <div class='form-group'>
                <label>Naziv</label>
                <input type='text' name='title' class='form-control' >
            </div>
            </div>
            <div class='modal-footer'>
              <button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>
              <button type='submit' class='btn btn-primary'>Save changes</button>
              </form>
            </div>
          </div>
        </div>
      </div>";
  }

  if ($role == 'ADMIN' || $role == 'MODERATOR') {
    echo "<div class='modal fad' id='editModal' tabindex='-1' role='dialog' aria-labelledby='exampleModalLabel' aria-hidden='true'>
        <div class='modal-dialog' role='document'>
          <div class='modal-content'>
            <div class='modal-header'>
              <h5 class='modal-title' id='editModal'>Izmeni </h5>
              <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                <span aria-hidden='true'>&times;</span>
              </button>
            </div>
            <div class='modal-body'>
              <form method='POST'>
                  <div class='form-group'>
                <label>Naziv</label>
                <input type='text' name='title' class='form-control' >
                <input type='hidden' id='idInput' value='' name='id' class='form-control' >

              <button type='submit' class='btn btn-primary'>Save changes</button>
                </form>
            </div>
            </div>
            <div class='modal-footer'>
              <button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>
      
            </div>
          </div>
        </div>
      </div>";
  }
  while ($row = $result->fetch_assoc()) {
    echo "<div class='inline'> <h5><b>" . $row['title'] . "</b></h5><form method='post'><input type='hidden' class='hiddenToDoId' name='id' value='$row[id]'>";

    if ($role == 'ADMIN') {
      echo "<button type='submit' class='btn btn-danger' name='delete'>Obriši </button>";
      echo "<button type='button' class='btn btn-warning' onclick='setValueToEditToDoForm($row[id])' data-toggle='modal'  data-target='#editModal'>Izmeni </button></form>";
    } else if ($role == 'MODERATOR') {
      echo "<button type='button' class='btn btn-warning' onclick='setValueToEditToDoForm($row[id])' data-toggle='modal' data-target='#editModal'>Izmeni </button></form>";
    }

    echo "</div>";
  }

  echo "</div>";
  ?>

  <div class="text-right container">
    <button class='btn btn-danger' onclick="logout()">Logout</button>
  </div>


  <script>
    function logout() {
      document.cookie = 'token=';
      location.reload();
    }

    function setValueToEditToDoForm(id) {
      document.getElementById('idInput').value = id;
    }
  </script>
</body>

</html>