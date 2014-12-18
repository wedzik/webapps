<?php
session_start();
include_once "./config.php";
include_once INCLUDE_GLOB_SCRIPTS_PATCH."manager.php";

$get_set = FALSE;
if (isset($_GET['sfpg'])) {
    $get = $manager->url_decode($_GET['sfpg']);
    if ($get) {
        define('GALLERY', $get[0]);
        define('IMAGE', $get[1]);
        $get_set = TRUE;
    }
}
if (!$get_set) {
    define('GALLERY', '');
    define('IMAGE', '');
}
$manager->processLogIn();

if (isset($_GET['cmd'])) {
    if ($get_set) {
        if ($_GET['cmd'] == 'thumb') {
            $manager->image(GALLERY, IMAGE, 'thumb');
            exit;
        }
        if ($_GET['cmd'] == 'image') {
            $manager->image(GALLERY, IMAGE, 'image');
            exit;
        }
        if ($_GET['cmd'] == 'preview') {
            $manager->image(GALLERY, IMAGE, 'preview');
            exit;
        }
        if (($_GET['cmd'] == 'dl') and TEXT_DOWNLOAD!='') {
            $manager->image(GALLERY, IMAGE, 'image', TRUE);
            exit;
        }
        if ($_GET['cmd'] == 'file') {
            header('Location: '.GALLERY_ROOT.GALLERY.IMAGE);
            exit;
        }
    }
}
   list($dirs, $images, $files, $misc) = $manager->get_dir(GALLERY);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"><html><head>
<link rel="stylesheet" type="text/css" href="<?php echo INCLUDE_CSS_PATCH; ?>style.css">
<meta http-equiv="Content-Type" content="text/html;charset='.CHARSET.'"><title><?php echo TEXT_GALLERY_NAME; ?></title>
    <?php $manager->load_javascript(); ?>
    <script src="<?php echo INCLUDE_JS_PATCH ?>scripts.js" type="text/javascript"></script>
</head>
<body onresize="initDisplay()" onload="showGallery(<?php echo (IMAGE_ID_IN_URL?IMAGE_ID_IN_URL:'false'); ?>)" class="sfpg">
<?php
    if(!$manager->haveAcess){
        include_once INCLUDE_GLOB_SCRIPTS_PATCH."login.php";
    } else {
?>
        <div id="box_navi" class="box_navi">
            <table class="sfpg_disp" cellspacing="0">
                <tr> <td class="navi"> <div id="navi"></div> </td> </tr>
                <tr> <td class="menu"> <div id="div_menu"></div> </td> </tr>
            </table>
        </div>
        <div id="box_image" class="box_image">
            <table class="sfpg_disp" cellspacing="0">
                <tr> <td class="mid"> <img alt="" src="" id="full" class="full_image" onclick="closeImageView()"> </td> </tr>
            </table>
        </div>
        <div id="box_wait" class="box_wait">
            <table class="sfpg_disp" cellspacing="0">
                <tr> <td class="mid"> <div id="wait"></div> </td> </tr>
            </table>
        </div>
        <div id="box_info" class="box_info">
            <table class="info" cellspacing="0">
                <tr> <td> <div id="box_inner_info"></div> </td> </tr>
            </table>
        </div>
        <div id="box_gallery" class="box_gallery"></div>
        <div id="box_overlay" class="box_overlay"></div>
<?php } ?>
</body>
</html>