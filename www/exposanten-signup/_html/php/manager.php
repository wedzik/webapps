<?php
require_once INCLUDE_LIBS_PATCH."mailer_lib/mailer_lib.php";

class Event {
    public $fileName;
    public $name = "";
    public $when = "";
    public $when_note = "";
    public $what = "";
    public $what_note = "";
    public $confirmation_text = "";
    public $accept_email_subject = "";
    public $accept_email_text = "";
    public $reject_email_subject = "";
    public $reject_email_text = "";
    public $invoice_email_subject = "";
    public $invoice_email_text = "";

    public function loadFromFile($fileName){
        $this->fileName = $fileName;
        try {
            $xml=simplexml_load_file($fileName);
            $this->name = $xml->name;
            $this->when = $xml->when;
            $this->when_note = $xml->when_note;
            $this->what = $xml->what;
            $this->what_note = $xml->what_note;
            $this->confirmation_text = $xml->confirmation_text;
            $this->accept_email_subject = $xml->accept_email_subject;
            $this->accept_email_text =  $xml->accept_email_text;
            $this->reject_email_subject = $xml->reject_email_subject;
            $this->reject_email_text = $xml->reject_email_text;
            $this->invoice_email_subject = $xml->invoice_email_subject;
            $this->invoice_email_text = $xml->invoice_email_text;
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function newEvent($name, $fileName){
        $this->name = $name;
        $this->fileName = $fileName;
    }

    public function update() {
        try {
            $this->name = strip_tags($_POST[XML_KEY_NAME]);
            $this->when = trim(strip_tags($_POST[XML_KEY_WHEN]));
            $this->when = preg_replace( "#\s*?\r?\n\s*?(?=\r\n|\n)#s" , "", $this->when);
            $this->when_note = strip_tags($_POST[XML_KEY_WHEN_NOTE]);
            $this->what = trim(strip_tags($_POST[XML_KEY_WHAT]));
            $this->what = preg_replace( "#\s*?\r?\n\s*?(?=\r\n|\n)#s" , "", $this->what);
            $this->what_note = strip_tags($_POST[XML_KEY_WHAT_NOTE]);
            $this->confirmation_text = strip_tags($_POST[XML_KEY_CONFIRMATION_TEXT]);
            $this->accept_email_subject = strip_tags($_POST[XML_KEY_ACCEPT_EMAIL_SUBJECT]);
            $this->accept_email_text = strip_tags($_POST[XML_KEY_ACCEPT_EMAIL_TEXT]);
            $this->reject_email_subject = strip_tags($_POST[XML_KEY_REJECT_EMAIL_SUBJECT]);
            $this->reject_email_text = strip_tags($_POST[XML_KEY_REJECT_EMAIL_TEXT]);
            $this->invoice_email_subject = strip_tags($_POST[XML_KEY_INVOICE_EMAIL_SUBJECT]);
            $this->invoice_email_text = strip_tags($_POST[XML_KEY_INVOICE_EMAIL_TEXT]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function save(){
        $newsXML = new SimpleXMLElement("<event></event>");
        $newsXML->addChild(XML_KEY_NAME, $this->name);
        $newsXML->addChild(XML_KEY_WHEN, $this->when);
        $newsXML->addChild(XML_KEY_WHEN_NOTE, $this->when_note);
        $newsXML->addChild(XML_KEY_WHAT, $this->what);
        $newsXML->addChild(XML_KEY_WHAT_NOTE, $this->what_note);
        $newsXML->addChild(XML_KEY_CONFIRMATION_TEXT, $this->confirmation_text);
        $newsXML->addChild(XML_KEY_ACCEPT_EMAIL_SUBJECT, $this->accept_email_subject);
        $newsXML->addChild(XML_KEY_ACCEPT_EMAIL_TEXT, $this->accept_email_text);
        $newsXML->addChild(XML_KEY_REJECT_EMAIL_SUBJECT, $this->reject_email_subject);
        $newsXML->addChild(XML_KEY_REJECT_EMAIL_TEXT, $this->reject_email_text);
        $newsXML->addChild(XML_KEY_INVOICE_EMAIL_SUBJECT, $this->invoice_email_subject);
        $newsXML->addChild(XML_KEY_INVOICE_EMAIL_TEXT, $this->invoice_email_text);
        //Header('Content-type: text/xml');
        return $newsXML->saveXML($this->fileName);
    }
}

class Member {
    public $parseOk = true;
    public $event_name = "";
    public $company_name = "";
    public $contact_name = "";
    public $address = "";
    public $phone = "";
    public $email = "";
    public $website = "";
    public $industry = "";
    public $products = "";
    public $when = "";
    public $what = "";

    function __construct($line=""){
        if($line == ""){ return; }
        $delimiter = CSV_DELIMITER;
        $values = explode($delimiter, $line);
        if(count($values) < 10) {
            $this->parseOk = false;
            return;
        }
        $this->company_name = trim($values[0]);
        $this->contact_name = trim($values[1]);
        $this->address = trim($values[2]);
        $this->phone = trim($values[3]);
        $this->email = trim($values[4]);
        $this->website = trim($values[5]);
        $this->industry = trim($values[6]);
        $this->products = trim($values[7]);
        $this->when = trim($values[8]);
        $this->what = trim($values[9]);
    }

    private function _getPostValue($filed_name, &$field){
        if(isset($_POST[$filed_name])) {
            $field = trim($_POST[$filed_name]);
            $field = strip_tags( $field);
            $rep_sym = array ("'");
            $field = str_replace( $rep_sym, '', $field);
            $field = str_replace( CSV_DELIMITER, '', $field);
            unset($_POST[$filed_name]);
            return true;
        } else {
            return false;
        }
    }

    private function _getPostArrayValue($filed_name, &$field){
        $field = "";
        if(isset($_POST[$filed_name])) {
            foreach($_POST[$filed_name] as $item){
                $item = trim($item);
                $item = strip_tags($item);
                $field .= $item."|";
                $rep_sym = array ("'");
                $field = str_replace( $rep_sym, '', $field);
            }
            unset($_POST[$filed_name]);
            return true;
        } else {
            return false;
        }
    }

    public function processForm(){
        try {
            $field_name = str_replace(" ", "_","event_name");
            $this->_getPostValue($field_name, $this->event_name);

            $field_name = str_replace(" ", "_",LABEL_COMPANY_NAME);
            $this->_getPostValue($field_name, $this->company_name);
            $this->_getPostValue($field_name."*", $this->company_name);

            $field_name = str_replace(" ", "_",LABEL_CONTACT_NAME);
            $this->_getPostValue($field_name, $this->contact_name);
            $this->_getPostValue($field_name."*", $this->contact_name);

            $field_name = str_replace(" ", "_",LABEL_ADDRESS);
            $this->_getPostValue($field_name, $this->address);
            $this->_getPostValue($field_name."*", $this->address);

            $field_name = str_replace(" ", "_",LABEL_PHONE);
            $this->_getPostValue($field_name, $this->phone);
            $this->_getPostValue($field_name."*", $this->phone);

            $field_name = str_replace(" ", "_",LABEL_EMAIL);
            $this->_getPostValue($field_name, $this->email);
            $this->_getPostValue($field_name."*", $this->email);

            $field_name = str_replace(" ", "_",LABEL_WEBSITE);
            $this->_getPostValue($field_name, $this->website);
            $this->_getPostValue($field_name."*", $this->website);

            $field_name = str_replace(" ", "_",LABEL_INDUSTRY);
            $this->_getPostValue($field_name, $this->industry);
            $this->_getPostValue($field_name."*", $this->industry);

            $field_name = str_replace(" ", "_",LABEL_PRODUCTS);
            $this->_getPostValue($field_name, $this->products);
            $this->_getPostValue($field_name."*", $this->products);

            $field_name = str_replace(" ", "_",XML_KEY_WHEN);
            $this->_getPostArrayValue($field_name, $this->when);

            $field_name = str_replace(" ", "_",XML_KEY_WHAT);
            $this->_getPostArrayValue($field_name, $this->what);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function saveNewMember(){
        try {
            $delimiter = CSV_DELIMITER;
            $new_record = $this->company_name  . $delimiter . $this->contact_name
                                                . $delimiter . $this->address
                                                . $delimiter . $this->phone
                                                . $delimiter . $this->email
                                                . $delimiter . $this->website
                                                . $delimiter . $this->industry
                                                . $delimiter . $this->products
                                                . $delimiter . $this->when
                                                . $delimiter . $this->what;
            if (!is_dir(CONFIG_MEMEBRS_PATCH)) { mkdir(CONFIG_MEMEBRS_PATCH); }
            $file_name = CONFIG_MEMEBRS_PATCH.$this->event_name."_".NEW_MEMEBRS_FILE;
            $f = fopen($file_name, 'a+');
            fwrite($f, $new_record . PHP_EOL);
            fclose($f);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function send_admin_new_member_email(){
        $space = " ";
        $delimiter = "\n";
        $message = LABEL_COMPANY_NAME . $space . $this->company_name . $delimiter .
            LABEL_CONTACT_NAME . $space . $this->contact_name . $delimiter .
            LABEL_ADDRESS . $space . $this->address . $delimiter .
            LABEL_PHONE . $space . $this->phone . $delimiter .
            LABEL_EMAIL . $space . $this->email . $delimiter .
            LABEL_WEBSITE . $space . $this->website . $delimiter .
            LABEL_INDUSTRY . $space . $this->industry . $delimiter .
            LABEL_PRODUCTS . $space . $this->products;

        //return mail( ADMIN_EMAIL , EMAIL_SUBJECT_ADMIN_NEW_MEMBER , $message);
        $_POST["MAILADRES"] =  ADMIN_EMAIL;
        $_POST["SUBJECT"] =  EMAIL_SUBJECT_ADMIN_NEW_MEMBER;
        $_POST["MESSAGE"] =  $message;

        return mailOut("EN", ADMIN_EMAIL, true, ADMIN_EMAIL, false);

    }
}

class Manager {
    public $haveAcess = false;
    private $_message = "";

    function __construct(){
        if (!is_dir(DATA_PATCH)) { mkdir(DATA_PATCH); }
        if (!is_dir(CONFIG_EVENTS_PATCH)) { mkdir(CONFIG_EVENTS_PATCH); }
    }

    private function _strip_data($text){
        $quotes = array ("\x27", "\x22", "\x60", "\t", "\n", "\r", "*", "%", "<", ">", "+");
        $text = trim( strip_tags( $text ) );
        $text = str_replace( $quotes, '', $text );
        return $text;
    }

    private function _addMessage($new_message){
        if(strlen($this->_message) > 0){
            $this->_message."<br/>".$new_message;
        } else {
            $this->_message = $new_message;
        }
    }

    private function _isAdminPageAuth(){
        if (isset($_SESSION["haveAcess"])) {
            return $_SESSION["haveAcess"] == TRUE;
        } else {
            return false;
        }
    }

    private function _generateEventFilename($event_name){
        return CONFIG_EVENTS_PATCH.$event_name.".xml";
    }

    public function processAdminPageLogIn(){
        $this->haveAcess = $this->_isAdminPageAuth();
        if (isset($_POST["password"]) && ($_POST["password"] == ADMIN_PASSWORD)) {
            $this->haveAcess = true;
            $_SESSION["haveAcess"] = TRUE;
        }
    }

    public function showMessage(){
        if(strlen($this->_message) > 0){
            if(strpos(strtoupper($this->_message),"ERROR") === false ) {
                $style = "message-ok";
            } else {
                $style = "message-error";
            }
        ?>
        <fieldset  class="<?php echo $style; ?>">
        <?php
            include_once INCLUDE_SCRIPTS_PATCH."tmpl-message-box.php";
        ?>
        </fieldset>
        <?php
        }
    }
    /*
     * Return list of config forms
     */
    public function eventsList($selected_event_name = ""){
        $files = glob(CONFIG_EVENTS_PATCH.'*.xml');
        include INCLUDE_SCRIPTS_PATCH."tmpl-events-list.php";
        $this->_loadEventInfo($selected_event_name);
    }

    public function newMembersList(){
        $files = glob(CONFIG_MEMEBRS_PATCH.'*.csv');
        foreach($files as $file){
            $event_name =  basename($file);
            $event_name = str_replace("_".NEW_MEMEBRS_FILE,"",$event_name);
            $event = new Event();
            $event->loadFromFile($this->_generateEventFilename($event_name));
            $members = $this->_getMembersFromFile($file);
            include INCLUDE_SCRIPTS_PATCH . "tmpl-new-members-list.php";
        }
        //$this->_loadEventInfo($selected_event_name);
    }

    private function _getMembersFromFile($file){
        $members = array();
        $lines = file($file);
        foreach($lines as $line){
            $member = new Member($line);
            if ($member->parseOk) {
                $members[] = $member;
            }
        }
        return $members;

    }

    public function handleAdminFormRequest(){
        if(isset ($_GET["members"])){
            $this->newMembersList();
        } elseif(isset ($_POST["new_event_name"])){
            $this->_newEvent($_POST["new_event_name"]);
        }elseif(isset ($_POST["selected_events_name"])) {
            $this->eventsList($_POST["selected_events_name"]);
        }elseif(isset ($_POST["event_edit_name"])) {
            $this->_editEvent($_POST["event_edit_name"]);
        } else {
            $this->eventsList();
        }
    }

    private function _newEvent($new_event_name){
        $new_event_name = $this->_strip_data($new_event_name);
        if(strlen($new_event_name) <= 0) {
            $this->_addMessage(MESSAGE_WRONG_EVENT_NAME);
            $this->eventsList();
            return;
        }
        if(file_exists($this->_generateEventFilename($new_event_name))) {
            $this->_addMessage(MESSAGE_EVENT_ALREADY_EXISTS);
            $this->eventsList($new_event_name);
            return;
        }
        $new_event = new Event();
        $new_event->newEvent($new_event_name, $this->_generateEventFilename($new_event_name));

        if ($new_event->save($this->_generateEventFilename($new_event_name))) {
            $this->_addMessage(MESSAGE_EVENT_CREATED);
            $this->eventsList($new_event_name);
        } else {
            $this->_addMessage(MESSAGE_EVENT_NOT_CREATED);
            $this->eventsList();
        }
    }

    private function _editEvent($event_name){
        $event = new Event();
        $event->loadFromFile($this->_generateEventFilename($event_name));
        if ($event->update() && $event->save()){
            $this->_addMessage(MESSAGE_EVENT_SAVED);
            $this->eventsList($event_name);
        } else {
            $this->_addMessage(MESSAGE_EVENT_NOT_SAVED);
            $this->eventsList();
        }
    }

    private function _loadEventInfo($event_name){
        if($event_name == ""){
            return false;
        }
        $event = new Event();
        if ($event->loadFromFile($this->_generateEventFilename($event_name))){
            include INCLUDE_SCRIPTS_PATCH."tmpl-event-detail.php";
        }
    }

    public function buildProcessForm($event_name){
        if($event_name == ""){
            return false;
        }
        $event = new Event();
        if (!$event->loadFromFile($this->_generateEventFilename($event_name))){
            return false;
        }
        ///$this->_buildProcessFormStage3($event);
        if (isset($_POST['event_form_stage'])) {
            if ($_POST['event_form_stage'] == 2) {
                $this->_buildProcessFormStage2($event);
            } elseif ($_POST['event_form_stage'] == 3) {
                $this->_buildProcessFormStage3($event);
            } elseif ($_POST['event_form_stage'] == 4) {
                $this->_buildProcessFormStage4($event);
            } elseif ($_POST['event_form_stage'] == 1) {
                $this->_buildProcessFormStage1($event);
            }
        } else {  //if not set $_POST['event_form_stage'] the stage is 1
            $this->_buildProcessFormStage1($event);
        }
    }

    private function _buildProcessFormStage1($event){
        ?>
        <form name="event_form_stage1" id="event_form_stage1" lang="nl" action="./" method="post" enctype="multipart/form-data" >
            <input type="hidden" name="event_form_stage" value="2"/>
            <div class="form_filds">
                <?php
                $items = explode("\n", $event->when);
                foreach($items as $item){ ?>
                <div class="form_filds_container" style="height: auto;">
                    <?php echo "<input type='checkbox' name='".XML_KEY_WHEN."[]' value='$item'/>".$item."<br/>"; ?>
                </div>
                <?php } ?>

                <div class="form_filds_container note_container">
                    <?php echo $event->when_note; ?>
                </div>
            </div>
            <div class="button_container">
                <a href="#" class="form_button cancel_button"  onclick="$('#lean_overlay').fadeOut(200); $('#sign-up-window').fadeOut(200);">Afbreken</a>
                <input type="submit" value="Bevestiging" class="form_button bevestiging_button" />
            </div>
        </form>
        <script type="text/javascript">
            $('.form_step_tab1').addClass('active_tab');
            $('.form_step_tab2').removeClass('active_tab');
            $('.form_step_tab3').removeClass('active_tab');
            $('.form_step_tab4').removeClass('active_tab');
            $('.process-order-tabs').addClass('process-order-tabs_step1_bg');
            $('.process-order-tabs').removeClass('process-order-tabs_step2_bg');
            $('.process-order-tabs').removeClass('process-order-tabs_step3_bg');
            $('.process-order-tabs').removeClass('process-order-tabs_step4_bg');
            $("#process-form-content").removeClass('process-order-loading');
            $("#event_form_stage1").submit(function() {
                if ($('#event_form_stage1 :checkbox:checked').length <=0){
                    alert('<?php echo MESSAGE_SELECT_ANY_VALUE; ?>');
                    return false;
                }
                if(domSubmit(this, 'event_form_stage1')) {
                    $("#process-form-content").addClass('process-order-loading');
                    var url = "./content.php?event_name=<?php echo $event->name; ?>";
                    $.ajax({
                        type: "POST", url: url,
                        data: $("#event_form_stage1").serialize(),
                        success: function (data) {
                            $("#process-form-content").html(data);
                        }
                    });
                    return false;
                } else {
                    return false;
                }
            });
        </script>
        <?php
    }

    private function _buildProcessFormStage2($event){
        ?>
        <br/>
        <form name="event_form_stage2" id="event_form_stage2" lang="nl" action="./" method="post" enctype="multipart/form-data" >
            <input type="hidden" name="event_form_stage" value="3"/>
            <fieldset>
                <legend><?php echo STEP_1_HEADER; ?></legend>
                <?php
                if (isset($_POST[XML_KEY_WHEN])) {
                    foreach ($_POST[XML_KEY_WHEN] as $when) {
                        echo $when."<br/>";
                        echo "<input type='hidden' name='".XML_KEY_WHEN."[]' value='$when'/>";
                    }
                }
                ?>
            </fieldset>
            <br/>
            <div class="form_filds">
                <?php $items = explode("\n", $event->what);
                foreach($items as $item){ ?>
                    <div class="form_filds_container" style="height: auto;">
                        <?php echo "<input type='checkbox' name='".XML_KEY_WHAT."[]' value='".$item."'/>".($item)."<br/>"; ?>
                    </div>
                <?php } ?>
                <div class="form_filds_container note_container">
                    <?php echo $event->what_note; ?>
                </div>
            </div>
            <div class="button_container">
                <input type="submit" value="Vorige" onclick="$('input[name=event_form_stage]').val(1); " class="form_button cancel_button" />
                <input type="submit" value="Bevestiging" class="form_button bevestiging_button" />
            </div>
        </form>
        <script type="text/javascript">
            $('.form_step_tab2').addClass('active_tab');
            $('.form_step_tab1').removeClass('active_tab');
            $('.form_step_tab3').removeClass('active_tab');
            $('.form_step_tab4').removeClass('active_tab');
            $('.process-order-tabs').addClass('process-order-tabs_step2_bg');
            $('.process-order-tabs').removeClass('process-order-tabs_step1_bg');
            $('.process-order-tabs').removeClass('process-order-tabs_step3_bg');
            $('.process-order-tabs').removeClass('process-order-tabs_step4_bg');
            $("#process-form-content").removeClass('process-order-loading');
            $("#event_form_stage2").submit(function() {
                if (($('input[name=event_form_stage]').val() != '1')&&($('#event_form_stage2 :checkbox:checked').length <=0)){
                    alert('<?php echo MESSAGE_SELECT_ANY_VALUE; ?>');
                    return false;
                }
                $("#process-form-content").addClass('process-order-loading');
                var url = "./content.php?event_name=<?php echo $event->name; ?>";
                $.ajax({
                    type: "POST", url: url,
                    data: $("#event_form_stage2").serialize(),
                    success: function(data) {
                        $("#process-form-content").html(data);
                    }
                });
                return false;
            });
        </script>
    <?php
    }

    private function _buildProcessFormStage3($event){
    ?>
        <form name="event_form_stage3" id="event_form_stage3" lang="nl"
              enctype="multipart/form-data" >
            <input type="hidden" name="event_form_stage" value="4"/>
            <input type="hidden" name="event_name" value="<?php echo $event->name; ?>"/>
            <fieldset>
                <legend><?php echo STEP_1_HEADER; ?></legend>
                <?php
                if (isset($_POST[XML_KEY_WHEN])) {
                    foreach ($_POST[XML_KEY_WHEN] as $when) {
                        echo $when."<br/>";
                        echo "<input type='hidden' name='".XML_KEY_WHEN."[]' value='$when'/>";
                    }
                }
                ?>
            </fieldset>
            <fieldset>
                <legend><?php echo STEP_2_HEADER; ?></legend>
                <?php
                if (isset($_POST[XML_KEY_WHAT])) {
                    foreach ($_POST[XML_KEY_WHAT] as $what) {
                        echo $what."<br/>";
                        echo "<input type='hidden' name='".XML_KEY_WHAT."[]' value='".$what."'/>";
                    }
                }
                ?>
            </fieldset>
            <br/>
            <div class="form_filds">
                <div class="form_filds_container" style="height: auto;">
                    <label class="form_label"><?php echo LABEL_COMPANY_NAME; ?></label>
                    <input class="form_input"  type="text" title="<?php echo LABEL_COMPANY_NAME; ?>" name="<?php echo LABEL_COMPANY_NAME; ?>*" value="" size="50"/>
                </div>
                <div class="form_filds_container" style="height: auto;">
                    <label class="form_label"><?php echo LABEL_CONTACT_NAME; ?></label>
                    <input class="form_input"  type="text" title="<?php echo LABEL_CONTACT_NAME; ?>" name="<?php echo LABEL_CONTACT_NAME; ?>*" value="" size="50"/>
                </div>
                <div class="form_filds_container" style="height: auto;">
                    <label class="form_label"><?php echo LABEL_ADDRESS; ?></label>
                    <input class="form_input"  type="text" title="<?php echo LABEL_ADDRESS; ?>" name="<?php echo LABEL_ADDRESS; ?>" value="" size="50"/>
                </div>
                <div class="form_filds_container" style="height: auto;">
                    <label class="form_label"><?php echo LABEL_PHONE; ?></label>
                    <input class="form_input"  type="text" title="<?php echo LABEL_PHONE; ?>" name="<?php echo LABEL_PHONE; ?>*" value="" size="50"/>
                </div>
                <div class="form_filds_container" style="height: auto;">
                    <label class="form_label"><?php echo LABEL_EMAIL; ?></label>
                    <input class="form_input"  email="true" type="text" title="<?php echo LABEL_EMAIL; ?>" name="<?php echo LABEL_EMAIL; ?>*" value="" size="50"/>
                </div>
                <div class="form_filds_container" style="height: auto;">
                    <label class="form_label"><?php echo LABEL_WEBSITE; ?></label>
                    <input class="form_input"  type="text" title="<?php echo LABEL_WEBSITE; ?>" name="<?php echo LABEL_WEBSITE; ?>" value="" size="50"/>
                </div>
                <div class="form_filds_container" style="height: auto;">
                    <label class="form_label"><?php echo LABEL_INDUSTRY; ?></label>
                    <input class="form_input"  type="text" title="<?php echo LABEL_INDUSTRY; ?>" name="<?php echo LABEL_INDUSTRY; ?>" value="" size="50"/>
                </div>
                <div class="form_filds_container" style="height: auto;">
                    <label class="form_label"><?php echo LABEL_PRODUCTS; ?></label>
                    <input class="form_input"  type="text" title="<?php echo LABEL_PRODUCTS; ?>" name="<?php echo LABEL_PRODUCTS; ?>" value="" size="50"/>
                </div>
            </div>
            <div class="button_container">
                <input type="submit" value="Vorige" onclick="$('input[name=event_form_stage]').val(2); " class="form_button cancel_button" />
                <input type="submit" value="Bevestiging" class="form_button bevestiging_button" />
            </div>
        </form>
        <script type="text/javascript">
            $('.form_step_tab3').addClass('active_tab');
            $('.form_step_tab1').removeClass('active_tab');
            $('.form_step_tab2').removeClass('active_tab');
            $('.form_step_tab4').removeClass('active_tab');
            $('.process-order-tabs').addClass('process-order-tabs_step3_bg');
            $('.process-order-tabs').removeClass('process-order-tabs_step1_bg');
            $('.process-order-tabs').removeClass('process-order-tabs_step2_bg');
            $('.process-order-tabs').removeClass('process-order-tabs_step4_bg');
            $("#process-form-content").removeClass('process-order-loading');
            $("#event_form_stage3").submit(function() {
                if(($('input[name=event_form_stage]').val()==2) || domSubmit(this, 'event_form_stage3')) {
                    $("#process-form-content").addClass('process-order-loading');
                    var url = "./content.php?event_name=<?php echo $event->name; ?>";
                    $.ajax({
                        type: "POST", url: url,
                        data: $("#event_form_stage3").serialize(),
                        success: function (data) {
                            $("#process-form-content").html(data);
                        }
                    });
                    return false;
                } else {
                    return false;
                }
            });
        </script>
    <?php
    }

    private function _buildProcessFormStage4($event){
        $member = new Member();
        unset($_POST["event_form_stage"]);
        if (!$member->processForm() || !$member->saveNewMember()) {
            $this->_buildProcessFormStage3($event);
            return;
        }
        $member->send_admin_new_member_email();
        ?>
        <form name="event_form_stage3" id="event_form_stage3" lang="nl" action="./" method="post" enctype="multipart/form-data" >
            <input type="hidden" name="event_form_stage" value="1"/>
            <div class="form_filds">
                <div class="form_filds_container" style="height: auto;">
                    <?php echo $event->confirmation_text; ?>
                </div>
            </div>
            <div class="button_container">
                <input type="submit" value="Close" class="form_button bevestiging_button"
                onclick="$('#lean_overlay').fadeOut(200); $('#sign-up-window').fadeOut(200);" />
            </div>
        </form>
        <script type="text/javascript">
            $('.form_step_tab4').addClass('active_tab');
            $('.form_step_tab1').removeClass('active_tab');
            $('.form_step_tab2').removeClass('active_tab');
            $('.form_step_tab3').removeClass('active_tab');
            $('.process-order-tabs').addClass('process-order-tabs_step4_bg');
            $('.process-order-tabs').removeClass('process-order-tabs_step1_bg');
            $('.process-order-tabs').removeClass('process-order-tabs_step2_bg');
            $('.process-order-tabs').removeClass('process-order-tabs_step3_bg');
            $("#process-form-content").removeClass('process-order-loading');
            $("#event_form_stage3").submit(function() {
                $("#process-form-content").addClass('process-order-loading');
                var url = "./content.php?event_name=<?php echo $event->name; ?>";
                $.ajax({
                    type: "POST", url: url,
                    data: $("#event_form_stage3").serialize(),
                    success: function(data) {
                        $("#process-form-content").html(data);
                    }
                });
                return false;
            });
        </script>
    <?php
    }

}

$manager = new Manager();