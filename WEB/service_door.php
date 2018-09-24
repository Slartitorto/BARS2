<?php
include "dbactions/db_connection.php";

// -----------

if(isset($_GET['service']) && ($_GET['service'] == "get_code_period") && isset($_GET['serial']) )
{
  $serial=$_GET['serial'];
  $query = "SELECT code_period FROM devices WHERE serial = '$serial' ";
  $result = $conn->query($query);
  while($row = $result->fetch_assoc()) {
    $code_period = $row["code_period"];
  }
  echo $code_period;
  $conn->close();
}


// -----------
// Da provare
// le prime due query dovrebbero servire solo ad aggiornare il timestamp, ma forse è sufficiente la terza (update)

if(isset($_GET['service']) && ($_GET['service'] == "keep_alive") && isset($_GET['router']))
{
  $router=$_GET['router'];
  $result = $conn->query("DELETE FROM keep_alive_check WHERE router = '$router'");
  $result = $conn->query("INSERT INTO keep_alive_check (router) VALUES ('$router')");
  $result = $conn->query("UPDATE keep_alive_check SET alarmed = '0' WHERE router = '$router'");
  $conn->close();
}

// -----------


if(isset($_GET['service']) && ($_GET['service'] == "get_new_router") )
{
  $query = "SELECT router_name,router_key,pin FROM new_routers ORDER BY router_name LIMIT 1";
  $result = $conn->query($query);

  while($row = $result->fetch_assoc()) {
    $router_name = $row["router_name"];
    $current_key = $row["router_key"];
    $current_pin = $row["pin"];

  }

  echo $router_name . ":" . $current_key;

  $query = "DELETE FROM new_routers WHERE router_name = '$router_name'";
  $result = $conn->query($query);

  $query = "INSERT INTO router (router,current_key,pin) VALUES ('$router_name','$current_key','$current_pin')";
  $result = $conn->query($query);
  $conn->close();
}

// -----------

if(isset($_GET['service']) && ($_GET['service'] == "get_nodeID"))
{
  $query = "SELECT max(serial)+1 as new_serial FROM new_devices";
  $result = $conn->query($query);
  while($row = $result->fetch_assoc()) {
    $new_serial = $row["new_serial"];
  }
  $new_serial = sprintf("%04d",$new_serial);
  $random_pin = sprintf("%04d",rand(0,9999));
  $query = "INSERT INTO new_devices VALUES('$new_serial','$random_pin')";
  $result = $conn->query($query);
  echo $new_serial;
  $conn->close();
}

// -----------

if(isset($_GET['service']) && ($_GET['service'] == "node_shutdown") && isset($_GET['serial']))
{
  $serial=$_GET['serial'];
  $query = "UPDATE devices SET batt_alarmed = 1 WHERE serial = '$serial' ";
  $result = $conn->query($query);

  $query = "SELECT device_name, position, tenant FROM devices WHERE serial = '$serial'";
  $result = $conn->query($query);
  $row = $result->fetch_assoc();
  $device_name = $row["device_name"];
  $position = $row["position"];
  $tenant = $row["tenant"];

  $subject = "Allarme $device_name $position";
  $message = "batteria scarica - sensore spento. Ricarica quanto prima !";
  $headers = "From: root@slartitorto.eu \r\n Reply-To: root@slartitorto.eu \r\n";

  $query = "SELECT email FROM utenti WHERE t0 = '$tenant' OR t1 = '$tenant' OR t2 = '$tenant' OR t3 = '$tenant'";
  $result = $conn->query($query);

  while($row = $result->fetch_assoc()) {
    $to = $row["email"];
    mail($to, $subject, $message, $headers);

    // CURL pushbullett (see https://wiki.onion.io/Tutorials/PHP-PushBullet-Example)
    $authToken = "YOUR_PUSHBULLETT_TOKEN";
    $curl = curl_init('https://api.pushbullet.com/v2/pushes');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ["Authorization: Bearer $authToken"]);
    curl_setopt($curl, CURLOPT_POSTFIELDS, [
      "type" => "note", "email" => "$to", "title" => "$subject", "body" => "$message"]
    );
    curl_exec($curl);
    curl_close($curl);
  }
  $conn->close();
}



?>
