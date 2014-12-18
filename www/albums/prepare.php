<?php
session_start();
include_once "./config.php";
include_once INCLUDE_GLOB_SCRIPTS_PATCH."manager.php";
$manager->processPreaprePageLogIn();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"><html><head>
<link rel="stylesheet" type="text/css" href="<?php echo INCLUDE_CSS_PATCH; ?>style.css">
<meta http-equiv="Content-Type" content="text/html;charset='.CHARSET.'"><title><?php echo TEXT_GALLERY_NAME; ?></title>
    <?php //$manager->load_javascript(); ?>
    <script src="<?php echo INCLUDE_JS_PATCH ?>scripts.js" type="text/javascript"></script>
    <script src="<?php echo INCLUDE_JS_PATCH ?>jquery.min.js" type="text/javascript"></script>
    <script src="<?php echo INCLUDE_JS_PATCH ?>jquery-ui.min.js" type="text/javascript"></script>
    <script language="JavaScript">
        $max_request = 4;
        $request = $max_request;
        $request_count = 0;
        $recal_count = 0
        function image_ajax_load(img_name, content_id) {
            if(( $request >= $max_request )&&($recal_count < 1000) ){
                setTimeout(function(){image_ajax_load(img_name, content_id)},1000)
                $recal_count = $recal_count +1;
            } else {
                $recal_count = 0;
                $request = $request + 1;
                $("#content_" + content_id).html("<div class='prepare_loading'></div>");
                $.ajax({
                    url: "./image_load.php?img=" + img_name,
                    cache: false,
                    success: function (html) {
                        $("#content_" + content_id).html(html);
                        $request = $request - 1;
                        $request_count = $request_count - 1;
                        if($request_count <= 0) {
                            alert("Gallery preparing completed");
                        }
                    }
                });
            }
        }
    </script>
</head>
<body class="sfpg prepare">
    <div class="box_preload_gallery" style="text-align: center;">
    <?php
        if(!$manager->haveAcess){
            include_once INCLUDE_GLOB_SCRIPTS_PATCH."prepare_page_login.php";
        } else {
            $images = $manager->cron_dir();
            $count = 0;
            foreach($images as $img){
                $thumb_file = DATA_ROOT."thumb/".$img;
                $preview_file = DATA_ROOT."preview/".$img;
                $thumb_file = str_replace("//","/", $thumb_file);
                $preview_file = str_replace("//","/", $preview_file);
                $last = 0;
                if ($img === end($images)) {
                    $last = 1;
                }
                if(!file_exists($thumb_file) || !file_exists($preview_file)) {
                    ?>
                    <div class="thumbbox">
                        <div class="innerboximg">
                            <div id="content_<?php echo $count; ?>">

                            </div>
                            <script language="JavaScript">
                                $request_count = $request_count +1;
                                image_ajax_load("<?php echo $img; ?>", <?php echo $count; ?>);
                            </script>
                        </div>
                    </div>
                    <?php
                    $count++;
                }
            }
        }
    ?>
    </div>
    <script type="text/javascript">
        $request = 0;
    </script>
</body>