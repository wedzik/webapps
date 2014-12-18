<?php
/* The default execution time limit skript set to  20 sec.
 * You can change this by specifying param ege:
 * cron.php?time=30 set time limit to 30 sec
 */
include_once "./config.php";
include_once INCLUDE_GLOB_SCRIPTS_PATCH."manager.php";
$manager->canEditHeader = FALSE;
define('GALLERY', '');
define('IMAGE', '');
$timeLimit = 20;
if (isset($_GET["time"])) {
    $timeLimit = intval($_GET["time"]);
}
echo "Time limit= ".$timeLimit."<br/>";
$start = microtime(true);

$images = $manager->cron_dir();
foreach($images as $img){
    $thumb_file = DATA_ABSOLUTE_ROOT."thumb/".$img;
    $preview_file = DATA_ABSOLUTE_ROOT."preview/".$img;
    $thumb_file = str_replace("//","/", $thumb_file);
    $preview_file = str_replace("//","/", $preview_file);

    if(!file_exists($thumb_file)) {
        $manager->image(dirname($img)."/", basename($img),'thumb');
        $time_elapsed_us = microtime(true) - $start;
        echo $img." => "."<span style='color:blue'>".$thumb_file . "</span> ".$time_elapsed_us."<br/>";
        if($time_elapsed_us > $timeLimit) {
            echo "<b>Stop byt time limit</b>";
            break;
        }
    }
    if(!file_exists($preview_file)) {
        $manager->image(dirname($img)."/", basename($img),'preview');
        $time_elapsed_us = microtime(true) - $start;
        echo $img." => "."<span style='color:darkviolet'>".$preview_file . "</span> ".$time_elapsed_us."<br/>";
        if($time_elapsed_us > $timeLimit) {
            echo "<b>Stop byt time limit</b>";
            break;
        }
    }
}

