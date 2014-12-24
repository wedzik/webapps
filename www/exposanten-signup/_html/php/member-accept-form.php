<div id="member-accept-window" class="modal_window modal_window_bg">
    <div class="content content-border">
        <h1>Accept</h1>
        <div class="gray_close">
            <a class="close_link" onclick="$('#lean_overlay').fadeOut(200); $('#member-accept-window').fadeOut(200);
                     $('#accept-mail-desk').fadeIn();
                   $('#accept-mail-resp').fadeOut();
             clearMemberAcceptFormDetail();"></a>
        </div>
        <div class="page_layout">
            <div id="member-form-container">
                <div class="member-form-content" id="accept-mail-desk">
                    <fieldset>
                        <legend>Member detail</legend>
                        <div class="form_filds">
                            <div class="form_filds_container" style="height: auto;">
                                <label class="form_label"><?php echo LABEL_COMPANY_NAME; ?></label>
                                <span class="field_value" id="member-accept-form-company-name"></span>
                            </div>
                            <div class="form_filds_container" style="height: auto;">
                                <label class="form_label"><?php echo LABEL_CONTACT_NAME; ?></label>
                                <span class="field_value" id="member-accept-form-contact-name"></span>
                            </div>
                            <div class="form_filds_container" style="height: auto;">
                                <label class="form_label"><?php echo LABEL_ADDRESS; ?></label>
                                <span class="field_value" id="member-accept-form-address"></span>
                            </div>
                            <div class="form_filds_container" style="height: auto;">
                                <label class="form_label"><?php echo LABEL_PHONE; ?></label>
                                <span class="field_value" id="member-accept-form-phone"></span>
                            </div>
                            <div class="form_filds_container" style="height: auto;">
                                <label class="form_label"><?php echo LABEL_EMAIL; ?></label>
                                <span class="field_value" id="member-accept-form-email"></span>
                            </div>
                            <div class="form_filds_container" style="height: auto;">
                                <label class="form_label"><?php echo LABEL_WEBSITE; ?></label>
                                <span class="field_value" id="member-accept-form-wedsite"></span>
                            </div>
                            <div class="form_filds_container" style="height: auto;">
                                <label class="form_label"><?php echo LABEL_INDUSTRY; ?></label>
                                <span class="field_value" id="member-accept-form-industry"></span>
                            </div>
                            <div class="form_filds_container" style="height: auto;">
                                <label class="form_label"><?php echo LABEL_PRODUCTS; ?></label>
                                <span class="field_value" id="member-accept-form-products"></span>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend><?php echo STEP_1_HEADER; ?></legend>
                        <span class="field_value" id="member-accept-form-when"></span>
                    </fieldset>
                    <fieldset>
                        <legend><?php echo STEP_2_HEADER; ?></legend>
                        <span class="field_value" id="member-accept-form-what"></span>
                    </fieldset>
                    <fieldset>
                        <legend>Email</legend>
                        <form lang="nl" name="AcceptForm"  id="AcceptForm"  action="./email_form.php"
                              onsubmit=" return domSubmit(this, 'AcceptForm');" method="post" enctype="multipart/form-data">
                            <input  type="hidden" value="" name="eid" id="member-accept-form-eid" />
                            <input  type="hidden" value="" name="uid" id="member-accept-form-uid" />
                            <input  type="hidden" value="" name="fid" id="member-accept-form-fid" />
                            <input  type="hidden" value="<?php echo ACCEPT_MEMBERS_FILE; ?>" name="action" id="member-accept-form-action" />
                            <div class="row">
                                <label>Email</label>
                                <input  type="text" email="true" value="" name="MailAdres*" id="member-accept-form-email-address"/>
                            </div>
                            <div class="row">
                                <label>Subject</label>
                                <input  type="text" value="" name="Subject*" id="member-accept-form-email-subject"/>
                            </div>
                            <div class="row">
                                <label>Message</label>
                                <textarea  name="Message*"  id="member-accept-form-email-message"></textarea>
                            </div>
                            <div class="row" style="text-align: center">
                                <input type="button" id=accept-form-submit-button"
                                       class="submit-button"
                                       value="Send"
                                       onclick=" if (domSubmit(this, 'AcceptForm')) {
                                                           AcceptFormObj.submit();
                                                           AddLoadingBar('accept-mail-resp-text');
                                                           $('#accept-mail-desk').fadeOut(1);
                                                           $('#accept-mail-resp').fadeIn();
                                                       }
                                                       ">
                            </div>
                        </form>
                    </fieldset>
                </div>

                <div id="accept-mail-resp" style="display: none; text-align: center; "  class="member-form-content">
                    <fieldset>
                    <div id="accept-mail-resp-text"  style="text-align: justify">
                        test content
                        <div class="loading"></div>
                    </div>
                    </fieldset>
                    <a id="GoBackAcceptFormButton" class="link_button">Terug</a>
                </div>


            </div>
        </div>
    </div>
</div>
<script language="JavaScript">
    var AcceptFormObj = new DHTMLSuite.form({formRef: 'AcceptForm', action: './mail_form.php', responseEl: 'accept-mail-resp-text'});

    $("#GoBackAcceptFormButton").click(function() {
        $('#accept-mail-resp').fadeOut(1);
        $('#accept-mail-desk').fadeIn();
    });

    $(document).ready(function(){
        $('#member-accept-window').css({'height':($(document).height())+'px'});
        $(window).resize(function(){
            $('#member-accept-window').css({'height':($(document).height())+'px'});
        });
    });

        function setMemberAcceptFormDetail($company_name, $contact_name, $address, $phone, $email, $website, $industry, $products,
                                 $email_subject, $email_message, $when, $what,  $eid, $uid, $fid) {
        $('#member-accept-form-company-name').html($company_name);
        $('#member-accept-form-contact-name').html($contact_name);
        $('#member-accept-form-address').html($address);
        $('#member-accept-form-phone').html($phone);
        $('#member-accept-form-email').html($email);
        $('#member-accept-form-wedsite').html($website);
        $('#member-accept-form-industry').html($industry);
        $('#member-accept-form-products').html($products);
        $('#member-accept-form-email-address').val($email);
        $('#member-accept-form-email-subject').val($email_subject);
        $('#member-accept-form-email-message').html($email_message);
        $('#member-accept-form-when').html($when);
        $('#member-accept-form-what').html($what);
        $('#member-accept-form-eid').val($eid);
        $('#member-accept-form-uid').val($uid);
        $('#member-accept-form-fid').val($fid);
    }
    function clearMemberAcceptFormDetail() {
        $('#member-accept-form-company-name').html("");
        $('#member-accept-form-contact-name').html("");
        $('#member-accept-form-address').html("");
        $('#member-accept-form-phone').html("");
        $('#member-accept-form-email').html("");
        $('#member-accept-form-wedsite').html("");
        $('#member-accept-form-industry').html("");
        $('#member-accept-form-products').html("");
        $('#member-accept-form-email-address').val("");
        $('#member-accept-form-email-subject').val("");
        $('#member-accept-form-email-message').html("");
        $('#member-accept-form-when').html("");
        $('#member-accept-form-what').html("");
        $('#member-accept-form-eid').val("");
        $('#member-accept-form-fid').val("");
        if ($('#remove-row-'+$('#member-accept-form-uid').val()).val() == 1){
            $('.'+$('#member-accept-form-uid').val()).hide();
        }
        $('#member-accept-form-uid').val("");
    }
</script>