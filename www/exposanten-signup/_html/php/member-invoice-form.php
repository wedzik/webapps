<div id="member-invoice-window" class="modal_window modal_window_bg">
    <div class="content content-border"  style="background-color: #F7FCF3">
        <h1>Invoice</h1>
        <div class="gray_close">
            <a class="close_link" onclick="$('#lean_overlay').fadeOut(200); $('#member-invoice-window').fadeOut(200);
                     $('#invoice-mail-desk').fadeIn();
                   $('#invoice-mail-resp').fadeOut();
             clearMemberInvoiceFormDetail();"></a>
        </div>
        <div class="page_layout">
            <div id="member-form-container">
                <div class="member-form-content" id="invoice-mail-desk">
                    <input type="hidden" name="event_name" value="<?php echo $event->name; ?>"/>
                    <fieldset>
                        <legend>Member detail</legend>
                        <div class="form_filds">
                            <div class="form_filds_container" style="height: auto;">
                                <label class="form_label"><?php echo LABEL_COMPANY_NAME; ?></label>
                                <span class="field_value" id="member-invoice-form-company-name"></span>
                            </div>
                            <div class="form_filds_container" style="height: auto;">
                                <label class="form_label"><?php echo LABEL_CONTACT_NAME; ?></label>
                                <span class="field_value" id="member-invoice-form-contact-name"></span>
                            </div>
                            <div class="form_filds_container" style="height: auto;">
                                <label class="form_label"><?php echo LABEL_ADDRESS; ?></label>
                                <span class="field_value" id="member-invoice-form-address"></span>
                            </div>
                            <div class="form_filds_container" style="height: auto;">
                                <label class="form_label"><?php echo LABEL_PHONE; ?></label>
                                <span class="field_value" id="member-invoice-form-phone"></span>
                            </div>
                            <div class="form_filds_container" style="height: auto;">
                                <label class="form_label"><?php echo LABEL_EMAIL; ?></label>
                                <span class="field_value" id="member-invoice-form-email"></span>
                            </div>
                            <div class="form_filds_container" style="height: auto;">
                                <label class="form_label"><?php echo LABEL_WEBSITE; ?></label>
                                <span class="field_value" id="member-invoice-form-wedsite"></span>
                            </div>
                            <div class="form_filds_container" style="height: auto;">
                                <label class="form_label"><?php echo LABEL_INDUSTRY; ?></label>
                                <span class="field_value" id="member-invoice-form-industry"></span>
                            </div>
                            <div class="form_filds_container" style="height: auto;">
                                <label class="form_label"><?php echo LABEL_PRODUCTS; ?></label>
                                <span class="field_value" id="member-invoice-form-products"></span>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend><?php echo STEP_1_HEADER; ?></legend>
                        <span class="field_value" id="member-invoice-form-when"></span>
                    </fieldset>
                    <fieldset>
                        <legend><?php echo STEP_2_HEADER; ?></legend>
                        <span class="field_value" id="member-invoice-form-what"></span>
                    </fieldset>
                    <fieldset>
                        <legend>Email</legend>
                        <form lang="nl" name="InvoiceForm"  id="InvoiceForm"  action="./invoice_form.php"
                              onsubmit=" return domSubmit(this, 'InvoiceForm');" method="post" enctype="multipart/form-data">
                            <input  type="hidden" value="" name="eid" id="member-invoice-form-eid" />
                            <input  type="hidden" value="" name="uid" id="member-invoice-form-uid" />
                            <input  type="hidden" value="" name="fid" id="member-invoice-form-fid" />
                            <div class="row">
                                <label>Email</label>
                                <input  type="text" email="true" value="" name="MailAdres*" id="member-invoice-form-email-address"/>
                            </div>
                            <div class="row">
                                <label>Subject</label>
                                <input  type="text" value="" name="Subject*" id="member-invoice-form-email-subject"/>
                            </div>
                            <div class="row">
                                <label>Message</label>
                                <textarea  name="Message*"  id="member-invoice-form-email-message"></textarea>
                            </div>
                            <div class="row" style="text-align: center">
                                <input type="button" id=invoice-form-submit-button"
                                       class="submit-button"
                                       value="Send"
                                       onclick=" if (domSubmit(this, 'InvoiceForm')) {
                                                           InvoiceFormObj.submit();
                                                           AddLoadingBar('invoice-mail-resp-text');
                                                           $('#invoice-mail-desk').fadeOut(1);
                                                           $('#invoice-mail-resp').fadeIn();
                                                       }
                                                       ">
                                <a id="member-show-invoice-link" href="#" class="link_button invoice_button" target="_blank">Show Invoice</a>
                            </div>
                        </form>
                    </fieldset>
                </div>

                <div id="invoice-mail-resp" style="display: none; text-align: center; "  class="member-form-content">
                    <fieldset>
                    <div id="invoice-mail-resp-text" style="text-align: justify">
                        test content
                        <div class="loading"></div>
                    </div>
                    </fieldset>
                    <a id="GoBackInvoiceFormButton" class="link_button">Terug</a>
                </div>


            </div>
        </div>
    </div>
</div>
<script language="JavaScript">
    var InvoiceFormObj = new DHTMLSuite.form({formRef: 'InvoiceForm', action: './invoice_form.php', responseEl: 'invoice-mail-resp-text'});

    $("#GoBackInvoiceFormButton").click(function() {
        $('#invoice-mail-resp').fadeOut(1);
        $('#invoice-mail-desk').fadeIn();
    });

    $(document).ready(function(){
        $('#member-invoice-window').css({'height':($(document).height())+'px'});
        $(window).resize(function(){
            $('#member-invoice-window').css({'height':($(document).height())+'px'});
        });
    });

        function setMemberInvoiceFormDetail($company_name, $contact_name, $address, $phone, $email, $website, $industry, $products,
                                            $email_subject, $email_message, $when, $what,  $eid, $uid, $fid) {
        $('#member-invoice-form-company-name').html($company_name);
        $('#member-invoice-form-contact-name').html($contact_name);
        $('#member-invoice-form-address').html($address);
        $('#member-invoice-form-phone').html($phone);
        $('#member-invoice-form-email').html($email);
        $('#member-invoice-form-wedsite').html($website);
        $('#member-invoice-form-industry').html($industry);
        $('#member-invoice-form-products').html($products);
        $('#member-invoice-form-email-address').val($email);
        $('#member-invoice-form-email-subject').val($email_subject);
        $('#member-invoice-form-email-message').html($email_message);
        $('#member-invoice-form-when').html($when);
        $('#member-invoice-form-what').html($what);
        $('#member-invoice-form-eid').val($eid);
        $('#member-invoice-form-uid').val($uid);
        $('#member-invoice-form-fid').val($fid);
        $('#member-show-invoice-link').prop('href','./view-pdf.php?eid='+$eid+'&uid='+$uid+'&fid='+$fid);



    }
    function clearMemberInvoiceFormDetail() {
        $('#member-invoice-form-company-name').html("");
        $('#member-invoice-form-contact-name').html("");
        $('#member-invoice-form-address').html("");
        $('#member-invoice-form-phone').html("");
        $('#member-invoice-form-email').html("");
        $('#member-invoice-form-wedsite').html("");
        $('#member-invoice-form-industry').html("");
        $('#member-invoice-form-products').html("");
        $('#member-invoice-form-email-address').val("");
        $('#member-invoice-form-email-subject').val("");
        $('#member-invoice-form-email-message').html("");
        $('#member-invoice-form-when').html("");
        $('#member-invoice-form-what').html("");
        $('#member-invoice-form-eid').val("");
        $('#member-invoice-form-fid').val("");
        $('#member-show-invoice-link').prop('href','');
        if ($('#remove-row-'+$('#member-invoice-form-uid').val()).val() == 1){
            $('.'+$('#member-invoice-form-uid').val()).hide();
        }
        $('#member-invoice-form-uid').val("");
    }
</script>