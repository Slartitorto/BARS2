
<link href="css/dropDownMenu.css" rel="stylesheet" type="text/css" />

<div class="hide-print">

  <ul class="top-level-menu">
    <li>
      <img src="icone/menu.png" width="40">

      <ul class="second-level-menu">
        <li><a href="status.php"><b>Stato dei sensori</b></a></li>

        <li><a href="#"><b>HACCP</b></a>
          <ul class="third-level-menu">
            <li><a href="generals.php?act=NC_insert">Inserisci non conformità</a></li>
            <li><a href="generals.php?act=NC_manage_select">Gestisci le non conformità</a></li>
            <li><a href="generals.php?act=RM_insert">Inserisci una rilevazione manuale</a></li>
            <li><a href="generals.php?act=RM_manage_select">Gestisci le rilevazioni manuali</a></li>
            <li><a href="generals.php?act=set_personalInfo">Info personali per SA-04</a></li>
            <li><a href="generals.php?act=monthly_report">Genera report mensili</a></li>
          </ul>
        </li>

        <li><a href="#"><b>Gestione sistema</b></a>
          <ul class="third-level-menu">
            <li><a href="device_settings.php">Configura i tuoi Hooly</a></li>
            <li><a href="generals.php?act=alarm_pause">Sospendi allarmi</a></li>
            <li><a href="generals.php?act=add_hooly">Aggiungi o elimina un Hooly</a></li>
            <li><a href="generals.php?act=add_router">Aggiungi un router</a></li>
            <li><a href="generals.php?act=delete_records">Elimina dati di rilevazione</a></li>
            <li><a href="generals.php?act=set_notifyMethod">Imposta notifiche</a></li>
          </ul>
        </li>
        <li><a href="#"><b>Account</b></a>
          <ul class="third-level-menu">
            <li><a href="generals.php?act=SMS_manage">Credito SMS</a></li>
            <li><a href="generals.php?act=changePwd">Cambio password</a></li>
            <li><a href="generals.php?act=set_billingInfo">Informazioni di fatturazione</a></li>
            <li><a href="generals.php?act=billing">Credito e consumi</a></li>
            <li><a href="generals.php?act=logout"><b>Logout</b></a></li>
          </ul>
        </li>
      </ul>
    </li>
  </ul>

</div>
