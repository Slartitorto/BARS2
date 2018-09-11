<?php
// aggiornamento per Hooly 2 (atmega328 con rfm69hw e SHT3x)


include "dbactions/db_connection.php";

if((isset($_GET['data'])) and (isset($_GET['rssi'])) and (isset($_GET['router']))){
  $data=$_GET['data'];
  $rssi=$_GET['rssi'];
  if ($rssi < 1)
  {$rssi = 1;}
  $router=$_GET['router'];
  if(isset($_GET['repeater'])) $repeater=$_GET['repeater']; else $repeater=0;
} else exit();

date_default_timezone_set('Europe/Rome');

list($serial, $counter, $temp, $hum, $battery, $period) = explode(":",$data);

$temp = $temp/100;
$hum = $hum/100;

// aggiorno comunque il router_keep_alive
$result = $conn->query("DELETE FROM keep_alive_check WHERE router = '$router'");
$result = $conn->query("INSERT INTO keep_alive_check (router) VALUES ('$router')");
$result = $conn->query("UPDATE keep_alive_check SET alarmed = '0' WHERE router = '$router'");

$query = "SELECT armed, batt_alarmed, alarmed, min_ok, max_ok, device_name, position, tenant, batt_type from devices where serial = '$serial'";
$result = mysqli_query($conn,$query);
$row = mysqli_fetch_array($result);
$armed = $row[0];
$batt_alarmed = $row[1];
$alarmed = $row[2];
$min_ok = intval($row[3]);
$max_ok = intval($row[4]);
$device_name = $row[5];
$position = $row[6];
$tenant = $row[7];
$batt_type = $row[8];

if ($batt_type == 1) { // Li-Po 3.6V 240mA
  //  3.3 / 1024 = 0.003222 (v per digit) = 3.222 mV
  //  min = 3600/2/3.222 = 558 -> 0%
  //  verified 575 -> 0%
  //  max = 4200/2/3.222 = 651 -> 100%
  //  verified 651 -> 100%
  //  651 - 575 = 76 (range in digit)
  //  100/76 = 1.316 (range in % per digit)
  //  so:
  $batt_0 = 575;
  $batt_corr = 1.316;
}
if ($batt_type == 2) { // NiMH 3.7V 480mAh
  //  3.3 / 1024 = 0.003222 (v per digit) = 3.222 mV
  //  min = 2800/2/3.222 = 434 -> 0%
  //  max = 4200/2/3.222 = 651 -> 100%
  //  651 - 434 = 217 (range in digit)
  //  100/217 = 0.461 (range in % per digit)
  //  so:
  $batt_0 = 434;
  $batt_corr = 0.461;
}
if ($batt_type == 3) { // 3x AAA ministilo
  //  3.3 / 1024 = 0.003222 (v per digit) = 3.222 mV
  //  min = 3000/2/3.222 = 465 -> 0%
  //  max = 4500/2/3.222 = 698 -> 100%
  //  verified 720 -> 100%
  //  720 - 465 = 255 (range in digit)
  //  100/255 = 0.392 (range in % per digit)
  //  so:
  $batt_0 = 465;
  $batt_corr = 0.392;
}

$battery = intval(($battery - $batt_0) * $batt_corr);
if ($battery > 100) $battery = 100;
if ($battery < 1)   $battery = 0;

$query = "DELETE from last_rec_data where serial = '$serial'";
$result = mysqli_query($conn,$query);

$query = "INSERT INTO last_rec_data (serial,counter,temp,hum,battery,period,rssi,router)
VALUES('$serial','$counter','$temp','$hum','$battery','$period','$rssi','$router')";
$result = mysqli_query($conn,$query);

$query = "INSERT INTO rec_data (serial,counter,temp,hum,battery,period,rssi,router)
VALUES('$serial','$counter','$temp','$hum','$battery','$period','$rssi','$router')";
$result = mysqli_query($conn,$query);

if ($battery < 10 && $batt_alarmed == 0) { // Battery low in discharging
  // send battery alarm  ---------- TOBE DONE --------------
  $query = "UPDATE devices SET batt_alarmed = 1 WHERE serial = '$serial'";
  $result = mysqli_query($conn,$query);
}
if ($battery > 20 && $batt_alarmed == 1) { // Battery previously alarmed, but now charged
  $query = "UPDATE devices SET batt_alarmed = 0 WHERE serial = '$serial'";
  $result = mysqli_query($conn,$query);
}

if (($temp < $min_ok) or ($temp > $max_ok)) {
  // alarm condition half
  if (($armed == 1) and ($alarmed == 0)) {
    //alarm condition full

    $subject = "Allarme $device_name $position";
    $message = "Temperatura rilevata = $temp - oltre i limiti (min = $min_ok - max = $max_ok)";
    $headers = "From: admin@hoooly.eu \r\n" .
    "Reply-To: admin@hooly.eu \r\n";

    $query = "SELECT codUtente FROM utenti WHERE t0 = '$tenant' OR t1 = '$tenant' OR t2 = '$tenant' OR t3 = '$tenant'";
    $result = mysqli_query($conn,$query);
    while (($row = mysqli_fetch_array($result))) {
      $codUtente = $row[0];

      $query = "SELECT alarm_pause_flag_1, alarm_pause_from_1, alarm_pause_to_1, alarm_pause_flag_2, alarm_pause_from_2, alarm_pause_to_2 from alarm_pause where codUtente = '$codUtente'";
      $result = mysqli_query($conn,$query);
      $row = mysqli_fetch_array($result);
      $alarm_pause_flag_1 = $row[0];
      $alarm_pause_from_1 = $row[1];
      $alarm_pause_to_1 = $row[2];
      $alarm_pause_flag_2 = $row[3];
      $alarm_pause_from_2 = $row[4];
      $alarm_pause_to_2 = $row[5];

      $suspend = 0; // normal alarm, not suspended
      $hour_now = date("H");
      if ($alarm_pause_flag_1 == 1 && $alarm_pause_from_1 < $hour_now && $alarm_pause_to_1 > $hour_now) $suspend=1;
      if ($alarm_pause_flag_2 == 1 && $alarm_pause_from_2 < $hour_now && $alarm_pause_to_2 > $hour_now) $suspend=1;

      if(!$suspend){
        $query = "SELECT telegram_flag, telegram_chatid, pushbullett_flag, pushbullett_addr, email_flag, email_addr, whatsapp_flag, whatsapp_tel,sms_flag,sms_tel from notify_method where codUtente = '$codUtente'";
        $result = mysqli_query($conn,$query);
        $row = mysqli_fetch_array($result);
        $telegram_flag = $row[0];
        $telegram_chatid = $row[1];
        $pushbullett_flag = $row[2];
        $pushbullett_addr = $row[3];
        $email_flag = $row[4];
        $email_addr = $row[5];
        $whatsapp_flag = $row[6];
        $whatsapp_tel = $row[7];
        $sms_flag = $row[8];
        $sms_tel = $row[9];

        $query = "SELECT pushbullett_token,telegram_BOT_ID,sendmessage_key from server_settings";
        $result = mysqli_query($conn,$query);
        $row = mysqli_fetch_array($result);
        $pushbullett_token = $row[0];
        $telegram_BOT_id = $row[1];
        $sendmessage_key = $row[2];

        if($telegram_flag) {
          $website="https://api.telegram.org/".$telegram_BOT_id;
          $params=[ 'chat_id'=>$telegram_chatid, 'text'=>"$subject - $message", ];
          $ch = curl_init($website . '/sendMessage');
          curl_setopt($ch, CURLOPT_HEADER, false);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_POST, 1);
          curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
          curl_exec($ch);
          curl_close($ch);
        }

        if ($pushbullett_flag) {
          // CURL pushbullett (see https://wiki.onion.io/Tutorials/PHP-PushBullet-Example)
          $curl = curl_init('https://api.pushbullet.com/v2/pushes');
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($curl, CURLOPT_POST, true);
          curl_setopt($curl, CURLOPT_HTTPHEADER, ["Authorization: Bearer $pushbullett_token"]);
          curl_setopt($curl, CURLOPT_POSTFIELDS, [
            "type" => "note", "email" => "$pushbullett_addr", "title" => "Allarme $device_name $position",
            "body" => "Temperatura rilevata = $temp - oltre i limiti (min = $min_ok - max = $max_ok)"]
          );
          curl_exec($curl);
          curl_close($curl);
        }

        if ($sms_flag) {
          $website="http://myhooly.hooly.eu/sendmessage.php?channel=sms&key=".$sendmessage_key."&destination=".$sms_tel."&subject=".$subject."&message=".$message;
          $website = str_replace(" ","%20",$website);
          $content = file_get_contents($website);
        }


        if ($email_flag) {
          mail($email_addr, $subject, $message, $headers);
        }

        if ($whatsapp_flag) {

        }
        $query = "UPDATE devices SET alarmed = 1 WHERE serial = '$serial'";
        $result = mysqli_query($conn,$query);
      }
    }
  }
} else {
  // If previously alarmed, reset alarmed flag
  if ($alarmed == 1) {
    $query = "UPDATE devices SET alarmed = 0 WHERE serial = '$serial'";
    $result = mysqli_query($conn,$query);
  }
}

mysqli_close($conn);
?>
