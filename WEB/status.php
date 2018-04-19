<?php

if(isset($_COOKIE['LOGIN'])) { $COD_UTENTE =	$_COOKIE['LOGIN'];}
else { $COD_UTENTE =	0; header("Location: index.php");}
?>

<head>
  <title>Hooly sensors</title>
  <link href="stile.css" rel="stylesheet" type="text/css" />
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="apple-touch-icon" href="/icone/temp_icon.png">
  <meta name="apple-mobile-web-app-status-bar-style" content="default" />
  <link rel="icon" href="/icone/temp_icon.png">
  <SCRIPT type="text/javascript">
  function navigator_Go(url) { window.location.assign(url);}
  </SCRIPT>
</head>

<body>
  <CENTER>
    <table width="300">
      <TR>
        <TD width="100" align="center"><A href="javascript:navigator_Go('device_settings.php');"><img src="icone/very-basic-settings-icon.png" width="40"></A></TD>
        <TD width="100" align="center"><A href="javascript:navigator_Go('home.php');"><img src="icone/home_button.png" width="35"></A></TD>
        <TD width="100" align="center"><A href="javascript:navigator_Go('index.php');"><img src="icone/refresh57.png" width="30"></A></TD>
      </TR>
    </table>
    <BR>
      <table width="300" class="gridtable">
        <tr><th>Termometro</th><th>Posizione</th><th>Temp</th><th>Stato</th></tr>

        <?php
        include "db_connection.php";

        $query = "SELECT idUtente,t0,t1,t2,t3 FROM utenti WHERE codUtente='$COD_UTENTE'";
        $result = $conn->query($query);
        while($row = $result->fetch_assoc()) {
          $idUtente = $row["idUtente"];
          $tenant0 = $row["t0"];
          $tenant1 = $row["t1"];
          $tenant2 = $row["t2"];
          $tenant3 = $row["t3"];
        }

        $query = "SELECT serial, device_name, position, batt_type, min_ok, max_ok FROM devices where tenant in ($tenant0,$tenant1,$tenant2,$tenant3)";
        $result = $conn->query($query);
        $x=0;
        while($row = $result->fetch_assoc()) {
          $serial[$x]=$row["serial"];
          $device_name[$x]=$row["device_name"];
          $position[$x]=$row["position"];
          $batt_type[$x]=$row["batt_type"];
          $min_ok[$x]=$row["min_ok"];
          $max_ok[$x]=$row["max_ok"];
          ++$x;
        }

        $count=count($serial);
        for($i=0;$i<$count;$i++) {
          $query = "select temp,hum,counter,battery,period,timestampdiff(second,timestamp,now()) as sec_delay from last_rec_data where serial = '$serial[$i]' order by timestamp desc limit 1";
          $result = $conn->query($query);
          while($row = $result->fetch_assoc()) {
            $last_temp[$i]=$row["temp"];
            $last_hum[$i]=$row["hum"];
            $sec_delay[$i]=$row["sec_delay"];
            $battery[$i]=$row["battery"];
            $period[$i]=$row["period"];
            $link_qlt0[$i]=$row["counter"];
          }

          if (($batt_type[$i] == "litio" and $battery[$i] < 3.5) or ($batt_type[$i] == "nimh" and $battery[$i] < 3.2)) {
            $warn[$i] = "battery_low";
          }
          else if ($sec_delay[$i] > 5 * $period[$i]) {
            $warn[$i] = "link";
          }
          else if ($last_temp[$i] < $min_ok[$i] or $last_temp[$i] > $max_ok[$i]) {
            $warn[$i] = "red";
          }
          else {
            $warn[$i] = "green";
          }

          echo "<TR>";
          echo "<form action=device_details.php method=post>";
          echo "<TD>";

          //echo "<A HREF=\"javascript:navigator_Go('device_details.php?serial=";
          //echo  $serial[$i] . "&last=2&graph=temp');\">" . $device_name[$i]. "</A>";
          echo "<input type=submit value=$device_name[$i] > ";
          echo "<input type=hidden name=serial value=$serial[$i] > ";
          echo "<input type=hidden name=last value=1 > ";
          echo "<input type=hidden name=graph value=temp > ";

          echo "</form>";
          echo "</TD><TD>" . $position[$i] . "</TD>";
          echo "<TD>" . round($last_temp[$i],1) . "</TD>";
          echo "<TD><img src=\"icone/" . $warn[$i] . "_signal.png\" width=\"25\"></TD>";
          echo "</TR>\n";
        }
        echo "</TABLE> ";

        $conn->close();
        ?>

        <hr>
        <br>
        <center>
          <h3>
            Genera report mensili
          </h3>
          <br>
          <form action=hooly_report.php method=post>

            <select name='ora'>
              <option value="00">00:00</option>
              <option value="02">02:00</option>
              <option value="04">04:00</option>
              <option value="06">06:00</option>
              <option value="08" selected>08:00</option>
              <option value="10">10:00</option>
              <option value="12">12:00</option>
              <option value="14">14:00</option>
              <option value="16">16:00</option>
              <option value="18">18:00</option>
              <option value="20">20:00</option>
              <option value="22">22:00</option>
            </select>

            <select name='mese'>
              <option value="01">Gennaio</option>
              <option value="02">Febbraio</option>
              <option value="03">Marzo</option>
              <option value="04">Aprile</option>
              <option value="05">Maggio</option>
              <option value="06">Giugno</option>
              <option value="07">Luglio</option>
              <option value="08">Agosto</option>
              <option value="09">Settembre</option>
              <option value="10">Ottobre</option>
              <option value="11">Novembre</option>
              <option value="12">Dicembre</option>
            </select>

            <select name='anno'>
              <option value="2017">2017</option>
              <option value="2018" selected>2018</option>
              <option value="2019">2019</option>
            </select>

            <input type="submit" value="Report">
          </form>
        </body>
