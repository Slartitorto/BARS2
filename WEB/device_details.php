<?php
include "db_connection.php";
if(isset($_COOKIE['LOGIN'])) { $COD_UTENTE = $_COOKIE['LOGIN']; }
else { $COD_UTENTE =	0; header("Location: index.php"); }
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link href='https://fonts.googleapis.com/css?family=Source Sans Pro' rel='stylesheet'>

  <?php

  $serial=($_POST["serial"]);
  $last=($_POST["last"]);
  $graph=$_POST['graph'];

  $query = "SELECT idUtente,t0,t1,t2,t3 FROM utenti WHERE codUtente='$COD_UTENTE'";
  $result = $conn->query($query);
  while($row = $result->fetch_assoc()) {
    $idUtente = $row["idUtente"];
    $tenant0 = $row["t0"];
    $tenant1 = $row["t1"];
    $tenant2 = $row["t2"];
    $tenant3 = $row["t3"];
  }

  $query = "SELECT serial, device_name, position FROM devices where tenant in ($tenant0,$tenant1,$tenant2,$tenant3)";

  $result = $conn->query($query);
  $serial_qty=0;
  while($row = $result->fetch_assoc()) {
    $serial_array[$serial_qty]=$row["serial"];
    $device_name_array[$serial_qty]=$row["device_name"];
    $position_array[$serial_qty]=$row["position"];
    ++$serial_qty;
  }

  // SELECT for data to graph

  if ($graph == "temp") {
    $query = "SELECT min_ok, max_ok FROM devices where serial = '$serial'";
    $result = $conn->query($query);
    while($row = $result->fetch_assoc()) {
      $min_ok=$row["min_ok"];
      $max_ok=$row["max_ok"];
    }

    $sqla = "SELECT unix_timestamp(timestamp) as timestamp, temp FROM rec_data where serial = '$serial' and timestamp > now()- interval '$last'  day order by timestamp";
    $sql_csv = "SELECT timestamp, counter,  temp FROM rec_data where serial = '$serial' and timestamp > now()- interval '$last'  day order by timestamp";

    $result = $conn->query($sqla);
    while ($row = $result->fetch_array()) {
      $timestamp = $row['timestamp'];
      $timestamp *=1000;
      $data = $row['temp'];

      $data1[] = "[$timestamp, $data]";
      $data2[] = "[$timestamp, $min_ok]";
      $data3[] = "[$timestamp, $max_ok]";
    }
  } else {
    $sqla = "SELECT unix_timestamp(timestamp) as timestamp, battery FROM rec_data where serial = '$serial' and timestamp > now()- interval '$last'  day order by timestamp";
    $sql_csv = "SELECT timestamp, battery FROM rec_data where serial = '$serial' and timestamp > now()- interval '$last'  day order by timestamp";
    $result = $conn->query($sqla);
    while ($row = $result->fetch_array()) {
      $timestamp = $row['timestamp'];
      $timestamp *=1000;
      $battery = $row['battery'];

      $data4[] = "[$timestamp, $battery]";
    }

  }

  print  "<title>Sensor details</title>
  <meta name=\"apple-mobile-web-app-capable\" content=\"yes\">
  <link rel=\"apple-touch-icon\" href=\"/icone/app_icon128.png\">
  <link href=\"stile.css\" rel=\"stylesheet\" type=\"text/css\" />
  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />
  <script type=\"text/javascript\">
  function navigator_Go(url) {
    window.location.assign(url);
  }
  </script>
  <script src=\"scripts/jquery.min.js\"></script>
  <script src=\"scripts/highcharts.js\"></script>
  <script>
  $(function () {
    Highcharts.setOptions({
      global: {
        useUTC: false
      }
    });
    $('#container1').highcharts({
      chart: {
        type: 'line'
      },
      title: {
        text: ''
      },
      legend: {
        enabled: false
      },
      xAxis: {
        type: 'datetime',
      },
      yAxis: {
        title: {
          text: 'Temperature (C)'
        },
      },
      series: [{
        data: [";
        if ($graph == "temp"){
          echo join($data1, ',') ;
          print			"]
        },{
          color:'#ff0000',
          enableMouseTracking: false,
          data: [";
          echo join($data2, ',') ;
          print                   "]
        },{
          color:'#ff0000',
          enableMouseTracking: false,
          data: [";
          echo join($data3, ',') ;
        } else {

          echo join($data4, ',') ;
        }
        print                   "]
      } ]
    });
  });
  </script>
  </head>
  <body>

  <BR>
  <TABLE width=\"100%\"><TR>
  <TD align=\"left\" width=\"90%\">
  <A href=\"javascript:navigator_Go('index.php');\"><img src=\"icone/left37.png\" width=\"35\"></A></TD>
  <TD align=\"right\">
  <A href=\"javascript:navigator_Go('device_details.php?serial=$serial&last=$last&graph=temp');\"><img src=\"icone/refresh57.png\" width=\"30\">
  </TD>
  </TR></TABLE>
  <BR><CENTER>
  ";
  function format_time($t,$f=':') // t = seconds, f = separator
  {
    return sprintf("%3d%s%02d", ($t/60) , $f, $t%60);
  }
  $sql = "SELECT device_name, position, batt_type FROM devices where serial = '$serial'" ;
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {

      $device_name = $row["device_name"];
      $position = $row["position"];
      $batt_type = $row["batt_type"];

    }
  }

  // SELECT last record
  $sql = "SELECT timestamp, temp, hum, battery, period, rssi, timestampdiff(second,timestamp,now()) as sec_delay FROM last_rec_data where serial = '$serial' order by timestamp desc limit 1";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {

      $time_stamp = $row["timestamp"];
      $temp = $row["temp"];
      $hum = $row["hum"];
      $batt = $row["battery"];
      $period = $row["period"];
      $rssi = $row["rssi"];
      $min_period = format_time($period);
      $sec_delay=$row["sec_delay"];
      $min_delay=format_time($sec_delay);

    }

    // SELECT last counter
    $query = "select counter from last_rec_data where serial = '$serial' order by timestamp desc limit 1";
    $result = $conn->query($query);
    while($row = $result->fetch_assoc()) {
      $link_qlt0=$row["counter"];
    }
    // SELECT last counter -10
    $query = "select counter from rec_data where serial = '$serial' order by timestamp desc limit 10,1";
    $result = $conn->query($query);
    while($row = $result->fetch_assoc()) {
      $link_qlt1=$row["counter"];
    }
    $link_qlt = intval(1000/($link_qlt0 - $link_qlt1));

    echo " <table class=\"padded centered\">";
    echo " <tr><th>" . $device_name . "</th><th>" . $position . "</th></tr><tr> <th colspan = 2>Temp: " . round($temp,2) . "&deg C</th></tr>";
    if ($graph == "temp" ){
      echo " <TR><TD>Serial: <B> " . $serial . "</B></TD><TD><A HREF=\"javascript:navigator_Go('device_details.php?serial=$serial&last=$last&graph=battery');\">Batteria:</a>  <B>" . $batt . "% </B> </TD></TR>";
    } else {
      echo " <TR><TD>Serial: <B> " . $serial . "</B></TD><TD>Batteria: <B>" . $batt . "</B> (" . $perc_batt . "%) - " . $batt_type . "</TD></TR>";
    }
    echo " <TR><TD>Periodo di rilevazione (min.)<B>" . $min_period . "</B><TD>Ultimo aggiornamento: <B>" . $min_delay . "</B></TD></TR>";
    echo " <TR><TD colspan=2>Link quality: <B>" . $link_qlt . "%</B>  (RSSI = <B>" . $rssi . ")</B></TD></TR>";
    echo "</table><br><br>";
    echo "<table width=100%>";
    echo "<tr>";
    echo "<td width=33%></td>";
    echo "<td width=34% align=center>";
    if ($graph == "temp") {

      echo "<form action =\"" . $_SERVER['PHP_SELF'] . "\" method=\"POST\">";
      echo "<select name=\"last\" onchange=\"this.form.submit()\">\n";
      echo "<option value= \"1\"";
      if ($last == 1) { echo " selected";}
      echo "> Ultime 24 ore</option>\n";

      echo "<option value= \"2\"";
      if ($last == 2) { echo " selected";}
      echo "> Ultime 48 ore</option>\n";

      echo "<option value= \"7\"";
      if ($last == 7) { echo " selected";}
      echo "> Ultima settimana</option>\n";

      echo "<option value= \"30\"";
      if ($last == 30) { echo " selected";}
      echo "> Ultimo mese</option>\n";

      echo "<input type=hidden name=serial value=" . $serial . ">";
      echo "<input type=hidden name=graph value=" . $graph . ">";
      echo "</form>";

      echo "</td>";

    } else {
      echo "<A href=\"javascript:navigator_Go('device_details.php?serial=$serial&last=$next_last&graph=battery');\">" . $string_last . "</a>";

    }
    echo "<td width=33% align=right>";
    echo "<form action =\"" . $_SERVER['PHP_SELF'] . "\" method=\"POST\">";
    echo "Passa a:  ";
    echo "<select name=\"serial\" onchange=\"this.form.submit()\">\n";
    for($i=0;$i<$serial_qty;$i++) {
      echo "<option value= \"$serial_array[$i]\"";
      if ($serial_array[$i] == $serial) { echo " selected";}
      echo ">$device_name_array[$i] - $position_array[$i]</option>\n";
    }
    echo "</select>";
    echo "<input type=hidden name=last value=" . $last . ">";
    echo "<input type=hidden name=graph value=" . $graph . ">";
    echo "</form>";




    echo "</td>";
    echo "</tr>";
    echo "</table>";

    print "<div id=\"container1\" style=\"width:100%; height:400px;\"></div>";
    ?>

    <script>
    var data = [
      <?php
      // https://code-maven.com/create-and-download-csv-with-javascript

      $result = $conn->query($sql_csv);
      while ($row = $result->fetch_array()) {
        echo "['" . $row[0] . "','" . $row[1] . "','" . $row[2] . "'],";
      }
      ?>
    ];

    function download_csv() {
      var csv = 'Timestamp,Count,Data\n';
      data.forEach(function(row) {
        csv += row.join(',');
        csv += "\n";
      });

      console.log(csv);
      var hiddenElement = document.createElement('a');
      hiddenElement.href = 'data:text/csv;charset=utf-8,' + encodeURI(csv);
      hiddenElement.target = '_blank';
      hiddenElement.download = 'data.csv';
      hiddenElement.click();
    }
    </script>

    <button class=graybtn onclick="download_csv()">Download CSV</button>
    <button class=graybtn onClick="window.print()">Print</button>

    <?php



  } else {
    echo "0 results";
  }

  $conn->close();
  ?>
</body>
</html>
