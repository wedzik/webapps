<div id="member-accept-window" class="modal_window modal_window_bg">
    <div class="content content-border">
        <div class="gray_close">
            <a class="close_link" onclick="$('#lean_overlay').fadeOut(200); $('#member-accept-window').fadeOut(200); clearMemberFormDetail();"></a>
        </div>
        <div class="page_layout">
            <div id="member-form-container">
                <div class="member-form-content" id="member-form-content">
                    <input type="hidden" name="event_name" value="<?php echo $event->name; ?>"/>
                    <fieldset>
                        <legend>Member detail</legend>
                        <div class="form_filds">
                            <div class="form_filds_container" style="height: auto;">
                                <label class="form_label"><?php echo LABEL_COMPANY_NAME; ?></label>
                                <span class="field_value" id="member-form-company-name"></span>
                            </div>
                            <div class="form_filds_container" style="height: auto;">
                                <label class="form_label"><?php echo LABEL_CONTACT_NAME; ?></label>
                                <span class="field_value" id="member-form-contact-name"></span>
                            </div>
                            <div class="form_filds_container" style="height: auto;">
                                <label class="form_label"><?php echo LABEL_ADDRESS; ?></label>
                                <span class="field_value" id="member-form-address"></span>
                            </div>
                            <div class="form_filds_container" style="height: auto;">
                                <label class="form_label"><?php echo LABEL_PHONE; ?></label>
                                <span class="field_value" id="member-form-phone"></span>
                            </div>
                            <div class="form_filds_container" style="height: auto;">
                                <label class="form_label"><?php echo LABEL_EMAIL; ?></label>
                                <span class="field_value" id="member-form-email"></span>
                            </div>
                            <div class="form_filds_container" style="height: auto;">
                                <label class="form_label"><?php echo LABEL_WEBSITE; ?></label>
                                <span class="field_value" id="member-form-wedsite"></span>
                            </div>
                            <div class="form_filds_container" style="height: auto;">
                                <label class="form_label"><?php echo LABEL_INDUSTRY; ?></label>
                                <span class="field_value" id="member-form-industry"></span>
                            </div>
                            <div class="form_filds_container" style="height: auto;">
                                <label class="form_label"><?php echo LABEL_PRODUCTS; ?></label>
                                <span class="field_value" id="member-form-products"></span>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend><?php echo STEP_1_HEADER; ?></legend>
                        <span class="field_value" id="member-form-when"></span>
                    </fieldset>
                    <fieldset>
                        <legend><?php echo STEP_2_HEADER; ?></legend>
                        <span class="field_value" id="member-form-what"></span>
                    </fieldset>
                    <fieldset>
                        <legend>Email</legend>
                        <form lang="nl" name="MailForm"  id="MailForm"
                              onsubmit=" return domSubmit(this, 'MailForm');" method="post" enctype="multipart/form-data">
                            <div class="row">
                                <label>Email</label>
                                <input  type="text" email="true" value="" name="MailAdres*" id="member-form-email-address"/>
                            </div>
                            <div class="row">
                                <label>Subject</label>
                                <input  type="text" value="" name="Subject" id="member-form-email-subject"/>
                            </div>
                            <div class="row">
                                <label>Message</label>
                                <textarea  name="Message*"  id="member-form-email-message"></textarea>
                            </div>
                            <div class="row" style="text-align: center">
                                <input  type="submit" value="Send"/>
                            </div>
                        </form>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
</div>
<script language="JavaScript">
    $(document).ready(function(){
        $('#member-accept-window').css({'height':($(document).height())+'px'});
        $(window).resize(function(){
            $('#member-accept-window').css({'height':($(document).height())+'px'});
        });
    });

        function setMemberFormDetail($company_name, $contact_name, $address, $phone, $email, $website, $industry, $products,
                                 $email_subject, $email_message, $when, $what) {
        $('#member-form-company-name').html($company_name);
        $('#member-form-contact-name').html($contact_name);
        $('#member-form-address').html($address);
        $('#member-form-phone').html($phone);
        $('#member-form-email').html($email);
        $('#member-form-wedsite').html($website);
        $('#member-form-industry').html($industry);
        $('#member-form-products').html($products);

        $('#member-form-email-address').val($email);
        $('#member-form-email-subject').val($email_subject);
        $('#member-form-email-message').html($email_message);
        $('#member-form-when').html($when);
        $('#member-form-what').html($what);
    }
    function clearMemberFormDetail() {
        $('#member-form-company-name').html("");
        $('#member-form-contact-name').html("");
        $('#member-form-address').html("");
        $('#member-form-phone').html("");
        $('#member-form-email').html("");
        $('#member-form-wedsite').html("");
        $('#member-form-industry').html("");
        $('#member-form-products').html("");
        $('#member-form-email-address').val("");
        $('#member-form-email-subject').val("");
        $('#member-form-email-message').html("");
        $('#member-form-when').html("");
        $('#member-form-what').html("");
    }
</script>