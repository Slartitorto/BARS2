<?php

if(isset($_COOKIE['LOGIN'])) { $COD_UTENTE =	$_COOKIE['LOGIN'];}
else { $COD_UTENTE =	0; header("Location: index.php");}
?>

<head>
  ldsòlkjsdf
  fòsòfdsfsd

  <title>Hooly sensors</title>
  <link href="css/reset.css" rel="stylesheet" type="text/css" />
  <link href="css/stile.css" rel="stylesheet" type="text/css" />
  <link href="css/jquery-ui.css" rel="stylesheet">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="apple-touch-icon" href="/icone/temp_icon.png">
  <meta name="apple-mobile-web-app-status-bar-style" content="default" />
  <link rel="icon" href="/icone/temp_icon.png">
  <SCRIPT type="text/javascript"> function navigator_Go(url) { window.location.assign(url);} </SCRIPT>
  <script src="scripts/jquery.min.js"></script>
  <script src="scripts/jquery-ui.js"></script>

  <script>
  $(document).ready(function(){
    $("#btn1").click(function(){ $("#advanced_preferences").toggle(1000); });
  });
</script>
<script>
$(function() {
  $.datepicker.regional['it'] = {
    closeText: 'Chiudi', // set a close button text
    currentText: 'Oggi', // set today text
    monthNames: ['Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno',   'Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'], // set month names
    monthNamesShort: ['Gen','Feb','Mar','Apr','Mag','Giu','Lug','Ago','Set','Ott','Nov','Dic'], // set short month names
    dayNames: ['Domenica','Luned&#236','Marted&#236','Mercoled&#236','Gioved&#236','Venerd&#236','Sabato'], // set days names
    dayNamesShort: ['Dom','Lun','Mar','Mer','Gio','Ven','Sab'], // set short day names
    dayNamesMin: ['Do','Lu','Ma','Me','Gio','Ve','Sa'], // set more short days names
    dateFormat: 'dd/mm/yy' // set format date
  };
  $.datepicker.setDefaults($.datepicker.regional['it']);
  $("#datepicker").datepicker();
  $("#datepicker").datepicker('setDate', new Date());
});
</script>
</head>

<body>
  <BR>
    <center>
      <TABLE width="800">
        <TR>
          <TD align="left"><A href="javascript:navigator_Go('device_settings.php');"><img src="icone/very-basic-settings-icon.png" width="40"></A></TD>
          <TD align="center"><A href="javascript:navigator_Go('logout.php');"><img src="icone/home_button.png" width="35"></A></TD>
          <TD align="right"><A href="javascript:navigator_Go('index.php');"><img src="icone/refresh57.png" width="30"></A></TD>
        </TR>
      </table>
      <BR> <BR> <BR>
        <table width="500" class="centered">
          <tr><th>Dettagli</th><th>Termometro</th><th>Posizione</th><th>Temp</th><th>Stato</th></tr>

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

            echo "<button type=submit class=imgbtn> <img src=icone/grafico.png height=\"30\" width=\"30\"></button>";
            echo "<input type=hidden name=serial value=$serial[$i] > ";
            echo "<input type=hidden name=last value=1 > ";
            echo "<input type=hidden name=graph value=temp > ";

            echo "</form>";
            echo "</TD><TD>" . $device_name[$i] . "</TD>";
            echo "</TD><TD>" . $position[$i] . "</TD>";
            echo "<TD>" . round($last_temp[$i],1) . "</TD>";
            echo "<TD><img src=\"icone/" . $warn[$i] . "_signal.png\" width=\"25\"></TD>";
            echo "</TR>\n";
          }
          echo "</TABLE> ";

          $conn->close();
          ?>

          <br><br>
          <button id="btn1" class="graybtn">Click per aprire o chiudere la gestione Ristorante</button>
          <br><br>
          <div id="advanced_preferences" style="display:none;">
            <div class="modal-content" style="margin:1% auto 1% auto;"> <br> <center>
              <h3> Inserisci non conformità</h3>
              <br>
              <form action="hooly_db_actions.php" method="post">
                <input type="hidden" name="act" value="nc_record">
                <input type="hidden" name="cod_utente" value="<?php echo $COD_UTENTE; ?>">
                Data: <input type="text" class="slim" id="datepicker" name="nc_date">
                <br>
                Seleziona dispositivo: <select name="serial">
                  <?php for ($i=0;$i<$count;$i++) {
                    echo "<option value= \"$serial[$i]\" > $device_name[$i]  $position[$i] </option>\n";
                  }
                  ?>
                </select>
                <br>
                Seleziona Non Conformità: <select name="nc_type">
                  <option value="A">A</option>
                  <option value="B">B</option>
                </select>
                <br>
                Seleziona Azione Correttiva<select name="nc_ac">
                  <option value="C">C</option>
                  <option value="D">D</option>
                  <option value="E">E</option>
                </select>
                <br>
                <br>
                <br>
                <div style=font-size:12px;text-align:left;margin:1% auto 1% auto;padding:30px;>
                  <b>Legenda Non Conformità</b>
                  <br>
                  A. Temperatura dell’apparecchio fuori limite ma temperatura degli alimenti entro i limiti
                  <br>
                  B. Temperatura dell’apparecchio e degli alimenti fuori limite
                  <br>
                  <br>
                  <b>Legenda Azioni correttive</b>
                  <br>
                  C. Trasferimento degli alimenti in altro apparecchio di riserva e riparazione dell’impianto
                  <br>
                  D. Eliminazione degli alimenti con temperatura superiore ai limiti e riparazione dell’impianto
                  <br>
                  E. Immediato impiego dei prodotti e riparazione dell’impianto
                  <br>
                </div>

                <button type="submit" class="greenbtn">Registra</button>
              </form>
            </div>

            <div class="modal-content" style="margin:1% auto 1% auto;"> <br> <center>
              <br> <center>
                <h3> Mostra e gestisci le Non Conformità registrate</h3>
                <br>
                <form action="gestione_nc.php" method="post">

                  <select name="mese">
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

                  <select name="anno">
                    <option value="2017">2017</option>
                    <option value="2018" selected>2018</option>
                    <option value="2019">2019</option>
                  </select>

                  <input type="hidden" name="cod_utente" value="<?php echo $COD_UTENTE; ?>">
                  <button type="submit" class="greenbtn">Seleziona</button>
                </form>
              </div>

              <div class="modal-content" style="margin:1% auto 1% auto;"> <br> <center>
                <br> <center>
                  <h3> Genera report mensili </h3>
                  <br>
                  <form action="hooly_report.php" method="post">

                    <select name="ora">
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

                    <select name="mese">
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

                    <select name="anno">
                      <option value="2017">2017</option>
                      <option value="2018" selected>2018</option>
                      <option value="2019">2019</option>
                    </select>

                    <button type="submit" class="greenbtn">Report SA-04</button>
                  </form>
                </div>


                <div class="modal-content" style="margin:1% auto 1% auto;"> <br> <center>
                  <br> <center>
                    <h3>Non inviare allarmi</h3>
                    <br>
                    <form action="standby_alarm.php" method="post">
                      Dalle ore:
                      <select name="standby_alarm_from">
                        <option value="01">01:00</option>
                        <option value="02">02:00</option>
                        <option value="03">03:00</option>
                        <option value="04">04:00</option>
                        <option value="05">05:00</option>
                        <option value="06">06:00</option>
                        <option value="07">07:00</option>
                        <option value="08">08:00</option>
                        <option value="09">09:00</option>
                        <option value="10">10:00</option>
                        <option value="11">11:00</option>
                        <option value="12">12:00</option>
                        <option value="13">13:00</option>
                        <option value="14">14:00</option>
                        <option value="15">15:00</option>
                        <option value="16">16:00</option>
                        <option value="17">17:00</option>
                        <option value="18">18:00</option>
                        <option value="19">19:00</option>
                        <option value="20">20:00</option>
                        <option value="21">21:00</option>
                        <option value="22">22:00</option>
                      </select>
                      <br>
                      Alle ore:
                      <select name="standby_alarm_to">
                        <option value="01">01:00</option>
                        <option value="02">02:00</option>
                        <option value="03">03:00</option>
                        <option value="04">04:00</option>
                        <option value="05">05:00</option>
                        <option value="06">06:00</option>
                        <option value="07">07:00</option>
                        <option value="08">08:00</option>
                        <option value="09">09:00</option>
                        <option value="10">10:00</option>
                        <option value="11">11:00</option>
                        <option value="12">12:00</option>
                        <option value="13">13:00</option>
                        <option value="14">14:00</option>
                        <option value="15">15:00</option>
                        <option value="16">16:00</option>
                        <option value="17">17:00</option>
                        <option value="18">18:00</option>
                        <option value="19">19:00</option>
                        <option value="20">20:00</option>
                        <option value="21">21:00</option>
                        <option value="22">22:00</option>
                      </select>
                      <br>

                      <button type="submit" class="greenbtn">Imposta</button>
                    </form>
                  </div>
                </div>

              </body>
