<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <title>Web dashboard - login</title>
  <link rel="Stylesheet" type="text/css" href="1.css">
  <script src="https://kit.fontawesome.com/b6abf34817.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poiret+One">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/paho-mqtt/1.0.1/mqttws31.js" type="text/javascript"></script>
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="theme-color" media="(prefers-color-scheme: light)" content="black">
  <meta name="theme-color" media="(prefers-color-scheme: dark)"  content="black">
  <link rel="manifest" href="manifest.json">
</head>
<body link="#6cf" vlink="green" >
  <?php
    session_start();
    if (isset($_SESSION['loggedin'])) {
      header('Location: home.php');
      exit;
    }
  ?>
    <div class="boddy place" id="place_sroda">
      <div class="place_name top" onclick="hide_show('sroda')">
        <h1>MQTT Web Dashboard <a href="https://jmcjm.ct8.pl" target="_blank" class="woda">by JMc</a></h1>
      </div>
      <div class="boddy sroda">
        <div class="content" style="width:280px;margin:auto;margin-bottom:30px;">
          <h1 class="">Logowanie</h1>
          <div class="break"></div>
          <div class="box-div login-box">
            <div class="login-div">
              <form action="login.php" method="post">
                <i class="fas fa-user"></i> <input type="text" placeholder="Login" id="login" name="login" required/>
                <i class="fas fa-lock"></i> <input type="password" placeholder="Hasło" id="passwd" name="passwd" required/><br/>
                <input type="submit" id="login-button" value="zaloguj się"/>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <p id="copyrigh">Copyright Ⓒ  Jakub JMc Mendyka (v1_public)</p>
  <script>
    if (!navigator.serviceWorker.controller) {
       navigator.serviceWorker.register("/service.js").then(function(reg) {
           console.log("Service worker has been registered for scope: " + reg.scope);
       });
    }
    if ('credentials' in navigator) {
      navigator.credentials.get({password: true, mediation: "silent"})
        .then((creds) => {
          document.getElementById("login").value=creds.id;
          document.getElementById("passwd").value=creds.password;
          document.getElementById("login-button").click();
        });
        }
  </script>
</body>
</html>
