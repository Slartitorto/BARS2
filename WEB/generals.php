<?php
if(isset($_COOKIE['LOGIN'])) { $COD_UTENTE =	$_COOKIE['LOGIN'];}
else { $COD_UTENTE = 0; header("Location: index.php"); }
include "db_connection.php";
?>

<head>

  <title>Hooly sensors</title>
  <link href="css/reset.css" rel="stylesheet" type="text/css" />
  <link href="css/dropDownMenu.css" rel="stylesheet" type="text/css" />
  <link href="css/stile.css" rel="stylesheet" type="text/css" />
  <link href="css/jquery-ui.css" rel="stylesheet">
  <link rel="apple-touch-icon" href="/icone/temp_icon.png">
  <link rel="icon" href="/icone/temp_icon.png">

  <script src="scripts/jquery.min.js"></script>
  <script src="scripts/jquery-ui.js"></script>
  <script src="scripts/datePickerLocalized.js"></script>
  <script src="scripts/utils.js"></script>

  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="apple-mobile-web-app-status-bar-style" content="default" />

</head>

<body>
  <?php include 'includes/headerTableMenu.php'; ?>
  <BR>
    <?php
    if(@$_GET["act"] == "NC_insert") {// ----------- Inserimento non conformità   ?>
      <?php
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
      ?>

      <div class="modal-content">
        <br><br>
        <center>
          <h3> Inserisci non conformità</h3>
          <br>
          <form onsubmit="preventMultiSubmit()" action="hooly_db_actions.php" method="post">
            <input type="hidden" name="act" value="nc_record">
            <input type="hidden" name="cod_utente" value="<?php echo $COD_UTENTE; ?>">
            Data: <input type="text" class="slim" id="datepicker" name="nc_date" maxlength="10" value="<?php echo date("d/m/Y"); ?>" required>
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
              <?php include("includes/legenda_nc.php"); ?>
            </div>
            <button id="mybutton" type="submit" class="greenbtn">Registra</button>
          </form>
        </div>


      <?php } else if(@$_GET["act"] == "RM_insert") { // ---------- Inserisci Registrazone Manuale


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
        ?>

        <div class="modal-content">
          <br><br>
          <center>
            <h3> Inserisci una registrazione manuale</h3>
            <br>
            <form onsubmit="preventMultiSubmit()" action="hooly_db_actions.php" method="post">
              <input type="hidden" name="act" value="rm_record">
              <input type="hidden" name="cod_utente" value="<?php echo $COD_UTENTE; ?>">

              <br>
              Seleziona dispositivo: <select name="serial">
                <?php for ($i=0;$i<$count;$i++) {
                  echo "<option value= \"$serial[$i]\" > $device_name[$i]  $position[$i] </option>\n";
                }
                ?>
              </select>
              <br>
              Data: <input type="text" class="slim" id="datepicker" name="nc_date" maxlength="10" value="<?php echo date("d/m/Y"); ?>" required>
              <br>
              Seleziona la fascia oraria:
              <select name="item">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
              </select>
              <br>
              Inserisci ore e minuti:
              <input type="text" name="ora" class="slim" size="2" maxlength="2">:
              <input type="text" name="minuto" class="slim" size="2" maxlength="2"><br><br><br>
              Inserisci la temperatura rilevata manualmente:<br><input type="text" name="temp" size="6" maxlength="6">
              <br>
              <br>
              <button id="mybutton" type="submit" class="greenbtn">Registra</button>
              <div style=font-size:12px;text-align:left;margin:1% auto 1% auto;padding:30px;>
                <b>NB: </b> Le rilevazioni manuali saranno segnalate con il carattere "M"
              </div>
            </form>
          </div>


        <?php } else if(@$_POST["act"] == "NC_modify") { // ---------- Modifica NC   ?>


          <?php
          $mese=$_POST['mese'];
          $anno=$_POST['anno'];

          $nc_id = $_POST['nc_id'];
          $query = "SELECT * FROM non_conformita where nc_id = '$nc_id'";
          $result = $conn->query($query);
          while($row = $result->fetch_assoc()) {
            $nc_date=$row["nc_date"];
            $nc_type=$row["nc_type"];
            $nc_ac=$row["nc_ac"];
            $serial_found=$row["serial"];
          }

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
          $x=0;
          while($row = $result->fetch_assoc()) {
            $serial[$x]=$row["serial"];
            $device_name[$x]=$row["device_name"];
            $position[$x]=$row["position"];
            ++$x;
          }
          $count=count($serial);

          ?>
          <div class="modal-content"> <br> <center>
            <h3> Modifica non conformità</h3>
            <br>
            <form action="hooly_db_actions.php" method="post">
              <input type="hidden" name="mese" value="<?php echo $mese; ?>">
              <input type="hidden" name="anno" value="<?php echo $anno; ?>">
              <input type="hidden" name="act" value="nc_modify">
              <input type="hidden" name="nc_id" value="<?php echo $nc_id; ?>">
              Data: <input type="text" class="slim" name="nc_date" id="datepicker" value="<?php echo $nc_date; ?>" maxlength="10" required>
              <br>
              Seleziona dispositivo: <select name="serial">
                <?php for ($i=0;$i<$count;$i++) {
                  echo "<option value= \"$serial[$i]\"";
                  if ($serial[$i] == $serial_found) { echo "selected"; }
                  echo "> $device_name[$i]  $position[$i] </option>\n";
                } ?>
              </select>
              <br>
              Seleziona Non Conformità: <select name="nc_type">
                <option value="A" <?php if ($nc_type == "A") {echo "selected"; } ?> >A</option>
                <option value="B" <?php if ($nc_type == "B") {echo "selected"; } ?> >B</option>
              </select>
              <br>
              Seleziona Azione Correttiva<select name="nc_ac">
                <option value="C" <?php if ($nc_ac == "C") {echo "selected"; } ?> >C</option>
                <option value="D" <?php if ($nc_ac == "D") {echo "selected"; } ?> >D</option>
                <option value="E" <?php if ($nc_ac == "E") {echo "selected"; } ?> >E</option>
              </select>
              <br>
              <br>
              <br>
              <div style=font-size:12px;text-align:left;margin:1% auto 1% auto;padding:30px;>
                <?php include("includes/legenda_nc.php"); ?>
              </div>

              <button type="submit" class="greenbtn">Registra</button>
            </form>
          </div>


        <?php } else if(@$_GET["act"] == "add_hooly_response") { // ---------- add Hooly: verify and record  ?>


          <div class="modal-content">
            <?php
            $idUtente = $_POST['idUtente'];
            $tenant = $_POST['tenant'];
            $serial = $_POST['serial'];
            $pin = $_POST['pin'];

            $query = "SELECT pin FROM new_devices WHERE serial='$serial'";
            $result = $conn->query($query);
            if ($result->num_rows > 0) {
              while($row = $result->fetch_assoc()) {
                $pin_trovato = $row["pin"];
              }
              if ($pin == $pin_trovato) {

                $query = "delete from new_devices WHERE serial='$serial'";
                $result = $conn->query($query);
                $query = "insert into devices (serial,device_name,position,armed,batt_alarmed,alarmed,min_ok,max_ok,batt_type,tenant,code_period) values ('$serial','Hooly_$serial','posizione',0,0,0,10,30,'litio','$tenant',6) ";
                $result = $conn->query($query); ?>
                Operazione effettuata con successo !! <br><br>
                <center>
                  <button type="button" onclick="location.href='generals.php?act=add_hooly';" class="greenbtn">Inserisci un altro Hooly</button>
                  <br>
                  oppure
                  <br>
                  <button type="button" onclick="location.href='device_settings.php';" class="greenbtn">Configura i tuoi Hooly</button>
                  <?php
                }  else   { ?>
                  Ops, c'e' stato un errore:<br><br>
                  Pin errato<br><br><br>
                  <button type="button" onclick="location.href='generals.php?act=add_hooly';" class="greenbtn">Torna indietro</button>
                <?php }
              }   else   { ?>
                Ops, c'e' stato un errore:<br><br>
                Il seriale non esiste<br><br><br>
                <button type="button" onclick="location.href='generals.php?act=add_hooly';" class="greenbtn">Torna indietro</button>
              <?php } ?>
            </center>

          </div>


        <?php } else if(@$_GET["act"] == "add_hooly") { // ---------- add Hooly: inserisci serial e pin  ?>


          <?php
          $query = "SELECT idUtente,t0 FROM utenti WHERE codUtente='$COD_UTENTE'";
          $result = $conn->query($query);
          while($row = $result->fetch_assoc()) {
            $idUtente = $row["idUtente"];
            $tenant0 = $row["t0"];
          }
          ?>

          <div class="modal-content"> <br> <center>
            <form action="generals.php?act=add_hooly_response" method="post">
              <br>
              Seriale: <input type="text" class="slim" name="serial" size="6" maxlength="4" pattern="[A-Z0-9]{4,4}" title= "Il codice seriale consiste di 4 caratteri (numeri o lettere maiuscole) ed è indicato sulla confezione del tuo Hooly" required>
              Pin: <input type="text" class="slim" name="pin" size="6" maxlength="4" pattern="[0-9]{4,4}" title="Il pin consiste di 4 caratteri (solo numeri) ed è indicato sulla confezione del tuo Hooly" required>
              <input type="hidden" name="idUtente" value="<?php echo $idUtente; ?>">
              <input type="hidden" name="tenant" value="<?php echo $tenant0; ?>">
              <br>
              <button type="submit" class=greenbtn>Aggiungi un sensore</button>
            </form>
          </div>


        <?php } else if(@$_GET["act"] == "NC_manage_select") { // ---------- Gestione NC: seleziona periodo -  ?>


          <div class="modal-content">
            <br><br>
            <center>
              <h3> Mostra e gestisci le Non Conformità registrate</h3>
              <br>
              <form action="generals.php?act=NC_manage" method="post">
                <?php include 'includes/select_mese_anno.php'; ?>
                <input type="hidden" name="cod_utente" value="<?php echo $COD_UTENTE; ?>">
                <br>
                <button type="submit" class="greenbtn">Seleziona</button>
              </form>
            </center>
          </div>


        <?php } else if(@$_GET["act"] == "NC_manage") { // ---------- Mostra e gestisce NC -  ?>


          <?php
          if(isset($_POST["mese"])) { $mese=($_POST["mese"]);}
          if(isset($_POST["anno"])) { $anno=($_POST["anno"]);}
          if(isset($_GET["mese"])) { $mese=($_GET["mese"]);}
          if(isset($_GET["anno"])) { $anno=($_GET["anno"]);}
          ?>

          <div class="modal-content NC_manage">
            <br><br>
            <center>
              Non conformità trovate per il mese: <?php echo $mese; ?> - anno: <?php echo $anno; ?>
              <br> <br>
              <table class="centered NC_manage">
                <tr><th>Elimina</th><th>Modifica</th><th>Data</th><th>Impianto</th><th>Posizione</th><th>Non conformità</th><th>Azione correttiva</th></tr>

                <?php
                $query = "SELECT nc_id, nc_date, nc_type, nc_ac, serial, device_name, position FROM non_conformita where codUtente = '$COD_UTENTE' and nc_date like '%$mese/$anno' order by nc_date";
                $result = $conn->query($query);
                if(($result->num_rows) == 0) { ?> </table> nessun record trovato <?php }
                else {
                  $x=0;
                  while($row = $result->fetch_assoc()) {
                    $nc_id[$x]=$row["nc_id"];
                    $nc_date[$x]=$row["nc_date"];
                    $nc_type[$x]=$row["nc_type"];
                    $nc_ac[$x]=$row["nc_ac"];
                    $device_name[$x]=$row["device_name"];
                    $position[$x]=$row["position"];
                    ++$x;
                  }

                  for($i=0;$i<$x;$i++) {

                    ?>
                    <TR>
                      <TD>
                        <form action="hooly_db_actions.php" onsubmit="return checkConfirm()" method="post">
                          <input type="hidden" name="act" value="nc_delete">
                          <input type="hidden" name="nc_id" value=<?php echo $nc_id[$i] ?> >
                          <input type="hidden" name="mese" value=<?php echo $mese ?> >
                          <input type="hidden" name="anno" value=<?php echo $anno ?> >
                          <button type="submit" class="imgbtn"> <img src="icone/trash.png" height="30" width="30"></button>
                        </form>
                      </td> <TD>
                        <form action="generals.php" method="post">
                          <input type="hidden" name="act" value="NC_modify">
                          <input type="hidden" name="nc_id" value=<?php echo $nc_id[$i] ?> >
                          <input type="hidden" name="mese" value=<?php echo $mese ?> >
                          <input type="hidden" name="anno" value=<?php echo $anno ?> >
                          <button type="submit" class="imgbtn"> <img src="icone/edit.png" height="25" width="30"></button>
                        </form></td>
                        <TD><?php echo $nc_date[$i] ?></TD>
                        <TD><?php echo $device_name[$i] ?></TD>
                        <TD><?php echo $position[$i] ?></TD>
                        <TD><?php echo $nc_type[$i] ?></TD>
                        <TD><?php echo $nc_ac[$i] ?></TD>
                      </TR>
                      <?php
                    }
                    echo "</TABLE> ";
                    echo "</div>";
                  }
                  ?>


                <?php } else if(@$_GET["act"] == "monthly_report") { // ---------- Report mensili -  ?>

                  <?php
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
                  $x=0;
                  while($row = $result->fetch_assoc()) {
                    $serial[$x]=$row["serial"];
                    $device_name[$x]=$row["device_name"];
                    $position[$x]=$row["position"];
                    ++$x;
                  }
                  $count=count($serial);
                  ?>


                  <div class="modal-content"> <br> <center>
                    <br> <center>
                      <h3> Genera report mensile </h3>
                      <br>
                      <form action="hooly_report.php" method="post">
                        <select name="serial">
                          <?php for ($i=0;$i<$count;$i++) {
                            echo "<option value= \"$serial[$i]\"";
                            echo "> $device_name[$i]  $position[$i] </option>\n";
                          } ?>
                        </select>

                        <?php include 'includes/select_mese_anno.php'; ?>

                        <br> <br> <br>
                        Seleziona i tre orari giornalieri:
                        <br> <br>
                        <select name="ora1">
                          <option value="00">00:00</option>
                          <option value="02">02:00</option>
                          <option value="04">04:00</option>
                          <option value="06" selected>06:00</option>
                          <option value="08">08:00</option>
                          <option value="10">10:00</option>
                          <option value="12">12:00</option>
                          <option value="14">14:00</option>
                          <option value="16">16:00</option>
                          <option value="18">18:00</option>
                          <option value="20">20:00</option>
                          <option value="22">22:00</option>
                        </select>
                        <select name="ora2">
                          <option value="00">00:00</option>
                          <option value="02">02:00</option>
                          <option value="04">04:00</option>
                          <option value="06">06:00</option>
                          <option value="08">08:00</option>
                          <option value="10" selected>10:00</option>
                          <option value="12">12:00</option>
                          <option value="14">14:00</option>
                          <option value="16">16:00</option>
                          <option value="18">18:00</option>
                          <option value="20">20:00</option>
                          <option value="22">22:00</option>
                        </select>
                        <select name="ora3">
                          <option value="00">00:00</option>
                          <option value="02">02:00</option>
                          <option value="04">04:00</option>
                          <option value="06">06:00</option>
                          <option value="08">08:00</option>
                          <option value="10">10:00</option>
                          <option value="12">12:00</option>
                          <option value="14">14:00</option>
                          <option value="16" selected>16:00</option>
                          <option value="18">18:00</option>
                          <option value="20">20:00</option>
                          <option value="22">22:00</option>
                        </select>
                        <br><br>

                        <button type="submit" class="greenbtn">Report SA-04</button>
                      </form>
                    </div>


                  <?php } else if(@$_GET["act"] == "alarm_pause") { // ---------- Sospendi notifiche -  ?>

                    <?php
                    $query = "SELECT alarm_pause_flag_1,alarm_pause_from_1,alarm_pause_to_1,alarm_pause_flag_2,alarm_pause_from_2,alarm_pause_to_2 FROM alarm_pause WHERE codUtente='$COD_UTENTE'";
                    $result = $conn->query($query);
                    if(($result->num_rows) == 1)
                    {
                      while($row = $result->fetch_assoc()) {
                        $alarm_pause_flag_1_result = $row["alarm_pause_flag_1"];
                        $alarm_pause_from_1_result = $row["alarm_pause_from_1"];
                        $alarm_pause_to_1_result = $row["alarm_pause_to_1"];
                        $alarm_pause_flag_2_result = $row["alarm_pause_flag_2"];
                        $alarm_pause_from_2_result = $row["alarm_pause_from_2"];
                        $alarm_pause_to_2_result = $row["alarm_pause_to_2"];
                      }
                    } else {

                      $alarm_pause_flag_1 = 0;
                      $alarm_pause_from_1_result = "01";
                      $alarm_pause_to_1_result = "01";
                      $alarm_pause_flag_2 = 0;
                      $alarm_pause_from_2_result = "01";
                      $alarm_pause_to_2_result = "01";
                    }

                    ?>

                    <div class="modal-content">
                      <br><br>
                      <center>
                        <h3>Non inviare allarmi</h3>
                        <br>
                        <form action="hooly_db_actions.php" onsubmit="return checkOrari()" method="post">
                          <input type="checkbox" name="alarm_pause_flag_1" value="1" <?php if($alarm_pause_flag_1_result) echo "checked"; ?>>

                          dalle ore:
                          <select name="alarm_pause_from_1" id="from1">
                            <?php for($i=1;$i<23;$i++){
                              echo "<option value= \""; printf('%02d', $i); echo "\"";
                              if (intval($alarm_pause_from_1_result) == $i) { echo " selected";}
                              echo ">"; printf('%02d', $i); echo ":00</option>\n";
                            }?>
                          </select>
                          alle ore:
                          <select name="alarm_pause_to_1" id="to1">
                            <?php for($i=1;$i<23;$i++){
                              echo "<option value= \""; printf('%02d', $i); echo "\"";
                              if (intval($alarm_pause_to_1_result) == $i) { echo " selected";}
                              echo ">"; printf('%02d', $i); echo ":00</option>\n";
                            }?>
                          </select>
                          <br>

                          <input type="checkbox" name="alarm_pause_flag_2" value="1" <?php if($alarm_pause_flag_2_result) echo "checked"; ?>>
                          dalle ore:
                          <select name="alarm_pause_from_2" id="from2">
                            <?php for($i=1;$i<23;$i++){
                              echo "<option value= \""; printf('%02d', $i); echo "\"";
                              if (intval($alarm_pause_from_2_result) == $i) { echo " selected";}
                              echo ">"; printf('%02d', $i); echo ":00</option>\n";
                            }?>
                          </select>
                          alle ore:
                          <select name="alarm_pause_to_2" id="to2">
                            <?php for($i=1;$i<23;$i++){
                              echo "<option value= \""; printf('%02d', $i); echo "\"";
                              if (intval($alarm_pause_to_2_result) == $i) { echo " selected";}
                              echo ">"; printf('%02d', $i); echo ":00</option>\n";
                            }?>
                          </select>
                          <br> <br> <br>
                          <input type="hidden" name="act" value="alarm_pause_record">
                          <button type="submit" class="greenbtn">Imposta</button>
                        </form>
                      </center>
                    </div>


                  <?php } else if(@$_GET["act"] == "logout") { // ---------- Logout ?>


                    <?php
                    setcookie('LOGIN', null);
                    header('Location: http://www.hooly.eu');
                    ?>


                  <?php } else if(@$_GET["act"] == "changePwd") { // ---------- cambio password ?>


                    <div class=modal-content>
                      <center>
                        <form action="generals.php?act=changePwd_result" onsubmit="return checkPassword()" method="post">
                          <br>
                          Inserisci la nuova password:
                          <br>
                          <input type="password" style="width:100%" placeholder="Enter Password" name="password" id="password" maxlength="14" pattern="[A-Za-z0-9]{5,12}" title="La passowrd può contenere lettere e numeri, un minimo di 5 ed un massimo di 12 caratteri alfanumerici" required>
                          <br> <br>
                          Ripeti la password:
                          <br>
                          <input type="password" style="width:100%" placeholder="Repeat Password" name="psw-repeat" id="confirm_password"  maxlenght="14" required>
                          <input name="act" type="hidden" value="registrazione">
                          <br><br>
                          <button type="submit" class=greenbtn>Cambia la tua password</button>
                        </form>
                      </center>
                    </div>


                  <?php } else if(@$_GET["act"] == "changePwd_result") { // ---------- effettua il cambio password  ?>

                    <?php
                    $password	= $_POST["password"];
                    $codPassword	= md5($password);
                    $query	= "UPDATE `utenti` SET `password` =  '$codPassword' WHERE `codUtente` = '$COD_UTENTE' ";
                    $result	= $conn->query($query);
                    $Messaggio	= "
                    Ciao, questa e-mail ti giunge dall'area riservata di ".NOMESITO.".\n\n
                    Ti informiamo che la tua password è stata cambiata.\n
                    Se non hai effettuato tu il cambio password ti invitiamo a contattarci direttamente.
                    ";
                    $query = "SELECT username FROM utenti WHERE codUtente='$COD_UTENTE'";
                    $result = $conn->query($query);
                    while($row = $result->fetch_assoc()) {
                      $To = $row["username"];
                    }
                    mail($To, "Hooly Sensors - Conferma registrazione", $Messaggio, "From: admin@hooly.eu");

                    setcookie('LOGIN', null);
                    ?>
                    <div class=modal-content>
                      <br>
                      Il cambio password è avvenuto correttamente.<br>
                      Adesso dovrai nuovamente autenticarti passando dalla pagina di Login<br><br><br>
                      <button type="button" onclick="location.href='index.php';" class="greenbtn">Login</button>


                    <?php } else if(@$_GET["act"] == "set_notifyMethod") { // ---------- Imposta metodo di notifica  ?>

                      <?php
                      $query = "SELECT * FROM notify_method WHERE codUtente='$COD_UTENTE' limit 1";
                      $result = $conn->query($query);
                      if(($result->num_rows) == 1)
                      {
                        while($row = $result->fetch_assoc()) {
                          $telegram_flag = $row["telegram_flag"];
                          $telegram_chatid = $row["telegram_chatid"];
                          $pushbullett_flag = $row["pushbullett_flag"];
                          $pushbullett_addr = $row["pushbullett_addr"];
                          $email_flag = $row["email_flag"];
                          $email_addr = $row["email_addr"];
                          $whatsapp_flag = $row["whatsapp_flag"];
                          $whatsapp_tel = $row["whatsapp_tel"];
                        }
                      } else {
                        $telegram_flag = "";
                        $telegram_chatid = "";
                        $pushbullett_flag = "";
                        $pushbullett_addr = "";
                        $email_flag = "";
                        $email_addr = "";
                        $whatsapp_flag = "";
                        $whatsapp_tel = "";
                      }
                      ?>

                      <div class=modal-content>
                        <center>
                          <br><br>
                          <h3>Desidero ricevere le notifiche tramite:</h3>
                          <br><br>

                          <form action="hooly_db_actions.php" method="post">
                            <table>
                              <tr><td><input type="checkbox" name="telegram_flag" value="1" <?php if($telegram_flag) echo "checked"; ?> >Telegram </td><td></td><td>
                                ChatId:</td><td> <input type="text" class=slim name="telegram_chatid" maxlength="20" value=<?php echo $telegram_chatid ?> ></td>
                              </tr>
                              <tr><td><input type="checkbox" name="pushbullett_flag" value="1" <?php if($pushbullett_flag) echo "checked"; ?> >Pushbullett</td><td>&nbsp&nbsp&nbsp</td><td>
                                Addr:</td><td> <input type="email" class=slim name="pushbullett_addr" maxlength="50" value=<?php echo $pushbullett_addr ?> ></td>
                              </tr>
                              <tr><td><input type="checkbox" name="email_flag" value="1" <?php if($email_flag) echo "checked"; ?> >Email</td><td></td><td>
                                Addr:</td><td> <input type="email" class=slim name="email_addr" maxlength="50" value=<?php echo $email_addr ?> ></td>
                              </tr>
                              <tr><td><input type="checkbox" name="whatsapp_flag" value="1" <?php if($whatsapp_flag) echo "checked"; ?> >WhatsApp</td><td>&nbsp</td><td>
                                #Tel:</td><td> <input type="text" class=slim name="whatsapp_tel" maxlength="20" value=<?php echo $whatsapp_tel ?> ></td>
                              </tr>
                            </table>
                            <br>
                            <input type="hidden" name="act" value="set_notifyMethod">
                            <button type="submit" class=greenbtn>Conferma</button>
                          </form>
                        </center>
                      </div>


                    <?php } else if(@$_GET["act"] == "set_personalInfo") { // ---------- Imposta info utente  ?>


                      <?php
                      $query = "SELECT * FROM personal_info WHERE codUtente='$COD_UTENTE' limit 1";
                      $result = $conn->query($query);
                      if(($result->num_rows) == 1)
                      {
                        while($row = $result->fetch_assoc()) {
                          $ragione_sociale = $row["ragione_sociale"];
                          $indirizzo_1 = $row["indirizzo_1"];
                          $indirizzo_2 = $row["indirizzo_2"];
                          $cap = $row["cap"];
                          $citta = $row["citta"];
                          $telefono = $row["telefono"];
                        }
                      } else {
                        $ragione_sociale = "";
                        $indirizzo_1 = "";
                        $indirizzo_2 = "";
                        $cap = "";
                        $citta = "";
                        $telefono = "";
                      }
                      ?>


                      <div class="modal-content">
                        <br><br>
                        <center>
                          <h3> Gestisci le informazioni personali </h3>
                          <form action="hooly_db_actions.php" method="post">
                            <br>
                            <table>
                              <tr><td>Ragione Sociale: </td><td><input type="text" class="slim" name="ragione_sociale" maxlength="50" value="<?php echo $ragione_sociale; ?>"></td></tr>
                              <tr><td>Indirizzo 1: </td><td><input type="text" class="slim" name="indirizzo_1" maxlength="50" value="<?php echo $indirizzo_1; ?>"></td></tr>
                              <tr><td>Indirizzo 2: </td><td><input type="text" class="slim" name="indirizzo_2" maxlength="50" value="<?php echo $indirizzo_2; ?>"></td></tr>
                              <tr><td>CAP: </td><td><input type="text" class="slim" name="cap" maxlength="5" value="<?php echo $cap; ?>"></td></tr>
                              <tr><td>Città: </td><td><input type="text" class="slim" name="citta" maxlength="32" value="<?php echo $citta; ?>"></td></tr>
                              <tr><td>Telefono: </td><td><input type="text" class="slim" name="telefono" maxlength="32" value="<?php echo $telefono; ?>"></td></tr>
                            </table>
                            <br>
                            <input type="hidden" name="act" value="set_personalInfo">
                            <button type="submit" class=greenbtn>Conferma</button>
                          </form>
                        </center>
                      </div>


                    <?php } ?>
                  </body>
                  </html>
                </body>
