<?php
    require_once dirname(__FILE__)."/config.php";
    include_once INCLUDE_SCRIPTS_PATCH."manager.php";
?>
<link rel="stylesheet" href="<?php echo INCLUDE_CSS_PATCH ?>style_modal_window.css"/>
<script src="<?php echo INCLUDE_JS_PATCH ?>jquery-ui.min.js" type="text/javascript"></script>
<script src="<?php echo INCLUDE_JS_PATCH ?>form-validation.js" type="text/javascript"></script>
<div id="sign-up-window" class="modal_window modal_window_bg">
    <!--<div  class="modal_window_content" >-->
    <div class="content content-border">
        <div class="gray_close">
            <a class="close_link" onclick="$('#lean_overlay').fadeOut(200); $('#sign-up-window').fadeOut(200);"></a>
        </div>
        <div class="page_layout">
            <div id="process-order-container">
                <div class="process-order-content" id="process-order-content">
                    <div class="process-order-tabs process-order-tabs_step1_bg">
                        <div class="form_step_tab form_step_tab1"><?php echo STEP_1_HEADER; ?></div>
                        <div class="form_step_tab form_step_tab2"><?php echo STEP_2_HEADER; ?></div>
                        <div class="form_step_tab form_step_tab3"><?php echo STEP_3_HEADER; ?></div>
                        <div class="form_step_tab form_step_tab4"><?php echo STEP_4_HEADER; ?></div>
                    </div>
                    <div style="padding: 30px 90px 0px 90px; float: left;" id="process-form-content">
                        <?php $manager->buildProcessForm($event_name); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script language="JavaScript">
    $(function(){
        $('#sign-up-window').css({'height':($(document).height())+'px'});
        $(window).resize(function(){
            $('#sign-up-window').css({'height':($(document).height())+'px'});
        });
    });
</script>