<?php
include_once "./config.php";
include_once INCLUDE_GLOB_SCRIPTS_PATCH."manager.php";
$manager->canEditHeader = FALSE;
define('GALLERY', '');
define('IMAGE', '');

    if (isset($_GET["img"])) {
        $img = $_GET["img"];
    }

    $dir_name = dirname($img)."/";
    $file_name = basename($img);
    $manager->image($dir_name, $file_name,'thumb');
    $manager->image($dir_name, $file_name,'preview');
    $url =  $manager->url_string($dir_name, $file_name);
?>
    <div class="thumbimgbox"><?php echo "<img class='thumb' alt='' src='./index.php?cmd=thumb&sfpg=$url'/>"; ?></div>
    <?php
        $lim = 20;
        if(strlen($file_name) > $lim) {
            $display_name = substr($file_name,0,$lim)."...";
        } else {
            $display_name = $file_name;
        }
        echo $display_name;
    ?>



