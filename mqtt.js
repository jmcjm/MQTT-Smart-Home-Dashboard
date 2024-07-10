var connected_flag=0;
var mqtt;
var reconnectTimeout = 2000;
var host="mqtt_server";
var port=1883;
var sub_topic="#";
var date;
var seconds;
var minutes;
var hour;
function onConnectionLost(){
  console.log("connection lost");
  document.getElementById("connection_mqtt_sroda").innerHTML = "Brak połączenia z serwerem ";
  connected_flag=0;
  MQTTconnect();
}

function onFailure(message) {
  console.log("Failed");
  document.getElementById("connection_mqtt_sroda").innerHTML = "Brak połączenia z serwerem ";
    setTimeout(MQTTconnect, reconnectTimeout);
}

function onMessageArrived(r_message){
  out_msg="Message received "+r_message.payloadString+" ";
  out_msg=out_msg+"in topic "+r_message.destinationName;
  //console.log("Message received ",r_message.payloadString);
  console.log(out_msg);
  date = new Date;
  seconds = date.getSeconds();
  minutes = date.getMinutes();
  hour = date.getHours()
  if (seconds < 10) {
    seconds="0"+seconds;
  }
  if (minutes < 10) {
    minutes="0"+minutes;
  }
  if (hour < 10) {
    hour="0"+hour;
  }
  var topic=r_message.destinationName;
  if(topic=="sroda/stan") {
    document.getElementById("stan_mqtt_sroda").innerHTML =r_message.payloadString;
    document.getElementById("last_update_water_mqtt_sroda").innerHTML =hour+":"+minutes+":"+seconds;
  }
  if(topic=="sroda/temp") {
    document.getElementById("temp_mqtt_sroda").innerHTML =r_message.payloadString+"&deg"+"C";
  }
  if(topic=="sroda/TimeLeft") {
    document.getElementById("time_mqtt_sroda").innerHTML =r_message.payloadString+"min";
  }
  if(topic=="sroda/leds/red") {
    document.getElementById("red_sroda").value =r_message.payloadString;
    document.getElementById("red_value_sroda").innerHTML =r_message.payloadString;
    document.getElementById("last_update_leds_mqtt_sroda").innerHTML =hour+":"+minutes+":"+seconds;
  }
  if(topic=="sroda/leds/green") {
    document.getElementById("green_sroda").value =r_message.payloadString;
    document.getElementById("green_value_sroda").innerHTML =r_message.payloadString;
  }
  if(topic=="sroda/leds/blue") {
    document.getElementById("blue_sroda").value =r_message.payloadString;
    document.getElementById("blue_value_sroda").innerHTML =r_message.payloadString;
  }
  if(topic=="sroda/leds/white") {
    document.getElementById("white_sroda").value =r_message.payloadString;
    document.getElementById("white_value_sroda").innerHTML =r_message.payloadString;
  }

  //poznan

  if(topic=="poznan/leds/red") {
    document.getElementById("red_poznan").value =r_message.payloadString;
    document.getElementById("red_value_poznan").innerHTML =r_message.payloadString;
    document.getElementById("last_update_leds_mqtt_poznan").innerHTML =hour+":"+minutes+":"+seconds;
  }
  if(topic=="poznan/leds/green") {
    document.getElementById("green_poznan").value =r_message.payloadString;
    document.getElementById("green_value_poznan").innerHTML =r_message.payloadString;
  }
  if(topic=="poznan/leds/blue") {
    document.getElementById("blue_poznan").value =r_message.payloadString;
    document.getElementById("blue_value_poznan").innerHTML =r_message.payloadString;
  }
  if(topic=="poznan/leds/white") {
    document.getElementById("white_poznan").value =r_message.payloadString;
    document.getElementById("white_value_poznan").innerHTML =r_message.payloadString;
  }
}
function onConnected(recon,url) {
  console.log(" in onConnected " +reconn);
}
function onConnect() {
   // Once a connection has been made, make a subscription and send a message.
  connected_flag=1
  document.getElementById("connection_mqtt_sroda").innerHTML = "Połączono z serwerem ";
  console.log("on Connect "+connected_flag);
  mqtt.subscribe(sub_topic);
  }

function MQTTconnect() {
  console.log("connecting to "+ host +" "+ port);
  var x=Math.floor(Math.random() * 10000);
  var cname="controlform-"+x;
  mqtt = new Paho.MQTT.Client("mqman.ct8.pl", Number(1883), cname);
  console.log("Connecting to " + host);
  var options = {
            onSuccess : onConnect,
            userName: usrname,
            password: usrpasswd,
            useSSL: true,
            cleanSession : false
        };
    mqtt.onConnectionLost = onConnectionLost;
    mqtt.onMessageArrived = onMessageArrived;
  //mqtt.onConnected = onConnected;
  mqtt.connect(options);
  return false;
}
function sub_topics(){
  document.getElementById("messages").innerHTML ="";
  if (connected_flag==0){
    console.log(out_msg);
    return false;
  }
  var stopic= document.forms["subs"]["Stopic"].value;
  console.log("Subscribing to topic ="+stopic);
  mqtt.subscribe(stopic);
  return false;
}
function send_message_2(msg,topic){
  if (connected_flag==0){
    console.log("No connection");
    return false;
  }
  console.log("message = "+msg);
  console.log("topic = "+topic);
  message = new Paho.MQTT.Message(msg);
  message.destinationName = topic;
  mqtt.send(message);
  return false;
}
function send_message(topic,value){
  console.log("message = "+value);
  console.log("topic = "+topic);
  message = new Paho.MQTT.Message(value);
  message.destinationName = topic;
  mqtt.send(message);
}
function send_color_sroda(red,green,blue,white){
  send_message('sroda/leds/red',red);
  send_message('sroda/leds/green',green);
  send_message('sroda/leds/blue',blue);
  if (white == 1023 || white == 0) {
    send_message('sroda/leds/white',white);
  }
}

function send_color_poznan(red,green,blue,white){
  send_message('poznan/leds/red',red);
  send_message('poznan/leds/green',green);
  send_message('poznan/leds/blue',blue);
  if (white == 1023 || white == 0) {
    send_message('poznan/leds/white',white);
  }
}
