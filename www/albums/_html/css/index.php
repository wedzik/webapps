<?php
    header("Location: http://".$_SERVER['HTTP_HOST']);
    /* Make sure that code below does not get executed when we redirect. */
    exit();
?>
