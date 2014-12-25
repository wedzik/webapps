<?php $this->showMessage();
?>
<fieldset>
    <legend><h2><?php echo $event_name; ?></h2></legend>
    <table class="member-table" id="<?php echo $event_name; ?>_report" style="display: none">
        <thead>
        <tr>
            <td><?php echo LABEL_COMPANY_NAME; ?></td>
            <td><?php echo LABEL_CONTACT_NAME; ?></td>
            <td><?php echo LABEL_EMAIL; ?></td>
            <td><?php echo LABEL_PHONE; ?></td>
        </tr>
        </thead>
        <tbody>
    <?php  foreach($members as $member){ ?>
        <tr class="<?php echo $member->id; ?>" >
            <td><?php echo $member->company_name; ?></td>
            <td><?php echo $member->contact_name; ?></td>
            <td><?php echo $member->email; ?></td>
            <td><?php echo $member->phone; ?></td>
        </tr>
        <tr class="<?php echo $member->id; ?>">
            <td colspan="4" class="member_detail">
                <fieldset style="float: left">
                <label class="form_label"><?php echo LABEL_COMPANY_NAME; ?></label>
                <span class="member_field_value"><?php echo $member->company_name; ?></span><br/>
                <label class="form_label"><?php echo LABEL_CONTACT_NAME; ?></label>
                <span class="member_field_value"><?php echo $member->contact_name; ?></span><br/>
                <label class="form_label"><?php echo LABEL_ADDRESS; ?></label>
                <span class="member_field_value"><?php echo $member->address; ?></span><br/>
                <label class="form_label"><?php echo LABEL_PHONE; ?></label>
                <span class="member_field_value"><?php echo $member->phone; ?></span><br/>
                <label class="form_label"><?php echo LABEL_EMAIL; ?></label>
                <span class="member_field_value"><?php echo $member->email; ?></span><br/>
                <label class="form_label"><?php echo LABEL_WEBSITE; ?></label>
                <span class="member_field_value"><?php echo $member->website; ?></span><br/>
                <label class="form_label"><?php echo LABEL_INDUSTRY; ?></label>
                <span class="member_field_value"><?php echo $member->industry; ?></span><br/>
                <label class="form_label"><?php echo LABEL_PRODUCTS; ?></label>
                <span class="member_field_value"><?php echo $member->products; ?></span><br/>
                </fieldset>
                <div style="display: block; float: right; width: 440px">
                <fieldset>
                    <legend><?php echo STEP_1_HEADER; ?></legend>
                    <?php
                        $values= explode("|", $member->when, -1);
                        $when_out = "";
                        foreach ($values as $when) {
                            $when_out .= $when."<br/>";
                        }
                        echo $when_out;
                    ?>
                </fieldset>
                <fieldset>
                    <legend><?php echo STEP_2_HEADER; ?></legend>
                    <?php
                        $values= explode("|", $member->what, -1);
                        $what_out = "";
                        foreach ($values as $what) {
                            $what_out .= $what."<br/>";
                        }
                        echo $what_out;
                    ?>
                </fieldset>
                    <fieldset>
                        <a rel="leanModal" href="#member-accept-window" class="link_button"
                            onclick=' setMemberAcceptFormDetail("<?php echo $member->company_name; ?>",
                                "<?php echo $member->contact_name; ?>", "<?php echo $member->address; ?>",
                                "<?php echo $member->phone; ?>", "<?php echo $member->email; ?>",
                                "<?php echo $member->website; ?>", "<?php echo $member->industry; ?>",
                                "<?php echo $member->products;?>", "<?php echo $event->accept_email_subject; ?>",
                                "<?php echo str_replace("\r\n","\\n", $event->accept_email_text); ?>",
                                "<?php echo $when_out; ?>","<?php echo $what_out;?>",
                                "<?php echo $member->event_name; ?>","<?php echo $member->id;?>",
                                "<?php echo $member->fileName;?>");'>Accept</a>

                        <a rel="leanModal" href="#member-reject-window" class="link_button cancel_button"
                            onclick=' setMemberRejectFormDetail("<?php echo $member->company_name; ?>",
                                "<?php echo $member->contact_name; ?>", "<?php echo $member->address; ?>",
                                "<?php echo $member->phone; ?>", "<?php echo $member->email; ?>",
                                "<?php echo $member->website; ?>", "<?php echo $member->industry; ?>",
                                "<?php echo $member->products;?>", "<?php echo $event->reject_email_subject; ?>",
                                "<?php echo str_replace("\r\n","\\n", $event->reject_email_text); ?>",
                                "<?php echo $when_out; ?>","<?php echo $what_out;?>",
                                "<?php echo $member->event_name; ?>","<?php echo $member->id;?>",
                                "<?php echo $member->fileName;?>" );'>Reject</a>

                        <a rel="leanModal" href="#member-invoice-window" class="link_button invoice_button"
                            onclick=' setMemberInvoiceFormDetail("<?php echo $member->company_name; ?>",
                                "<?php echo $member->contact_name; ?>", "<?php echo $member->address; ?>",
                                "<?php echo $member->phone; ?>", "<?php echo $member->email; ?>",
                                "<?php echo $member->website; ?>", "<?php echo $member->industry; ?>",
                                "<?php echo $member->products;?>", "<?php echo $event->invoice_email_subject; ?>",
                                "<?php echo str_replace("\r\n","\\n", $event->invoice_email_text); ?>",
                                "<?php echo $when_out; ?>","<?php echo $what_out;?>",
                                "<?php echo $member->event_name; ?>","<?php echo $member->id;?>",
                                "<?php echo $member->fileName;?>"  );'>Invoice</a>

<!--                        <a href="./view-pdf.php?eid=<?php echo $member->event_name; ?>&uid=<?php echo $member->id; ?>" class="link_button invoice_button" target="_blank">Show Invoice</a>-->
                    </fieldset>
                </div>
            </td>
        </tr>
    <?php } ?>
        </tbody>
    </table>
</fieldset>
    <script type="text/javascript">
        $(document).ready(function(){
            $("#menu a").removeClass('active')
            $("#menu-<?php echo $sufix; ?>").addClass('active')
            $("#<?php echo $event_name; ?>_report tr:odd").addClass("odd");
            $("#<?php echo $event_name; ?>_report tr:not(.odd)").hide();
            $("#<?php echo $event_name; ?>_report tr:first-child").show();
            $(".member-table").show();
            $("#<?php echo $event_name; ?>_report tr.odd").click(function(){
                $("#<?php echo $event_name; ?>_report tr:not(.odd)").hide();
                $("#<?php echo $event_name; ?>_report tr").removeClass('open_row_top')
                $("#<?php echo $event_name; ?>_report tr").removeClass('open_row_down')
                $("#<?php echo $event_name; ?>_report tr:first-child").show();
                $(this).next("tr").toggle();
                $(this).addClass('open_row_top')
                $(this).next("tr").addClass('open_row_down')
                $('#member-accept-window').css({'height':($(document).height())+'px'});
                $('#member-reject-window').css({'height':($(document).height())+'px'});
            });
        });
    </script>
<?php
    include_once INCLUDE_SCRIPTS_PATCH."member-accept-form.php";
    include_once INCLUDE_SCRIPTS_PATCH."member-reject-form.php";
    include_once INCLUDE_SCRIPTS_PATCH."member-invoice-form.php";
?>