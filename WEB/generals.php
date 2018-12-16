<?php
if(isset($_COOKIE['LOGIN'])) { $COD_UTENTE = $_COOKIE['LOGIN'];}
else { $COD_UTENTE = 0; header("Location: index.php"); }
include "dbactions/db_connection.php";
?>

<head>

  <title>Hooly sensors</title>
  <link href="css/reset.css" rel="stylesheet" type="text/css" />
  <link href="css/stile.css" rel="stylesheet" type="text/css" />
  <link href="css/jquery-ui.css" rel="stylesheet">
  <link rel="apple-touch-icon" href="/icone/temp_icon.png">
  <link rel="icon" href="/icone/temp_icon.png">

  <script src="scripts/jquery.min.js"></script>
  <script src="scripts/jquery-ui.js"></script>
  <script src="scripts/datePickerLocalized.js"></script>
  <script src="scripts/utils.js"></script>
  <script>
  $(function() {
    $( ".datepicker" ).datepicker();
  });
  </script>

  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="apple-mobile-web-app-status-bar-style" content="default" />

</head>

<body>
  <?php include 'includes/headerTableMenu.php'; ?>
  <BR>



    <?php if(@$_GET["act"] == "NC_insert") {// ----------- Inserimento non conformità   ?>

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
          <form onsubmit="preventMultiSubmit()" action="dbactions/hooly_db_actions.php" method="post">
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


      <?php } else if(@$_GET["act"] == "RM_insert") { // ---------- Inserisci Registrazone Manuale   ?>


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
            <h3> Inserisci una registrazione manuale</h3>
            <br>
            <form onsubmit="preventMultiSubmit()" action="dbactions/hooly_db_actions.php" method="post">
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
              Data: <input type="text" class="slim" id="datepicker" name="date" maxlength="10" value="<?php echo date("d/m/Y"); ?>" required>
              <br>
              Seleziona la fascia oraria:
              <select name="item">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
              </select>
              <br>
              Inserisci ore e minuti:
              <input type="number" name="ora" class="slim" min="0" max="23" required>:
              <input type="number" name="minuto" class="slim" min="0" max="59" required><br><br><br>
              Inserisci la temperatura rilevata manualmente:<br>
              <input type="number" name="temp_gradi" min="-30" max="60" required>,
              <input type="number" name="temp_centesimi" class="slim" min="0" max="99">
              <br><br>
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
            <form action="dbactions/hooly_db_actions.php" method="post">
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


        <?php } else if(@$_POST["act"] == "RM_modify") { // ---------- Modifica Rilevazione Manuale   ?>


          <?php
          $mese=$_POST['mese'];
          $anno=$_POST['anno'];
          $id = $_POST['id'];

          $query = "SELECT * FROM rilevazioni_manuali where id = '$id'";
          $result = $conn->query($query);
          while($row = $result->fetch_assoc()) {
            $serial_trovato=$row["serial"];
            $device_name_trovato=$row["device_name"];
            $position_trovato=$row["position"];
            $giorno=$row["giorno"];
            $mese=$row["mese"];
            $anno=$row["anno"];
            $ora=$row["ora"];
            $minuto=$row["minuto"];
            $item_trovato=$row["item"];
            $temp=$row["temp"];
          }

          $splitted_temp = preg_split('/\./',$temp);
          $temp_gradi = $splitted_temp[0];
          $temp_centesimi = $splitted_temp[1];

          $date_trovato= $giorno . "/" . $mese . "/" . $anno;

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
            <h3> Modifica rilevazione manuale</h3>
            <br>
            <form onsubmit="preventMultiSubmit()" action="dbactions/hooly_db_actions.php" method="post">
              <input type="hidden" name="act" value="rm_modify">
              <input type="hidden" name="mese" value="<?php echo $mese; ?>">
              <input type="hidden" name="anno" value="<?php echo $anno; ?>">
              <input type="hidden" name="id" value="<?php echo $id; ?>">
              <input type="hidden" name="cod_utente" value="<?php echo $COD_UTENTE; ?>">

              <br>
              Seleziona dispositivo: <select name="serial">
                <?php for ($i=0;$i<$count;$i++) {
                  echo "<option value= \"$serial[$i]\" ";
                  if ($serial[$i] == $serial_trovato) echo " selected";
                  echo "> $device_name[$i]  $position[$i] </option>\n";
                }
                ?>
              </select>
              <br>
              Data: <input type="text" class="slim" id="datepicker" name="date" maxlength="10" value="<?php echo $date_trovato; ?>" required>
              <br>
              Seleziona la fascia oraria:
              <select name="item">
                <option value="1" <?php if ($item_trovato == "1") echo " selected"; ?> >1</option>
                <option value="2" <?php if ($item_trovato == "2") echo " selected"; ?> >2</option>
                <option value="3" <?php if ($item_trovato == "3") echo " selected"; ?> >3</option>
              </select>
              <br>
              Inserisci ore e minuti:
              <input type="number" name="ora" class="slim" min="0" max="23" value=<?php echo $ora ?> required>:
              <input type="number" name="minuto" class="slim" min="0" max="59" value=<?php echo $minuto ?> required><br><br><br>
              Inserisci la temperatura rilevata manualmente:<br>
              <input type="number" name="temp_gradi" min="-30" max="60" value=<?php echo $temp_gradi ?> required>,
              <input type="number" name="temp_centesimi" class="slim" min="0" max="99" value=<?php echo $temp_centesimi ?> >
              <br><br>
              <button id="mybutton" type="submit" class="greenbtn">Modifica</button>
              <div style=font-size:12px;text-align:left;margin:1% auto 1% auto;padding:30px;>
                <b>NB: </b> Le rilevazioni manuali saranno segnalate con il carattere "M"
              </div>
            </form>
          </div>


        <?php } else if(@$_GET["act"] == "delete_hooly_response") { // ---------- delete Hooly ?>


          <div class="modal-content">
            <?php
            $serial = $_POST['serial'];
            if (isset($_POST['delete_all']))  $delete_all = $_POST['delete_all']; else $delete_all = 0;

            $query = "UPDATE new_devices SET assigned = 0 WHERE serial = '$serial'";
            $result = $conn->query($query);
            $query = "UPDATE new_devices SET former_owner = '$COD_UTENTE' WHERE serial = '$serial'";
            $result = $conn->query($query);
            $query = "UPDATE new_devices SET owner = '' WHERE serial = '$serial'";
            $result = $conn->query($query);
            $query = "DELETE FROM devices WHERE serial = '$serial'";
            $result = $conn->query($query);
            $query = "DELETE FROM last_rec_data WHERE serial = '$serial'";
            $result = $conn->query($query);
            if ($delete_all == 1) {
              $query = "INSERT INTO rec_data_trash SELECT * FROM rec_data WHERE serial = '$serial'";
              $result = $conn->query($query);
              $query = "DELETE FROM rec_data WHERE serial = '$serial'";
              $result = $conn->query($query);
            }
            ?>

            Operazione effettuata con successo !! <br><br>
            <center>
              <button type="button" onclick="location.href='generals.php?act=add_hooly';" class="greenbtn">Torna a "aggiungi o elimina un sensore"</button>
              <br>
            </center>
          </div>


        <?php } else if(@$_GET["act"] == "add_hooly_response") { // ---------- add Hooly: verify and record  ?>


          <div class="modal-content">
            <?php
            $idUtente = $_POST['idUtente'];
            $tenant = $_POST['tenant'];
            $serial = $_POST['serial'];
            $pin = $_POST['pin'];

            $query = "SELECT pin FROM new_devices WHERE serial = '$serial' and assigned = 0";
            $result = $conn->query($query);
            if ($result->num_rows > 0) {
              while($row = $result->fetch_assoc()) {
                $pin_trovato = $row["pin"];
              }
              if ($pin == $pin_trovato) {

                $query = "update new_devices set assigned = 1 WHERE serial='$serial'";
                $result = $conn->query($query);
                $query = "update new_devices set owner = '$COD_UTENTE' WHERE serial='$serial'";
                $result = $conn->query($query);
                $query = "insert into devices (serial,device_name,position,armed,batt_alarmed,alarmed,min_ok,max_ok,batt_type,tenant,code_period) values ('$serial','Hooly_$serial','posizione',0,0,0,10,30,3,'$tenant',5) ";
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

          $x = 0;
          $query = "SELECT serial, device_name, position FROM devices WHERE tenant = '$tenant0'";
          $result = $conn->query($query);
          while($row = $result->fetch_assoc()) {
            $serial_array[$x] = $row["serial"];
            $device_name_array[$x] = $row["device_name"];
            $position_array[$x] = $row["position"];
            $x++;
          }
          ?>

          <div class="modal-content">
            <br><br>
            <center>

              <h3>Aggiungi un sensore</h3>
              <br>
              <form action="generals.php?act=add_hooly_response" method="post">
                <br>
                Seriale: <input type="text" class="slim" name="serial" size="6" maxlength="4" pattern="[A-Z0-9]{4,4}" title= "Il codice seriale consiste di 4 caratteri (numeri o lettere maiuscole) ed è indicato sulla confezione del tuo Hooly" required>
                Pin: <input type="text" class="slim" name="pin" size="6" maxlength="4" pattern="[0-9]{4,4}" title="Il pin consiste di 4 caratteri (solo numeri) ed è indicato sulla confezione del tuo Hooly" required>
                <input type="hidden" name="idUtente" value="<?php echo $idUtente; ?>">
                <input type="hidden" name="tenant" value="<?php echo $tenant0; ?>">
                <br><br>
                <button type="submit" class=greenbtn>Aggiungi</button>
              </form>
            </div>

            <center>
              Oppure
            </center>

            <div class="modal-content">
              <br><br>
              <center>
                <h3>Elimina un sensore</h3>
                <br>
                <form action="generals.php?act=delete_hooly_response" method="post" onsubmit="return checkConfirm_with_attention_1()">
                  <br>
                  Seriale: <select name="serial" class="slim">
                    <?php  for($i=0;$i<$x;$i++) {echo "<option value= \"$serial_array[$i]\""; echo ">$device_name_array[$i] - $position_array[$i]</option>\n";}  ?>
                  </select><br>
                  Cancella anche tutti i dati storici associati
                  <input type = "checkbox" name = "delete_all" value = "1">
                  <br><br>
                  <button type="submit" class=redbtn>Elimina</button>
                </form>
              </div>


            <?php } else if(@$_GET["act"] == "delete_records_check") { // ---------- delete records  ?>


              <div class="modal-content">
                <br><br>
                <center>
                  <h3>Elimina rilevazioni</h3>
                  <br>
                  <?php
                  $serial = $_POST['serial'];
                  $date_from = $_POST['date_from'];
                  $ora_from = $_POST['ora_from'];
                  if ($ora_from < 9) $ora_from = sprintf("%02d",$ora_from);
                  $minuto_from = $_POST['minuto_from'];
                  if ($minuto_from < 9) $minuto_from = sprintf("%02d",$minuto_from);
                  $date_to = $_POST['date_to'];
                  $ora_to = $_POST['ora_to'];
                  if ($ora_to < 9) $ora_to = sprintf("%02d",$ora_to);
                  $minuto_to = $_POST['minuto_to'];
                  if ($minuto_to < 9) $minuto_to = sprintf("%02d",$minuto_to);

                  $splitted_date_from = preg_split('/\//',$date_from);
                  $giorno = $splitted_date_from[0];
                  $mese = $splitted_date_from[1];
                  $anno = $splitted_date_from[2];
                  $timestamp_from = "'" . $anno . "-" . $mese . "-" . $giorno . " " . $ora_from . ":" . $minuto_from . "'";

                  $splitted_date_to = preg_split('/\//',$date_to);
                  $giorno = $splitted_date_to[0];
                  $mese = $splitted_date_to[1];
                  $anno = $splitted_date_to[2];
                  $timestamp_to = "'" . $anno . "-" . $mese . "-" . $giorno . " " . $ora_to . ":" . $minuto_to . "'";

                  $query = "SELECT count(*) as count FROM rec_data WHERE serial = '$serial' and timestamp > $timestamp_from and timestamp < $timestamp_to";
                  $result = $conn->query($query);
                  $row = $result->fetch_assoc();
                  $count = $row["count"];
                  if ($count > 0)
                  { ?>
                    Per il sensore con seriale <?php echo $serial; ?> sono state trovate <?php echo $count; ?> rilevazioni nell'intervallo di tempo
                    tra le ore <br> <?php echo $ora_from . ":" . $minuto_from ?> del giorno <?php echo $date_from ?>
                    <br> e le ore  <br> <?php echo $ora_to . ":" . $minuto_to ?> del giorno <?php echo $date_to ?>.
                    <br><br>
                    Sei sicuro di volerle elminare ?
                    <br><br>
                    <form action="generals.php?act=delete_records_response" method="post" onsubmit="return checkConfirm_with_attention_1()">

                      <input type="hidden" name="serial" value="<?php echo $serial; ?>">
                      <input type="hidden" name="date_from" value="<?php echo $date_from; ?>">
                      <input type="hidden" name="ora_from" value="<?php echo $ora_from; ?>">
                      <input type="hidden" name="minuto_from" value="<?php echo $minuto_from; ?>">
                      <input type="hidden" name="date_to" value="<?php echo $date_to; ?>">
                      <input type="hidden" name="ora_to" value="<?php echo $ora_to; ?>">
                      <input type="hidden" name="minuto_to" value="<?php echo $minuto_to; ?>">
                      <button type="submit" class=greenbtn>Conferma</button>
                      <button type="button" onclick="location.href='generals.php?act=delete_records';" class="greenbtn">Torna indietro</button>
                    </form>
                  <?php  }   else   { ?>
                    Non ci sono rilevazioni nell'intervallo specificato.<br><br>
                    <br><br><br>
                    <button type="button" onclick="location.href='generals.php?act=delete_records';" class="greenbtn">Torna indietro</button>
                  <?php } ?>
                </center>
              </div>


            <?php } else if(@$_GET["act"] == "delete_records_response") { // ---------- delete Hooly ?>


              <?php
              $serial = $_POST['serial'];
              $date_from = $_POST['date_from'];
              $ora_from = $_POST['ora_from'];
              $minuto_from = $_POST['minuto_from'];
              if ($minuto_from < 9) $minuto_from = sprintf("%02d",$minuto_from);
              $date_to = $_POST['date_to'];
              $ora_to = $_POST['ora_to'];
              $minuto_to = $_POST['minuto_to'];
              if ($minuto_to < 9) $minuto_from = sprintf("%02d",$minuto_to);

              $splitted_date_from = preg_split('/\//',$date_from);
              $giorno = $splitted_date_from[0];
              $mese = $splitted_date_from[1];
              $anno = $splitted_date_from[2];
              $timestamp_from = "'" . $anno . "-" . $mese . "-" . $giorno . " " . $ora_from . ":" . $minuto_from . "'";

              $splitted_date_to = preg_split('/\//',$date_to);
              $giorno = $splitted_date_to[0];
              $mese = $splitted_date_to[1];
              $anno = $splitted_date_to[2];
              $timestamp_to = "'" . $anno . "-" . $mese . "-" . $giorno . " " . $ora_to . ":" . $minuto_to . "'";
              $query = "SELECT count(*) AS count FROM rec_data WHERE serial = '$serial' AND timestamp > $timestamp_from AND timestamp < $timestamp_to";
              $result = $conn->query($query);
              $row = $result->fetch_assoc();
              $count = $row["count"];

              $query = "INSERT INTO rec_data_trash SELECT * FROM rec_data WHERE serial = '$serial' AND timestamp > $timestamp_from AND timestamp < $timestamp_to";
              $result = $conn->query($query);
              $query = "DELETE FROM rec_data WHERE serial = '$serial' AND timestamp > $timestamp_from AND timestamp < $timestamp_to";
              $result = $conn->query($query);
              ?>
              <div class="modal-content">
                <br><br>
                <center>
                  <h3>Elimina rilevazioni</h3>
                  <br><br>

                  Operazione effettuata con successo !! <br><br>
                  Sono state eliminate <?php echo $count ?> rilevazioni.
                  <br><br>

                  <button type="button" onclick="location.href='status.php';" class="greenbtn">Torna alla pagina principale</button>
                  <br>
                </center>
              </div>


            <?php } else if(@$_GET["act"] == "delete_records") { // ---------- Cancella dati da rec-data  ?>


              <?php
              $query = "SELECT idUtente,t0 FROM utenti WHERE codUtente='$COD_UTENTE'";
              $result = $conn->query($query);
              while($row = $result->fetch_assoc()) {
                $idUtente = $row["idUtente"];
                $tenant0 = $row["t0"];
              }

              $x = 0;
              $query = "SELECT serial, device_name, position FROM devices WHERE tenant = '$tenant0'";
              $result = $conn->query($query);
              while($row = $result->fetch_assoc()) {
                $serial_array[$x] = $row["serial"];
                $device_name_array[$x] = $row["device_name"];
                $position_array[$x] = $row["position"];
                $x++;
              }
              ?>

              <div class="modal-content">
                <br><br>
                <center>
                  <h3>Elimina rilevazioni</h3>
                  <br>
                  <form action="generals.php?act=delete_records_check" method="post">
                    <br>
                    Sensore:
                    <select name="serial" class="slim">
                      <?php  for($i=0;$i<$x;$i++) {echo "<option value= \"$serial_array[$i]\""; echo ">$device_name_array[$i] - $position_array[$i]</option>\n";}  ?>
                    </select>
                    <br><br><br>
                    Seleziona l'intervallo:
                    <br><br>
                    Da: <input type="text" class="slim datepicker" name="date_from" size='10' maxlength="10" value="<?php echo date("d/m/Y"); ?>" required>
                    ore e minuti:
                    <input type="number" name="ora_from" class="slim" min="0" max="23" required>:
                    <input type="number" name="minuto_from" class="slim" min="0" max="59" required><br><br>
                    A: <input type="text" class="slim datepicker" name="date_to" size='10' maxlength="10" value="<?php echo date("d/m/Y"); ?>" required>
                    ore e minuti:
                    <input type="number" name="ora_to" class="slim" min="0" max="23" required>:
                    <input type="number" name="minuto_to" class="slim" min="0" max="59" required><br><br><br>
                    <br><br>
                    <button type="submit" class=greenbtn>Elimina</button>
                  </form>
                </center>
              </div>


            <?php } else if(@$_GET["act"] == "add_router_response") { // ---------- add router: verify and record  ?>


              <div class="modal-content">
                <?php
                $serial = $_POST['serial'];
                $pin = $_POST['pin'];

                $query = "SELECT pin FROM router WHERE router = '$serial' AND codUtente = '' ";
                $result = $conn->query($query);
                if ($result->num_rows > 0) {
                  while($row = $result->fetch_assoc()) {
                    $pin_trovato = $row["pin"];
                  }
                  if ($pin == $pin_trovato) {
                    $query = "UPDATE router SET codUtente = '$COD_UTENTE' WHERE router = '$serial' ";
                    $result = $conn->query($query); ?>
                    Operazione effettuata con successo !! <br><br>
                    <center>
                      Inserisci adesso i tuoi Hooly e sei subito pronto:
                      <br><br>
                      <button type="button" onclick="location.href='generals.php?act=add_hooly';" class="greenbtn">Inserisci un Hooly</button>
                      <?php
                    }  else   { ?>
                      Ops, c'e' stato un errore:<br><br>
                      Pin errato<br><br><br>
                      <button type="button" onclick="location.href='generals.php?act=add_router';" class="greenbtn">Torna indietro</button>
                    <?php }
                  }   else   { ?>
                    Ops, c'e' stato un errore:<br><br>
                    Il seriale non esiste oppure è già in uso<br><br><br>
                    <button type="button" onclick="location.href='generals.php?act=add_router';" class="greenbtn">Torna indietro</button>
                  <?php } ?>
                </center>

              </div>


            <?php } else if(@$_GET["act"] == "add_router") { // ---------- add Hooly: inserisci serial e pin  ?>

              <?php
              $query = "SELECT router FROM router WHERE codUtente = '$COD_UTENTE' ";
              $result = $conn->query($query);
              if ($result->num_rows > 0) $router_trovato = 1; else $router_trovato = 0;
              ?>

              <div class="modal-content"> <br> <center>
                <br><br>
                <center>
                  <h3> <?php if($router_trovato) echo "Registrazione nuovo Hooly Router"; else echo "Benvenuto nel servizio Hooly !"; ?> </h3>
                  <br>
                  <form action="generals.php?act=add_router_response" method="post">
                    <br>
                  </center>
                  <?php if($router_trovato) echo "Inserisci il numero seriale ed il pin del nuovo router"; else echo "Registra subito un router ed inizia con Hooly"; ?>
                  <br><br>
                  <center>
                    Seriale: <input type="text" class="slim" name="serial" size="8" maxlength="6" pattern="[0-9]{6,6}" title= "Il codice seriale consiste di 6 numeri ed è indicato sulla confezione del tuo Hooly-router" required>
                    Pin: <input type="text" class="slim" name="pin" size="6" maxlength="4" pattern="[0-9]{4,4}" title="Il pin consiste di 4 numeri ed è indicato sulla confezione del tuo Hooly-router" required>
                    <input type="hidden" name="codUtente" value="<?php echo $COD_UTENTE; ?>">
                    <br>
                    <button type="submit" class=greenbtn>Registra il router</button>
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

              <?php } else if(@$_GET["act"] == "billing") { // ---------- Gestione conto mensile e credito -  ?>


                <div class="modal-content NC_manage">
                  <br><br>
                  <center>
                    <h3> Fatturazione </h3>
                    <br>
                  </center>

                  <?php
                  $query = "SELECT router FROM router where codUtente = '$COD_UTENTE'";
                  $result = $conn->query($query);
                  $x=0;
                  while($row = $result->fetch_assoc()) {
                    $router[$x]=$row["router"];
                    ++$x;
                  }
                  $router_count=count($router);

                  $query = "SELECT serial FROM new_devices where owner = '$COD_UTENTE' and assigned = '1'";
                  $result = $conn->query($query);
                  $x=0;
                  while($row = $result->fetch_assoc()) {
                    $serial[$x]=$row["serial"];
                    ++$x;
                  }
                  $hooly_count=count($serial);

                  $query = "SELECT serial FROM new_devices where former_owner = '$COD_UTENTE' and assigned = '0'";
                  $result = $conn->query($query);
                  $x=0;
                  while($row = $result->fetch_assoc()) {
                    $deleted_hooly[$x]=$row["serial"];
                    ++$x;
                  }
                  $deleted_hooly_count=count($deleted_hooly);

                  $query = "SELECT saldo FROM credit where codUtente = '$COD_UTENTE' order by timestamp desc limit 1";
                  $result = $conn->query($query);
                  $row = $result->fetch_assoc();
                  $credito=$row["saldo"];

                  echo "La tua utenza risulta avere in carico: <br><br>N. <b>$hooly_count</b> Hooly e N. <b>$router_count</b> Hooly-Router";
                  if($deleted_hooly_count > 0)
                  {
                    echo "<br><br>Ci risulta anche <b>$deleted_hooly_count</b> Hooly";
                    if($deleted_hooly_count == 1) echo " eliminato"; else echo " eliminati";
                    echo " con numero di serie: ";
                    for ($x=0;$x<$deleted_hooly_count;$x++)
                    {
                      echo " <b>$deleted_hooly[$x]</b>";
                    }
                    echo ".<br>Utilizza gli Hooly eliminati oppure rivolgiti a <a href=\"mailto:admin@hooly.eu?subject=Restituzione Hooly eliminati\">admin@hooly.eu</a> per restituirli.";
                  }
                  $importo_mese=(($hooly_count+$router_count+$deleted_hooly_count)*7.5);
                  $importo_mese_ic=$importo_mese * 1.22;
                  ?>

                  <br><br><br>La spesa del mese in corso è di <b> <?php echo number_format($importo_mese_ic,2,",",".")?></b> euro (IVA inclusa)
                  e sarà addebitata sul tuo credito il primo giorno del prossimo mese. Eventuali variazioni di consistenza saranno calcolate al momento dell'addebito.
                  <br><br>Il tuo credito residuo attuale è di <b> <?php echo number_format($credito,2,",",".")?></b> euro. Ricarica con PayPal o carta di credito.<br><br>

                  <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                  <input type="hidden" name="cmd" value="_s-xclick">
                  <input type="hidden" name="hosted_button_id" value="7XCWYA7U6HFUE">
                  <input type="hidden" name="on0" value="codice">
                  <input type="hidden" name="os0" value="<?php echo $COD_UTENTE ?>">
                  <table width="100%">
                    <tr>
                      <td align="right">
                  <input type="image" src="https://www.paypalobjects.com/it_IT/IT/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal è il metodo rapido e sicuro per pagare e farsi pagare online.">
                  <img alt="" border="0" src="https://www.paypalobjects.com/it_IT/i/scr/pixel.gif" width="1" height="1">
                </td>
              </tr>
            </table>
                  </form>

                  <?php
                  $query = "SELECT * FROM credit where codUtente = '$COD_UTENTE' ORDER BY timestamp DESC";
                  $result = $conn->query($query);
                  if(($result->num_rows) != 0)
                  {
                    $found = 1;
                    $x=0;
                    while($row = $result->fetch_assoc()) {
                      $timestamp[$x]=$row["timestamp"];
                      $text[$x]=$row["text"];
                      $importo[$x]=$row["importo"];
                      $saldo[$x]=$row["saldo"];
                      ++$x;
                    }
                  }
                  if ($found == 1) { ?>
                    <br><br>Resoconto dei movimenti:<br><br>

                    <center>
                      <table class="centered NC_manage">
                        <tr>
                          <th>Timestamp</th><th>Causale</th><th>Importo</th><th>Saldo</th>
                        </tr>
                        <?php  for($i=0;$i<$x;$i++) { ?>
                          <tr>
                            <TD style="border: 1px solid #dddddd; padding: 10px;" width="30%"><?php echo $timestamp[$i] ?></TD>
                            <TD style="border: 1px solid #dddddd; padding: 10px;" width="45%"><?php echo $text[$i] ?></TD>
                            <TD style="border: 1px solid #dddddd; padding: 10px;" width="10%"><?php echo number_format($importo[$i],2,",",".") ?></TD>
                            <TD style="border: 1px solid #dddddd; padding: 10px;" width="15%"><?php echo number_format($saldo[$i],2,",",".") ?></TD>
                          </tr>
                        <?php }  ?>
                      </TABLE>
                    <?php } else echo "Non ci sono dati da visualizzare" ?>
                    <br><br><br><br>
                    <button type="button" onclick="location.href='status.php';" class="greenbtn">Torna alla pagina principale</button>
                    <br><br><br>
                  </center>



                </div>


              <?php } else if(@$_GET["act"] == "RM_manage_select") { // ---------- Gestione RM: seleziona periodo -  ?>


                <div class="modal-content">
                  <br><br>
                  <center>
                    <h3> Mostra e gestisci le Rilevazioni Manuali registrate</h3>
                    <br>
                    <form action="generals.php?act=RM_manage" method="post">
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
                              <form action="dbactions/hooly_db_actions.php" onsubmit="return checkConfirm()" method="post">
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


                      <?php } else if(@$_GET["act"] == "RM_manage") { // ---------- Mostra e gestisce le Rilevazioni Manuali -  ?>


                        <?php
                        if(isset($_POST["mese"])) { $mese=($_POST["mese"]);}
                        if(isset($_POST["anno"])) { $anno=($_POST["anno"]);}
                        if(isset($_GET["mese"])) { $mese=($_GET["mese"]);}
                        if(isset($_GET["anno"])) { $anno=($_GET["anno"]);}
                        ?>

                        <div class="modal-content NC_manage">
                          <br><br>
                          <center>
                            Registrazioni Manuali trovate per il mese: <?php echo $mese; ?> - anno: <?php echo $anno; ?>
                            <br> <br>
                            <table class="centered NC_manage">
                              <tr><th>Elimina</th><th>Modifica</th><th>Data e ora</th><th>Impianto</th><th>Posizione</th><th>Fascia</th><th>Temperatura</th></tr>

                              <?php
                              $query = "SELECT * FROM rilevazioni_manuali where codUtente = '$COD_UTENTE' and mese = '$mese' and anno = '$anno' order by giorno,ora,minuto,item asc";
                              $result = $conn->query($query);
                              if(($result->num_rows) == 0) { ?> </table> nessun record trovato <?php }
                              else {
                                $x=0;
                                while($row = $result->fetch_assoc()) {
                                  $id[$x]=$row["id"];
                                  $serial[$x]=$row["serial"];
                                  $device_name[$x]=$row["device_name"];
                                  $position[$x]=$row["position"];
                                  $giorno[$x]=$row["giorno"];
                                  $ora[$x]=$row["ora"];
                                  $minuto[$x]=$row["minuto"];
                                  $item[$x]=$row["item"];
                                  $temp[$x]=$row["temp"];
                                  ++$x;
                                }

                                for($i=0;$i<$x;$i++) {

                                  ?>
                                  <TR>
                                    <TD>
                                      <form action="dbactions/hooly_db_actions.php" onsubmit="return checkConfirm()" method="post">
                                        <input type="hidden" name="act" value="rm_delete">
                                        <input type="hidden" name="id" value=<?php echo $id[$i] ?> >
                                        <input type="hidden" name="mese" value=<?php echo $mese ?> >
                                        <input type="hidden" name="anno" value=<?php echo $anno ?> >
                                        <button type="submit" class="imgbtn"> <img src="icone/trash.png" height="30" width="30"></button>
                                      </form>
                                    </td> <TD>
                                      <form action="generals.php" method="post">
                                        <input type="hidden" name="act" value="RM_modify">
                                        <input type="hidden" name="id" value=<?php echo $id[$i] ?> >
                                        <input type="hidden" name="mese" value=<?php echo $mese ?> >
                                        <input type="hidden" name="anno" value=<?php echo $anno ?> >
                                        <button type="submit" class="imgbtn"> <img src="icone/edit.png" height="25" width="30"></button>
                                      </form></td>
                                      <TD><?php echo $giorno[$i] . "/" . $mese . "/" . $anno . " " . $ora[$i] . ":" . $minuto[$i] ?></TD>
                                      <TD><?php echo $device_name[$i] ?></TD>
                                      <TD><?php echo $position[$i] ?></TD>
                                      <TD><?php echo $item[$i] ?></TD>
                                      <TD><?php echo $temp[$i] ?></TD>
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


                                <div class="modal-content">
                                  <br><br> <center>
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
                                  </center>
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
                                    <form action="dbactions/hooly_db_actions.php" onsubmit="return checkOrari()" method="post">
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
                                $codPassword = md5($password);
                                $query = "UPDATE `utenti` SET `password` =  '$codPassword' WHERE `codUtente` = '$COD_UTENTE' ";
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
                                </div>

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
                                    $sms_flag = $row["sms_flag"];
                                    $sms_tel = $row["sms_tel"];

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
                                  $sms_flag = "";
                                  $sms_tel = "";

                                }
                                ?>

                                <div class=modal-content>
                                  <center>
                                    <br><br>
                                    <h3>Desidero ricevere le notifiche tramite:</h3>
                                    <br><br>

                                    <form action="dbactions/hooly_db_actions.php" method="post">
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
                                        <tr><td><input type="checkbox" disabled name="whatsapp_flag" value="1" <?php if($whatsapp_flag) echo "checked"; ?> >WhatsApp</td><td>&nbsp</td><td>
                                          #Tel:</td><td> <input type="text" class=slim name="whatsapp_tel" maxlength="20" value=<?php echo $whatsapp_tel ?> ></td>
                                        </tr>
                                        <tr><td><input type="checkbox" name="sms_flag" value="1" <?php if($sms_flag) echo "checked"; ?> >SMS</td><td>&nbsp</td><td>
                                          #Tel:</td><td> <input type="text" class=slim name="sms_tel" maxlength="20" value=<?php echo $sms_tel ?> ></td>
                                        </tr>
                                        <tr><td colspan="4"> Ricorda di verificare il tuo <a href="generals.php?act=SMS_manage">credito SMS</a></td></tr>
                                      </table>
                                      <br><br>
                                      <input type="hidden" name="act" value="set_notifyMethod">
                                      <button type="submit" class=greenbtn>Conferma</button>
                                    </form>
                                  </center>
                                </div>


                              <?php } else if(@$_GET["act"] == "set_billingInfo") { // ---------- Imposta info di fatturazione  ?>


                                <?php
                                $query = "SELECT * FROM billing_info WHERE codUtente='$COD_UTENTE' LIMIT 1";
                                $result = $conn->query($query);
                                if(($result->num_rows) == 1)
                                {
                                  while($row = $result->fetch_assoc()) {
                                    $PIVA = $row["PIVA"];
                                    $ragione_sociale = $row["ragione_sociale"];
                                    $indirizzo_1 = $row["indirizzo_1"];
                                    $indirizzo_2 = $row["indirizzo_2"];
                                    $cap = $row["cap"];
                                    $citta = $row["citta"];
                                    $codice_destinatario = $row["codice_destinatario"];
                                    $richiesta_invio_pec = $row["richiesta_invio_pec"];
                                    $indirizzo_pec = $row["indirizzo_pec"];

                                  }
                                } else {
                                  $PIVA = "";
                                  $ragione_sociale = "";
                                  $indirizzo_1 = "";
                                  $indirizzo_2 = "";
                                  $cap = "";
                                  $citta = "";
                                  $codice_destinatario = "";
                                  $richiesta_invio_pec = "";
                                  $indirizzo_pec = "";
                                }
                                ?>

                                <div class="modal-content">
                                  <br><br>
                                  <center>
                                    <?php if ($result->num_rows) echo "<h3> Gestisci le informazioni di fatturazione </h3>"; else echo "<h3> Inserisci le informazioni di fatturazione </h3>";?>

                                    <form action="dbactions/hooly_db_actions.php" method="post">
                                      <br>
                                      <table>
                                        <tr><td>Partita IVA: </td><td><input type="text" class="slim" name="PIVA" maxlength="50" value="<?php echo $PIVA; ?>"></td></tr>
                                        <tr><td>Ragione Sociale: </td><td><input type="text" class="slim" name="ragione_sociale" maxlength="50" value="<?php echo htmlentities($ragione_sociale, ENT_QUOTES); ?>"></td></tr>
                                        <tr><td>Indirizzo 1: </td><td><input type="text" class="slim" name="indirizzo_1" maxlength="50" value="<?php echo htmlentities($indirizzo_1, ENT_QUOTES); ?>"></td></tr>
                                        <tr><td>Indirizzo 2: </td><td><input type="text" class="slim" name="indirizzo_2" maxlength="50" value="<?php echo htmlentities($indirizzo_2, ENT_QUOTES); ?>"></td></tr>
                                        <tr><td>CAP: </td><td><input type="text" class="slim" name="cap" maxlength="5" value="<?php echo $cap; ?>"></td></tr>
                                        <tr><td>Città: </td><td><input type="text" class="slim" name="citta" maxlength="32" value="<?php echo htmlentities($citta, ENT_QUOTES); ?>"></td></tr>
                                        <tr><td>Codice Destinatario: </td><td><input type="text" class="slim" name="codice_destinatario" maxlength="32" value="<?php echo htmlentities($codice_destinatario, ENT_QUOTES); ?>"></td></tr>
                                        <tr><td>Richiesta invio PEC: </td><td><input type="checkbox" name="richiesta_invio_pec" maxlength="32" value="1" <?php if ($richiesta_invio_pec) echo "checked"; ?>></td></tr>
                                        <tr><td>Indirizzo PEC Destinatario: </td><td><input type="text" class="slim" name="indirizzo_pec" maxlength="32" value="<?php echo $indirizzo_pec; ?>"></td></tr>
                                      </table>
                                      <br>
                                      <input type="hidden" name="act" value="set_billingInfo">
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
                                    <?php if ($result->num_rows) echo "<h3> Gestisci le informazioni personali </h3>"; else echo "<h3> Inserisci le informazioni personali </h3>";?>
                                    <form action="dbactions/hooly_db_actions.php" method="post">
                                      <br>
                                      <table>
                                        <tr><td>Ragione Sociale: </td><td><input type="text" class="slim" name="ragione_sociale" maxlength="50" value="<?php echo htmlentities($ragione_sociale, ENT_QUOTES); ?>"></td></tr>
                                        <tr><td>Indirizzo 1: </td><td><input type="text" class="slim" name="indirizzo_1" maxlength="50" value="<?php echo htmlentities($indirizzo_1, ENT_QUOTES); ?>"></td></tr>
                                        <tr><td>Indirizzo 2: </td><td><input type="text" class="slim" name="indirizzo_2" maxlength="50" value="<?php echo htmlentities($indirizzo_2, ENT_QUOTES); ?>"></td></tr>
                                        <tr><td>CAP: </td><td><input type="text" class="slim" name="cap" maxlength="5" value="<?php echo $cap; ?>"></td></tr>
                                        <tr><td>Città: </td><td><input type="text" class="slim" name="citta" maxlength="32" value="<?php echo htmlentities($citta, ENT_QUOTES); ?>"></td></tr>
                                        <tr><td>Telefono: </td><td><input type="text" class="slim" name="telefono" maxlength="32" value="<?php echo $telefono; ?>"></td></tr>
                                      </table>
                                      <br>
                                      <input type="hidden" name="act" value="set_personalInfo">
                                      <button type="submit" class=greenbtn>Conferma</button>
                                    </form>
                                  </center>
                                </div>


                              <?php } else if(@$_GET["act"] == "SMS_manage") { // ---------- Gestione SMS, credito e history  ?>


                                <?php
                                $query = "SELECT credit FROM sms_usage where codUtente = '$COD_UTENTE' ORDER BY timestamp DESC LIMIT 1";
                                $result = $conn->query($query);
                                if(($result->num_rows) == 1)
                                {
                                  $row = $result->fetch_assoc();
                                  $credit = $row["credit"];
                                } else {
                                  $credit = 0;
                                }
                                ?>

                                <div class="modal-content NC_manage">
                                  <br><br>
                                  <center>
                                    <h3> Resoconto credito SMS e messaggi inviati</h3>
                                  </center>
                                  <br><br><br>
                                  <table width=100%>
                                    <tr>
                                      <td align="left">
                                        <?php if ($credit > 0) echo "Il tuo credito residuo è di <b>" . $credit . "</b> SMS"; else echo "Attenzione! Il tuo credito SMS è esaurito."; ?>
                                      </td>
                                      <td align="right" >

                                        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                                          <input type="hidden" name="cmd" value="_s-xclick">
                                          <input type="hidden" name="hosted_button_id" value="M8WD3G8HY34T6">
                                          <table>
                                            <tr>
                                              <td>
                                                <input type="hidden" name="on0" value="Quanità">Ricarica ora
                                              </td>
                                            </tr>
                                            <tr>
                                              <td>
                                                <select name="os0">
                                                  <option value="50 sms">50 sms €10,00 EUR</option>
                                                  <option value="200 sms">200 sms €34,00 EUR</option>
                                                  <option value="500 sms">500 sms €75,00 EUR</option>
                                                </select>
                                              </td>
                                            </tr>
                                            <tr>
                                              <td>
                                                <input type="hidden" name="on1" value="codice">
                                              </td>
                                            </tr>
                                            <tr>
                                              <td>
                                                <input type="hidden" name="os1" value ="<?php echo $COD_UTENTE ?>">
                                              </td>
                                            </tr>
                                          </table>
                                          <input type="hidden" name="currency_code" value="EUR">
                                          <input type="image" src="https://www.paypalobjects.com/it_IT/IT/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal è il metodo rapido e sicuro per pagare e farsi pagare online.">
                                          <img alt="" border="0" src="https://www.paypalobjects.com/it_IT/i/scr/pixel.gif" width="1" height="1">
                                        </form>


                                      </td>
                                    </tr>
                                  </table>
                                  <br><br><br>

                                  <?php
                                  $query = "SELECT * FROM sms_usage where codUtente = '$COD_UTENTE' ORDER BY timestamp DESC";
                                  $result = $conn->query($query);
                                  if(($result->num_rows) != 0)
                                  {
                                    $found = 1;
                                    $x=0;
                                    while($row = $result->fetch_assoc()) {
                                      $destination[$x]=$row["destination"];
                                      $timestamp[$x]=$row["timestamp"];
                                      $text[$x]=$row["text"];
                                      $credito[$x]=intval($row["credit"]);
                                      ++$x;
                                    }
                                  }
                                  if ($found == 1) { ?>
                                    Resoconto:<br><br>

                                    <center>
                                      <table class="centered NC_manage">
                                        <tr>
                                          <th>Destinazione</th><th>Timestamp</th><th>Testo</th><th>Saldo</th>
                                        </tr>
                                        <?php  for($i=0;$i<$x;$i++) { ?>
                                          <tr>
                                            <TD style="border: 1px solid #dddddd; padding: 10px;" width="15%"><?php echo $destination[$i] ?></TD>
                                            <TD style="border: 1px solid #dddddd; padding: 10px;" width="30%"><?php echo $timestamp[$i] ?></TD>
                                            <TD style="border: 1px solid #dddddd; padding: 10px;" width="45%"><?php echo $text[$i] ?></TD>
                                            <TD style="border: 1px solid #dddddd; padding: 10px;" width="10%"><?php echo $credito[$i] ?></TD>
                                          </tr>
                                        <?php }  ?>
                                      </TABLE>
                                    <?php } else echo "Non ci sono SMS inviati" ?>
                                    <br><br><br><br>
                                    <button type="button" onclick="location.href='status.php';" class="greenbtn">Torna alla pagina principale</button>
                                    <br><br><br>
                                  </center>
                                </div>


                              <?php } ?>
                            </body>
                            </html>
                          </body>
