<?php
    require_once dirname(__FILE__)."/config.php";
    include_once INCLUDE_SCRIPTS_PATCH."manager.php";
    $manager->buildProcessForm($_GET['event_name']);
