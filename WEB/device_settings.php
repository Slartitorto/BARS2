<?php
if(isset($_COOKIE['LOGIN'])) { $COD_UTENTE =	$_COOKIE['LOGIN'];
} else { $COD_UTENTE =	0; header("Location: index.php"); }
include "db_connection.php";
?>

<head><title>Hooly settings</title>
  <meta name="apple-mobile-web-app-capable" content="yes">
  <link rel="apple-touch-icon" href="/icone/app_icon128.png">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <script src="scripts/jquery.min.js"></script>
  <script>
  $(document).ready(function(){
    $("#btn1").click(function(){ $("#advanced_preferences").toggle(1000); });
  });
</script>

<script type="text/javascript">
function navigator_Go(url)
{ window.location.assign(url); }
</script>
<script>
function checkPassword() {
  var pass1 = document.getElementById("password").value;
  var pass2 = document.getElementById("confirm_password").value;
  var ok = true;
  if (pass1 != pass2) {
    document.getElementById("password").style.borderColor = "#E34234";
    document.getElementById("confirm_password").style.borderColor = "#E34234";
    ok = false;
  }
  return ok;
}
</script>

<link href="css/reset.css" rel="stylesheet" type="text/css" />
<link href="css/stile.css" rel="stylesheet" type="text/css" />
</head>
<body>
  <BR>
    <center>
      <TABLE class="top-menu">
        <TR><TD align="left"><A href="javascript:navigator_Go('index.php');"><img src="icone/left37.png" width="35"></TD></TR>
        </TABLE>
        <BR><BR><BR>
          <?php
          if(isset($_POST['serial']))
          // START self_reload
          {
            $serial=$_POST['serial'];
            $device_name=$_POST['device_name'];
            $position=$_POST['position'];
            $code_period=$_POST['code_period'];
            $min_ok=$_POST['min_ok'];
            $max_ok=$_POST['max_ok'];

            if(!isset($_POST['armed']))
            { $armed=0; }
            else
            { $armed=1; }

            if ((preg_match("/^[a-zA-Z0-9_ ]+$/", $device_name)) and (preg_match("/^[a-zA-Z0-9_ ]+$/", $position)) and (preg_match("/^-?[0-9]{1,3}+$/", $min_ok)) and (preg_match("/^-?[0-9]{1,3}+$/", $max_ok)))
            {
              $sql = "UPDATE devices set device_name='$device_name', position='$position', code_period='$code_period', min_ok='$min_ok', max_ok='$max_ok', armed='$armed' where serial='$serial'";
              $result = $conn->query($sql);
            }

            $serial="";
            $device_name="";
            $position="";
            $min_ok="";
            $max_ok="";
            $armed="";
            $code_period="";
          }
          // END self_reload

          $query = "SELECT idUtente,t0,t1,t2,t3 FROM utenti WHERE codUtente='$COD_UTENTE'";
          $result = $conn->query($query);
          while($row = $result->fetch_assoc()) {
            $idUtente = $row["idUtente"];
            $tenant0 = $row["t0"];
            $tenant1 = $row["t1"];
            $tenant2 = $row["t2"];
            $tenant3 = $row["t3"];
          }

          $sql = "SELECT serial, device_name, position, min_ok, max_ok, armed, code_period FROM devices where tenant in ($tenant0,$tenant1,$tenant2,$tenant3) order by serial";
          $result = $conn->query($sql);
          if ($result->num_rows > 0) {
            $x=0;
            while($row = $result->fetch_assoc()) {
              $serial[$x] = $row["serial"];
              $device_name[$x] = $row["device_name"];
              $position[$x] = $row["position"];
              $min_ok[$x] = $row["min_ok"];
              $max_ok[$x] = $row["max_ok"];
              $armed[$x] = $row["armed"];
              $code_period[$x] = $row["code_period"];
              ++$x;
            }

            echo "<div class=\"modal-content device_settings\">";
            echo "<table class=\"centered device_settings\">\n";
            echo "<tr><th>Seriale</th><th>Nome hooly</th><th>Posizione</th><th>Rilevamento</th>";
            echo "<th>Min</th><th>Max</th><th>Notifica</th></tr>\n";

            for($x=0;$x<$result->num_rows;$x++) {
              echo "<form action =\"" . $_SERVER['PHP_SELF'] . "\" method=\"POST\">";
              echo "<TR>";
              echo "<TD>" . $serial[$x] . "</TD>\n";
              echo "<input type=\"hidden\" name=\"serial\" value=\"" . $serial[$x] . "\">\n";
              echo "<TD><input type=\"text\" class=\"slim\" name=\"device_name\" value=\"" . $device_name[$x] . "\" size=15 onchange=\"this.form.submit()\"></TD>\n";
              echo "<TD><input type=\"text\" class=\"slim\" name=\"position\" value=\"" . $position[$x] . "\" size=15 onchange=\"this.form.submit()\"></TD>\n";
              echo "<TD><select class=\"slim\" name=\"code_period\" onchange=\"this.form.submit()\">\n";

              echo "<option value= \"1\"";
              if ($code_period[$x] == 1) { echo " selected";}
              echo "> 5 sec. </option>\n";

              echo "<option value= \"2\"";
              if ($code_period[$x] == 2) { echo " selected";}
              echo "> 15 sec. </option>\n";

              echo "<option value= \"3\"";
              if ($code_period[$x] == 3) { echo " selected";}
              echo "> 30 sec. </option>\n";

              echo "<option value= \"4\"";
              if ($code_period[$x] == 4) { echo " selected";}
              echo "> 1 min. </option>\n";

              echo "<option value= \"5\"";
              if ($code_period[$x] == 5) { echo " selected";}
              echo "> 3 min. </option>\n";

              echo "<option value= \"6\"";
              if ($code_period[$x] == 6) { echo " selected";}
              echo "> 5 min. </option>\n";

              echo "<option value= \"7\"";
              if ($code_period[$x] == 7) { echo " selected";}
              echo "> 15 min. </option>\n";

              echo "<option value= \"8\"";
              if ($code_period[$x] == 8) { echo " selected";}
              echo "> 30 min. </option>\n";

              echo "<option value= \"9\"";
              if ($code_period[$x] == 9) { echo " selected";}
              echo "> 60 min. </option>\n";
              echo "</select>";
              echo "<TD><select class=\"slim\" name=\"min_ok\" onchange=\"this.form.submit()\">\n";
              for ($i = -40; $i <= 80; $i++) {
                echo "<option value= \"$i\"";
                if ($min_ok[$x] == $i) { echo " selected";}
                echo ">$i °C</option>\n";
              }
              echo "</select>";

              echo "<TD><select class=\"slim\" name=\"max_ok\" onchange=\"this.form.submit()\">\n";
              for ($i = -40; $i <= 80; $i++) {
                echo "<option value= \"$i\"";
                if ($max_ok[$x] == $i) { echo " selected";}
                echo ">$i °C</option>\n";
              }
              echo "</select>";

              if ($armed[$x] == 1)
              {
                echo "<TD><input name=\"armed\" type=checkbox value=\"1\" checked=\"checked\" onchange=\"this.form.submit()\"></TD>\n";
              }
              else
              {
                echo "<TD><input name=\"armed\" type=checkbox value=\"1\" onchange=\"this.form.submit()\"></TD>\n";
              }
              echo "</TR>\n";
              echo "</form>\n";
            }
            echo "</table>\n";

          } else {
            echo "0 results";
          }

          $conn->close();
          ?>
          <br><br>
          <button id=btn1 class=graybtn>Click per aprire o chiudere le preferenze avanzate</button>
        </div>
        <br><br>

        <div id=advanced_preferences style=display:none;>

          <div class=modal-content style=margin:1% auto 1% auto;>
            <form action="add_sensor.php" method="post">
              <br>
              Seriale: <input type="text" class="slim" name="serial" size="6" maxlength="4">
              Pin: <input type="text" class="slim" name="pin" size="6" maxlength="4">
              <input type="hidden" name="idUtente" value="<?php echo $idUtente; ?>">
              <input type="hidden" name="tenant" value="<?php echo $tenant0; ?>">
              <br>
              <button type="submit" class=greenbtn>Aggiungi un sensore</button>
            </form>
          </div>

          <div class=modal-content style=margin:1% auto 1% auto;>
            <form action="change_pwd.php" onsubmit="return checkPassword()" method="post">
              <br>
              Password:
              <br>
              <input type="password" style="width:100%" placeholder="Enter Password" name="password" id="password" pattern="[A-Za-z0-9]{5,12}" title="La passowrd può contenere lettere e numeri, un minimo di 5 ed un massimo di 12 caratteri alfanumerici" required>
              <br> <br>
              Ripeti la Password:
              <br>
              <input type="password" style="width:100%" placeholder="Repeat Password" name="psw-repeat" id="confirm_password" required>
              <input name="act" type="hidden" value="registrazione">
              <br>
              <button type="submit" class=greenbtn>Cambia la tua password</button>
            </form>
          </div>

          <div class=modal-content style=margin:1% auto 1% auto;>
            <form action="manage_preferences.php" method="post">
              <br>
              <table>
                <tr><td>Ragione Sociale: </td><td><input type="text" class="slim" name="ragione_sociale"></td></tr>
                <tr><td>Indirizzo 1: </td><td><input type="text" class="slim" name="indirizzo1"></td></tr>
                <tr><td>Indirizzo 2: </td><td><input type="text" class="slim" name="indirizzo2"></td></tr>
                <tr><td>CAP: </td><td><input type="text" class="slim" name="cap"></td></tr>
                <tr><td>Città: </td><td><input type="text" class="slim" name="citta"></td></tr>
                <tr><td>Telefono: </td><td><input type="text" class="slim" name="telefono"></td></tr>
              </table>
              <br>
              <button type="submit" class=greenbtn>Conferma</button>
              <input type="hidden" name="idUtente" value="<?php echo $idUtente; ?>">
            </form>
          </div>

          <div class=modal-content style=margin:1% auto 1% auto;>
            <form action="manage_preferences.php" method="post">
              <br><br>Desidero ricevere le notifiche tramite:<br><br>
              <table>
                <tr><td><input type="checkbox" name="notif_telegram" value="1">Telegram </td><td></td><td>
                  ChatId:</td><td> <input type="text" class=slim name="notif_teleg_chatid" value=<?php echo $user_chatid ?> ></td>
                </tr>
                <tr><td><input type="checkbox" name="notif_pushbullett" value="1">Pushbullett</td><td>&nbsp&nbsp&nbsp</td><td>
                  Addr:</td><td> <input type="text" class=slim name="notif_pushb_email" value=<?php echo $user_email ?> ></td>
                </tr>
                <tr><td><input type="checkbox" name="notif_mail" value="1">Email</td><td></td><td>
                  Addr:</td><td> <input type="text" class=slim name="notif_email_addr" value=<?php echo $user_email ?> ></td>
                </tr>
                <tr><td><input type="checkbox" name="notif_whatsapp" value="1">WhatsApp</td><td>&nbsp</td><td>
                  #Tel:</td><td> <input type="text" class=slim name="notif_whatsapp_tel" value=<?php echo $user_email ?> ></td>
                </tr>
              </table>
              <br>
              <input type="hidden" name="idUtente" value="<?php echo $idUtente; ?>">
              <button type="submit" class=greenbtn>Conferma</button>
            </form>
          </div>

        </div>
      </body>
