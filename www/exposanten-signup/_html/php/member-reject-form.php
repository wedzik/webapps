<div id="member-reject-window" class="modal_window modal_window_bg">
    <div class="content content-border"  style="background-color: #FFF9F9">
        <h1>Reject</h1>
        <div class="gray_close">
            <a class="close_link" onclick="$('#lean_overlay').fadeOut(200); $('#member-reject-window').fadeOut(200);
                     $('#reject-mail-desk').fadeIn();
                   $('#reject-mail-resp').fadeOut();
             clearMemberRejectFormDetail();"></a>
        </div>
        <div class="page_layout">
            <div id="member-form-container">
                <div class="member-form-content" id="reject-mail-desk">
                    <input type="hidden" name="event_name" value="<?php echo $event->name; ?>"/>
                    <fieldset>
                        <legend>Member detail</legend>
                        <div class="form_filds">
                            <div class="form_filds_container" style="height: auto;">
                                <label class="form_label"><?php echo LABEL_COMPANY_NAME; ?></label>
                                <span class="field_value" id="member-reject-form-company-name"></span>
                            </div>
                            <div class="form_filds_container" style="height: auto;">
                                <label class="form_label"><?php echo LABEL_CONTACT_NAME; ?></label>
                                <span class="field_value" id="member-reject-form-contact-name"></span>
                            </div>
                            <div class="form_filds_container" style="height: auto;">
                                <label class="form_label"><?php echo LABEL_ADDRESS; ?></label>
                                <span class="field_value" id="member-reject-form-address"></span>
                            </div>
                            <div class="form_filds_container" style="height: auto;">
                                <label class="form_label"><?php echo LABEL_PHONE; ?></label>
                                <span class="field_value" id="member-reject-form-phone"></span>
                            </div>
                            <div class="form_filds_container" style="height: auto;">
                                <label class="form_label"><?php echo LABEL_EMAIL; ?></label>
                                <span class="field_value" id="member-reject-form-email"></span>
                            </div>
                            <div class="form_filds_container" style="height: auto;">
                                <label class="form_label"><?php echo LABEL_WEBSITE; ?></label>
                                <span class="field_value" id="member-reject-form-wedsite"></span>
                            </div>
                            <div class="form_filds_container" style="height: auto;">
                                <label class="form_label"><?php echo LABEL_INDUSTRY; ?></label>
                                <span class="field_value" id="member-reject-form-industry"></span>
                            </div>
                            <div class="form_filds_container" style="height: auto;">
                                <label class="form_label"><?php echo LABEL_PRODUCTS; ?></label>
                                <span class="field_value" id="member-reject-form-products"></span>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend><?php echo STEP_1_HEADER; ?></legend>
                        <span class="field_value" id="member-reject-form-when"></span>
                    </fieldset>
                    <fieldset>
                        <legend><?php echo STEP_2_HEADER; ?></legend>
                        <span class="field_value" id="member-reject-form-what"></span>
                    </fieldset>
                    <fieldset>
                        <legend>Email</legend>
                        <form lang="nl" name="RejectForm"  id="RejectForm"  action="./email_form.php"
                              onsubmit=" return domSubmit(this, 'RejectForm');" method="post" enctype="multipart/form-data">
                            <input  type="hidden" value="" name="eid" id="member-reject-form-eid" />
                            <input  type="hidden" value="" name="uid" id="member-reject-form-uid" />
                            <input  type="hidden" value="" name="fid" id="member-reject-form-fid" />
                            <input  type="hidden" value="<?php echo REJECT_MEMBERS_FILE; ?>" name="action" id="member-reject-form-action" />
                            <div class="row">
                                <label>Email</label>
                                <input  type="text" email="true" value="" name="MailAdres*" id="member-reject-form-email-address"/>
                            </div>
                            <div class="row">
                                <label>Subject</label>
                                <input  type="text" value="" name="Subject*" id="member-reject-form-email-subject"/>
                            </div>
                            <div class="row">
                                <label>Message</label>
                                <textarea  name="Message*"  id="member-reject-form-email-message"></textarea>
                            </div>
                            <div class="row" style="text-align: center">
                                <input type="button" id=reject-form-submit-button"
                                       class="submit-button"
                                       value="Send"
                                       onclick=" if (domSubmit(this, 'RejectForm')) {
                                                           RejectFormObj.submit();
                                                           AddLoadingBar('reject-mail-resp-text');
                                                           $('#reject-mail-desk').fadeOut(1);
                                                           $('#reject-mail-resp').fadeIn();
                                                       }
                                                       ">
                            </div>
                        </form>
                    </fieldset>
                </div>

                <div id="reject-mail-resp" style="display: none; text-align: center; "  class="member-form-content">
                    <fieldset>
                    <div id="reject-mail-resp-text"  style="text-align: justify">
                        test content
                        <div class="loading"></div>
                    </div>
                    </fieldset>
                    <a id="GoBackRejectFormButton" class="link_button">Terug</a>
                </div>


            </div>
        </div>
    </div>
</div>
<script language="JavaScript">
    var RejectFormObj = new DHTMLSuite.form({formRef: 'RejectForm', action: './mail_form.php', responseEl: 'reject-mail-resp-text'});

    $("#GoBackRejectFormButton").click(function() {
        $('#reject-mail-resp').fadeOut(1);
        $('#reject-mail-desk').fadeIn();
    });

    $(document).ready(function(){
        $('#member-reject-window').css({'height':($(document).height())+'px'});
        $(window).resize(function(){
            $('#member-reject-window').css({'height':($(document).height())+'px'});
        });
    });

        function setMemberRejectFormDetail($company_name, $contact_name, $address, $phone, $email, $website, $industry, $products,
                                           $email_subject, $email_message, $when, $what,  $eid, $uid, $fid) {
        $('#member-reject-form-company-name').html($company_name);
        $('#member-reject-form-contact-name').html($contact_name);
        $('#member-reject-form-address').html($address);
        $('#member-reject-form-phone').html($phone);
        $('#member-reject-form-email').html($email);
        $('#member-reject-form-wedsite').html($website);
        $('#member-reject-form-industry').html($industry);
        $('#member-reject-form-products').html($products);
        $('#member-reject-form-email-address').val($email);
        $('#member-reject-form-email-subject').val($email_subject);
        $('#member-reject-form-email-message').html($email_message);
        $('#member-reject-form-when').html($when);
        $('#member-reject-form-what').html($what);
        $('#member-reject-form-eid').val($eid);
        $('#member-reject-form-uid').val($uid);
        $('#member-reject-form-fid').val($fid);
    }
    function clearMemberRejectFormDetail() {
        $('#member-reject-form-company-name').html("");
        $('#member-reject-form-contact-name').html("");
        $('#member-reject-form-address').html("");
        $('#member-reject-form-phone').html("");
        $('#member-reject-form-email').html("");
        $('#member-reject-form-wedsite').html("");
        $('#member-reject-form-industry').html("");
        $('#member-reject-form-products').html("");
        $('#member-reject-form-email-address').val("");
        $('#member-reject-form-email-subject').val("");
        $('#member-reject-form-email-message').html("");
        $('#member-reject-form-when').html("");
        $('#member-reject-form-what').html("");
        $('#member-reject-form-eid').val("");
        $('#member-reject-form-fid').val("");
        if ($('#remove-row-'+$('#member-reject-form-uid').val()).val() == 1){
            $('.'+$('#member-reject-form-uid').val()).hide();
        }
        $('#member-reject-form-uid').val("");
    }
</script>