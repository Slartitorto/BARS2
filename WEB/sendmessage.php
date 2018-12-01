<?php

$destination=$_GET['destination'];
$message=$_GET['message'];
$key=$_GET['key'];
$subject=$_GET['subject'];
$channel=$_GET['channel'];
$codUtente=$_GET['codUtente'];

include "dbactions/db_connection.php";
$query = "SELECT * FROM server_settings";
$result = $conn->query($query);
while($row = $result->fetch_assoc())
{
  $sendmessage_key=$row["sendmessage_key"];
  $pushbullett_token=$row["pushbullett_token"];
  $telegram_BOT_id=$row["telegram_BOT_id"];
  $sms_sender=$row["sms_sender"];
}

if($key == $sendmessage_key)
{


  if ($channel == "pushbullett") {
    // Pushbullett (see https://wiki.onion.io/Tutorials/PHP-PushBullet-Example)
    $curl = curl_init('https://api.pushbullet.com/v2/pushes');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ["Authorization: Bearer $pushbullett_token"]);
    curl_setopt($curl, CURLOPT_POSTFIELDS, [ "type" => "note", "email" => "$destination", "title" => "$subject", "body" => "$message"]);
    curl_exec($curl);
    curl_close($curl);
  }


  if ($channel == "telegram") {
    // Telegram
    $url="https://api.telegram.org/$telegram_BOT_id/sendMessage?chat_id=$destination&text=$subject - $message";
    $curl = curl_init($url);
    echo $url;
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_exec($curl);
    curl_close($curl);
  }


  if ($channel == "sms")
  {
    if($codUtente == "NoCreditControl")
    { $query = "SELECT 1 as credit"; } else { $query = "SELECT credit FROM sms_usage where codUtente = '$codUtente' order by timestamp desc limit 1"; }
    $result = $conn->query($query);
    if(($result->num_rows) == 1)
    {
      $row = $result->fetch_assoc();
      $credit=$row["credit"];
      if ($credit > 0)
      {
        $message = $subject." - ".$message;
        require('./sms_gateway_php/RSSDK/sendsms.php');
        $sms = new Sdk_SMS();
        $sms->sms_type = SMSTYPE_ALTA;
        $tel_destination="+39" . $destination;
        $sms->add_recipient($tel_destination);
        $sms->message = $message;
        $sms->sender = $sms_sender;        // A phone number, or a registered alphanumeric sender
        $sms->set_immediate();
        $sms->order_id = '999FFF111';
        if ($sms->validate())
        {
          $res = $sms->send();
          if ($res['ok'])
          {
            // echo $res['sentsmss'] . ' SMS sent, order id is ' . $res['order_id'] . ' </br>';
            $credit = $credit - 1;
            $query = "INSERT INTO sms_usage (codUtente, destination, type, text, credit) values ('$codUtente', '$destination', 1, '$message', '$credit')";
            // echo $query;
            $result = $conn->query($query);
          } else {
            // echo 'Error sending SMS: ' . $sms->problem() . ' </br>';
          }
        }
      }
    }
  }
}
?>
