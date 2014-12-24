<?php
session_start();
require_once dirname(__FILE__)."/config.php";
include_once INCLUDE_SCRIPTS_PATCH."manager.php";
require_once INCLUDE_LIBS_PATCH."mailer_lib/mailer_lib.php";
if (isset($_POST['rndval'])) {
    unset($_POST['rndval']);
}
if (isset($_POST['eid']) && isset($_POST['uid'])&& isset($_POST['fid'])&& isset($_POST['action'])) {
    $file_sufix = str_replace($_POST['eid']."_","",$_POST['fid']);
    $member = $manager->getMemberByEventAndId($_POST['eid'], $_POST['uid'], $file_sufix);
    $move_file = $_POST['action'];
    $row_class = $_POST['uid'];
    unset($_POST['eid'], $_POST['uid'], $_POST['fid'], $_POST['action']);
}

if (isset($_POST['MailAdres*'])) {
    $res = MailOut("NL", ADMIN_EMAIL, false, "", true, "", 20, 0);
    if ($res == TRUE) {
        $member->moveTo($move_file);
        ?>
        <input  type="hidden" value="1" name="remove-row-<?php echo $row_class; ?>" id="remove-row-<?php echo $row_class; ?>" />
        <?php
    } else {
        echo "<div class='error'>" . $res . "</div>";
    }
}