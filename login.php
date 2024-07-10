<?php
  session_start();

  $DATABASE_HOST = 'db_host';
  $DATABASE_USER = 'login';
  $DATABASE_PASS = 'password';
  $DATABASE_NAME = 'db_name';

  $con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
  if ( mysqli_connect_errno() ) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
  }
  if ( !isset($_POST['login'], $_POST['passwd']) ) {
     exit('Please fill both the login and password fields!');
  }
  if ($stmt = $con->prepare('SELECT id, password FROM accounts WHERE username = ?')) {
    // Bind parameters (s = string, i = int, b = blob, etc)
    $stmt->bind_param('s', $_POST['login']);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
      $stmt->bind_result($id, $password);
      $stmt->fetch();
      if (password_verify($_POST['passwd'], $password)) {
        session_regenerate_id();
        $_SESSION['loggedin'] = TRUE;
        $_SESSION['name'] = $_POST['login'];
        $_SESSION['id'] = $id;
        echo 'Welcome ' . $_SESSION['name'] . '!';
      }
      else {
        echo 'Incorrect login and/or password!';
        header('Location: index.php');
      }
    }
    else {
      echo 'Incorrect login and/or password!';
      header('Location: index.php');
    }

    $stmt->close();
  }

  if (isset($_SESSION['loggedin'])) {
    header('Location: home.php');
    exit;
  }

?>
