<fieldset>
    <legend><h2>Trade show detail</h2></legend>
<form name="event-form" method="post">
    <input type="hidden"  name="event_edit_name" value="<?php echo basename($event->fileName, ".xml"); ?>"/>
    <div class="row">
        <label><?php echo XML_KEY_NAME_CAPTION; ?></label>
        <input  type="text" value="<?php echo $event->name; ?>" name="<?php echo XML_KEY_NAME; ?>"/>
    </div>
    <fieldset class="fieldset2">
        <legend><?php echo XML_KEY_WHEN_CAPTION; ?></legend>
        <textarea class="listbox-check-values" name="<?php echo XML_KEY_WHEN; ?>"><?php echo $event->when; ?></textarea>
        <?php echo XML_KEY_WHEN_NOTE_CAPTION; ?>
        <textarea name="<?php echo XML_KEY_WHEN_NOTE; ?>"><?php echo $event->when_note; ?></textarea>
    </fieldset>

    <fieldset class="fieldset2">
        <legend><?php echo XML_KEY_WHAT_CAPTION; ?></legend>
        <textarea class="listbox-check-values" name="<?php echo XML_KEY_WHAT; ?>"><?php echo $event->what; ?></textarea>
        <?php echo XML_KEY_WHAT_NOTE_CAPTION; ?>
        <textarea  name="<?php echo XML_KEY_WHAT_NOTE; ?>"><?php echo $event->what_note; ?></textarea>
    </fieldset>

    <div class="row">
        <label><?php echo XML_KEY_CONFIRMATION_TEXT_CAPTION; ?></label>
        <textarea  name="<?php echo XML_KEY_CONFIRMATION_TEXT; ?>"><?php echo $event->confirmation_text; ?></textarea>
    </div>
    <div class="row">
        <label><?php echo XML_KEY_ACCEPT_EMAIL_SUBJECT_CAPTION; ?></label>
        <input  type="text" value="<?php echo $event->accept_email_subject; ?>" name="<?php echo XML_KEY_ACCEPT_EMAIL_SUBJECT; ?>"/>
    </div>
    <div class="row">
        <label><?php echo XML_KEY_ACCEPT_EMAIL_TEXT_CAPTION; ?></label>
        <textarea  name="<?php echo XML_KEY_ACCEPT_EMAIL_TEXT; ?>"><?php echo $event->accept_email_text; ?></textarea>
    </div>
    <div class="row">
        <label><?php echo XML_KEY_REJECT_EMAIL_SUBJECT_CAPTION; ?></label>
        <input  type="text" value="<?php echo $event->reject_email_subject; ?>" name="<?php echo XML_KEY_REJECT_EMAIL_SUBJECT; ?>"/>
    </div>
    <div class="row">
        <label><?php echo XML_KEY_REJECT_EMAIL_TEXT_CAPTION; ?></label>
        <textarea  name="<?php echo XML_KEY_REJECT_EMAIL_TEXT; ?>"><?php echo $event->reject_email_text; ?></textarea>
    </div>
    <div class="row">
        <label><?php echo XML_KEY_INVOICE_EMAIL_SUBJECT_CAPTION; ?></label>
        <input  type="text" value="<?php echo $event->invoice_email_subject; ?>" name="<?php echo XML_KEY_INVOICE_EMAIL_SUBJECT; ?>"/>
    </div>
    <div class="row">
        <label><?php echo XML_KEY_INVOICE_EMAIL_TEXT_CAPTION; ?></label>
        <textarea  name="<?php echo XML_KEY_INVOICE_EMAIL_TEXT; ?>"><?php echo $event->invoice_email_text; ?></textarea>
    </div>
    <div class="row" style="text-align: center">
        <input  type="submit" value="Save"/>
    </div>
</form>
</fieldset>


