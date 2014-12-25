<?php
require_once dirname(__FILE__)."/config.php";
require_once INCLUDE_LIBS_PATCH."mailer_lib/mailer_lib.php";
include_once INCLUDE_SCRIPTS_PATCH . "manager.php";
include_once dirname(__FILE__)."/view-pdf.php";

if (isset($_POST['rndval'])) {
    unset($_POST['rndval']);
}

if (isset($_POST['eid']) && isset($_POST['uid'])&& isset($_POST['fid'])) {
    $file_sufix = str_replace($_POST['eid']."_","",$_POST['fid']);
    $member = $manager->getMemberByEventAndId($_POST['eid'], $_POST['uid'], $file_sufix);
    $move_file = INVOICE_MEMBERS_FILE;
    unset($_POST['fid']);
}

if (isset($_POST["eid"]) && isset($_POST["uid"])) {
    $member = $manager->getMemberByEventAndId($_POST["eid"], $_POST["uid"],  $file_sufix);
    $row_class = $_POST['uid'];
    unset($_POST['eid'], $_POST['uid']);

    if ($member !== false) {
        $invoice = new pdfIvoice();
        $fileName = $invoice->printPdf($member);
        if (file_exists(ORDERS_PATCH . $fileName)) {
            $_FILES['AtachedFile']['name'] = $fileName;
            $_FILES['AtachedFile']['tmp_name'] = ORDERS_PATCH . $fileName;
            $_FILES['AtachedFile']['error'] = 0;
            $_FILES['AtachedFile']['size'] = filesize(ORDERS_PATCH . $fileName);
        }
    }
    if (isset($_POST['MailAdres*'])) {
        $destEmail = $_POST['MailAdres*'];
        $_POST['MailAdres*'] = ADMIN_EMAIL;
        $res = MailOut("NL", $destEmail, false, "", true, "", 20, 0);
        if ($res == TRUE) {
            $member->moveTo($move_file);
            ?>
            <input  type="hidden" value="1" name="remove-row-<?php echo $row_class; ?>" id="remove-row-<?php echo $row_class; ?>" />
        <?php
        } else {
            echo "<div class='error'>" . $res . "</div>";
        }
    }
}

