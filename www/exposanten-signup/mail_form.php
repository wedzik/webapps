<?php
session_start();
require_once dirname(__FILE__)."/config.php";
//include_once INCLUDE_SCRIPTS_PATCH."manager.php";
require_once INCLUDE_LIBS_PATCH."mailer_lib/mailer_lib.php";
if (isset($_POST['rndval'])) {
    unset($_POST['rndval']);
}

if (isset($_POST['MailAdres*'])) {
    $res = MailOut("NL", ADMIN_EMAIL, false, "", true, "", 20, 0);
    if ($res !== TRUE) {
        echo "<div class='error'>" . $res . "</div>";
    }
}