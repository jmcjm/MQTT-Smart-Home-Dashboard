<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Web dashboard</title>
    <link rel="Stylesheet" type="text/css" href="1.css">
    <script src="https://kit.fontawesome.com/b6abf34817.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poiret+One">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/paho-mqtt/1.0.1/mqttws31.js" type="text/javascript"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script>
      <?php session_start(); ?>
      var usrname = <?php
      if (isset($_SESSION['loggedin'])) {
        echo '"login"';
      }
      else {
        echo '"error"';
      } ?>;
      var usrpasswd = <?php
      if (isset($_SESSION['loggedin'])) {
        echo '"password"';
      }
      else {
        echo '"error"';
      } ?>;
    </script>
    <script src="mqtt.js"></script>
    <script src="mqtt_poznan.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="theme-color" media="(prefers-color-scheme: light)" content="black">
    <meta name="theme-color" media="(prefers-color-scheme: dark)"  content="black">
    <link rel="manifest" href="manifest.json">
    <script>
    function hide_show(where) {
      var elements = document.getElementsByClassName(where);
      document.getElementsByClassName(where)
      if (elements) {
        for (var x = 0; x < elements.length; x++) {
          if (document.getElementById(where+"_check").style.visibility === 'hidden') {
            elements[x].style.visibility = "visible";
            elements[x].style.height = 'auto';
            elements[x].style.marginTop = '30px';
          }
          else {
            elements[x].style.visibility = "hidden";
            elements[x].style.height = '0px';
            elements[x].style.marginTop = '1px';
          }
        }
      }
    }

    function hexToRgb(hex, where) {
      hex = hex.replace('#','');
      var bigint = parseInt(hex, 16);
      var r = (bigint >> 16) & 255;
      var g = (bigint >> 8) & 255;
      var b = bigint & 255;

      var tenbitR = eightToTen (r);
      var tenbitG = eightToTen (g);
      var tenbitB = eightToTen (b);
      console.log(tenbitR + ", " + tenbitG + ", " + tenbitB);
      if (where === "poznan")
        send_color_poznan(tenbitR, tenbitG, tenbitB);
      else if (where === "sroda")
        send_color_sroda(tenbitR, tenbitG, tenbitB);
    }
    function eightToTen(eightbit) {
      return Math.round((eightbit/255)*1023).toString();
    }

    function load() {
      hide_show('poznan');
      hide_show('sroda');
      MQTTconnect();
    }
    </script>
</head>
<body link="#6cf" vlink="green" onload="load()">
  <?php
    if (!isset($_SESSION['loggedin'])) {
      header('Location: index.php');
      exit;
    }
  ?>
  <h1><p id="connection_mqtt_sroda">Brak połączenia z serwerem </p>
    <i class="fa-solid fa-house-signal"></i>
  </h1>
  <div class="boddy place" id="place_sroda">
    <div class="place_name top" onclick="hide_show('sroda')">
      <h1>Środa
        <i class="fa-solid fa-up-down"></i>
      </h1>
    </div>
    <div class="boddy sroda">
        <div class="content">
            <div class="top">
                <h1 class="woda">Ciepła woda</h1>
            </div>
            <div class="break"></div>
            <div class="box-div">
              <div class="info-box stan">
                <div class="text main_text">Stan grzania:</div>
                <div class="text stan_mqtt" id="stan_mqtt_sroda">stan</div>
              </div>
              <div class="info-box stan">
                <div class="text main_text">Pozostały czas:</div>
                <div class="text" id="time_mqtt_sroda">stan</div>
              </div>
              <div class="info-box">
                <div class="text main_text">Włącz na 1h</div>
                <div class="text button" onclick="send_message('sroda/tryb','1')">
                  <i class="fa-solid fa-1"></i>
                </div>
              </div>
              <div class="info-box">
                <div class="text main_text">Włącz na 2h</div>
                <div class="text button" onclick="send_message('sroda/tryb','2')">
                  <i class="fa-solid fa-2"></i>
                </div>
              </div>
              <div class="info-box">
                <div class="text main_text">Włącz bez limitu</div>
                <div class="text button" onclick="send_message('sroda/tryb','5')">
                  <i class="fa-solid fa-infinity"></i>
                </div>
              </div>
              <div class="info-box">
                <div class="text main_text">Wyłącz</div>
                <div class="text button" onclick="send_message('sroda/tryb','0')">
                  <i class="fa-solid fa-power-off"></i>
                </div>
                </div>
                    <div class="info-box stan">
                    <div class="text main_text">Temperatura:</div>
                    <div class="text" id="temp_mqtt_sroda">stan</div>
                </div>
                <div class="info-box stan">
                    <div class="text main_text">Ostatnia aktualizacja:</div>
                    <div class="text" id="last_update_water_mqtt_sroda">stan</div>
                </div>
            </div>
        </div>
    </div>
    <!-- LEDY -->
  <div class="boddy sroda" id="sroda_check">
        <div class="content" id="conten_ledy_sroda">
            <div class="top">
                <h1 class="ledy">Ledy</h1>
            </div>
            <div class="break"></div>
            <div class="box-div">
            <div class="info-box">
              <div class="text main_text">Wybierz kolor:</div>
              <input type="color" class="colorpicker" value="#33ccff" onchange="hexToRgb(this.value, 'sroda');">
            </div>
            <div class="info-box">
              <div class="text main_text">Czerwony</div>
              <div class="slidecontainer">
                <p id="red_value_sroda">0</p>
                <input type="range" min="0" max="1023" value="0" class="slider red" id="red_sroda" oninput="red_value_sroda.innerText = this.value" onchange="send_message('sroda/leds/red',this.value)">
              </div>
            </div>
            <div class="info-box">
              <div class="text main_text">Zielony</div>
              <div class="slidecontainer">
                <p id="green_value_sroda">0</p>
                <input type="range" min="0" max="1023" value="0" class="slider green" id="green_sroda" oninput="green_value_sroda.innerText = this.value" onchange="send_message('sroda/leds/green',this.value)">
              </div>
            </div>
            <div class="info-box">
              <div class="text main_text">Niebieski</div>
              <div class="slidecontainer">
                <p id="blue_value_sroda">0</p>
                <input type="range" min="0" max="1023" value="0" class="slider blue" id="blue_sroda" oninput="blue_value_sroda.innerText = this.value" onchange="send_message('sroda/leds/blue',this.value)">
              </div>
            </div>
            <div class="info-box">
              <div class="text main_text">Biały</div>
              <div class="slidecontainer">
                <p id="white_value_sroda">0</p>
                <input type="range" min="0" max="1023" value="0" class="slider white" id="white_sroda" oninput="white_value_sroda.innerText = this.value" onchange="send_message('sroda/leds/white',this.value)">
              </div>
            </div>
            <div class="info-box">
              <div class="text main_text">Ostatnia aktualizacja:</div>
              <div class="text" id="last_update_leds_mqtt_sroda">stan</div>
            </div>
              <div class="info-box stan big_box">
                <div class="text main_text">Predefiniowane:</div>
                <div class="text ledy_mqtt">
                  <div class="led_color" style="background-color:rgb(255, 255, 255)" onclick="send_color_sroda('1023', '1023', '1023', '1023')"></div>
                  <div class="led_color" style="background-color:rgb(0, 0, 0)" onclick="send_color_sroda('0', '0', '0', '0')"></div>
                  <div class="led_color" style="background-color:rgb(186, 0, 112)" onclick="send_color_sroda('746', '0', '450')"></div>
                  <div class="led_color" style="background-color:rgb(82, 209, 255)" onclick="send_color_sroda('327', '838', '1023')"></div>
                  <div class="led_color" style="background-color:rgb(255, 82, 186)" onclick="send_color_sroda('1023', '327', '746')"></div>
                  <div class="led_color" style="background-color:rgb(204, 255, 0)" onclick="send_color_sroda('818', '1023', '0')"></div>
                  <div class="led_color" style="background-color:rgb(255, 61, 106)" onclick="send_color_sroda('1023', '245', '425')"></div>
                  <div class="led_color" style="background-color:rgb(61, 255, 229)" onclick="send_color_sroda('245', '1023', '910')"></div>
                </div>
              </div>
            </div>
        </div>
      </div>
  </div>
  <div class="boddy place" id="place_poznan">
    <div class="place_name top" onclick="hide_show('poznan')">
      <h1>Poznań
        <i class="fa-solid fa-up-down"></i>
      </h1>
    </div>
    <!-- LEDY -->
    <div class="boddy poznan" id="poznan_check">
      <div class="content" id="conten_ledy_poznan">
        <div class="top">
          <h1 class="ledy">Ledy</h1>
        </div>
        <div class="break"></div>
        <div class="box-div">
          <div class="info-box">
            <div class="text main_text">Wybierz kolor:</div>
            <input type="color" class="colorpicker" value="#33ccff" onchange="hexToRgb(this.value, 'poznan');">
          </div>
          <div class="info-box">
            <div class="text main_text">Czerwony</div>
            <div class="slidecontainer">
              <p id="red_value_poznan">0</p>
              <input type="range" min="0" max="1023" value="0" class="slider red" id="red_poznan" oninput="red_value_poznan.innerText = this.value" onchange="send_message('poznan/leds/red',this.value)">
            </div>
          </div>
          <div class="info-box">
            <div class="text main_text">Zielony</div>
            <div class="slidecontainer">
              <p id="green_value_poznan">0</p>
              <input type="range" min="0" max="1023" value="0" class="slider green" id="green_poznan" oninput="green_value_poznan.innerText = this.value" onchange="send_message('poznan/leds/green',this.value)">
            </div>
          </div>
          <div class="info-box">
            <div class="text main_text">Niebieski</div>
            <div class="slidecontainer">
              <p id="blue_value_poznan">0</p>
              <input type="range" min="0" max="1023" value="0" class="slider blue" id="blue_poznan" oninput="blue_value_poznan.innerText = this.value" onchange="send_message('poznan/leds/blue',this.value)">
            </div>
          </div>
          <div class="info-box">
            <div class="text main_text">Biały</div>
            <div class="slidecontainer">
              <p id="white_value_poznan">0</p>
              <input type="range" min="0" max="1023" value="0" class="slider white" id="white_poznan" oninput="white_value_poznan.innerText = this.value" onchange="send_message('poznan/leds/white',this.value)">
            </div>
          </div>
          <div class="info-box">
            <div class="text main_text">Ostatnia aktualizacja:</div>
            <div class="text" id="last_update_leds_mqtt_poznan">stan</div>
          </div>
          <div class="info-box stan big_box">
              <div class="text main_text">Predefiniowane:</div>
              <div class="text ledy_mqtt">
                <div class="led_color" style="background-color:rgb(255, 255, 255)" onclick="send_color_poznan('1023', '1023', '1023', '1023')"></div>
                <div class="led_color" style="background-color:rgb(0, 0, 0)"        onclick="send_color_poznan('0', '0', '0', '0')"></div>
                <div class="led_color" style="background-color:rgb(255, 129, 0)"    onclick="send_color_poznan('1023', '246', '0')"></div>
                <div class="led_color" style="background-color:rgb(186, 0, 112)"  onclick="send_color_poznan('746', '0', '450')"></div>
                <div class="led_color" style="background-color:rgb(82, 209, 255)" onclick="send_color_poznan('327', '838', '1023')"></div>
                <div class="led_color" style="background-color:rgb(255, 82, 186)" onclick="send_color_poznan('1023', '63', '505')"></div>
                <div class="led_color" style="background-color:rgb(204, 255, 0)"  onclick="send_color_poznan('1023', '673', '0')"></div>
                <div class="led_color" style="background-color:rgb(227, 0, 255)" onclick="send_color_poznan('1023', '0', '1023')"></div>
                <div class="led_color" style="background-color:rgb(61, 255, 229)" onclick="send_color_poznan('245', '1023', '910')"></div>
                <div class="led_color" style="background-color:rgb(62, 20, 255)"  onclick="send_color_poznan('245', '0', '1023')"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  <p id="copyrigh">Copyright Ⓒ  Jakub JMc Mendyka (v1.3_public)</p>
  <script>
  if (!navigator.serviceWorker.controller) {
     navigator.serviceWorker.register("/service.js").then(function(reg) {
         console.log("Service worker has been registered for scope: " + reg.scope);
     });
  }
</script>
</body>
</html>
