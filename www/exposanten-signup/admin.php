<?php
session_start();
require_once dirname(__FILE__)."/config.php";
include_once INCLUDE_SCRIPTS_PATCH."manager.php";
$manager->processAdminPageLogIn();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Admin</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="<?php echo INCLUDE_CSS_PATCH ?>style.css"/>
    <link rel="stylesheet" href="<?php echo INCLUDE_CSS_PATCH ?>style_modal_window.css"/>

    <script src="<?php echo INCLUDE_JS_PATCH ?>jquery.min.js" type="text/javascript"></script>
    <script src="<?php echo INCLUDE_JS_PATCH ?>jquery-ui.min.js" type="text/javascript"></script>
    <script src="<?php echo INCLUDE_JS_PATCH ?>jquery.leanModal.min.js" type="text/javascript"></script>
    <script src="<?php echo INCLUDE_JS_PATCH ?>form-validation.js" type="text/javascript"></script>
    <script type="text/javascript"> $(function() { $('a[rel*=leanModal]').leanModal({top: 0, overlay: 0.01}); }); </script>
</head>
<body>
    <?php
        if(!$manager->haveAcess){
            include_once INCLUDE_SCRIPTS_PATCH."login-form.php";
        } else { ?>
    <div id="container">
        <div id="menu">
            <a href="admin.php">Trade show</a> &nbsp; &nbsp; &nbsp; &nbsp;
            <a href="admin.php?members=1">New Exposants</a> &nbsp; &nbsp; &nbsp; &nbsp;
        </div>
        <div id="main">
            <?php $manager->handleAdminFormRequest(); ?>
        </div>
        <div id="footer">
            <div style="float: right; margin: 0px 10px;">&copy;2014 &nbsp;<span class="separator">|</span>&nbsp; Design by <a href="/"></a></div>
        </div>
    </div>
  <?php } ?>
</html>
