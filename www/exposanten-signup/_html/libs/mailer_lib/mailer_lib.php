<?php
//***********************************************************************//
//                              MAILER LIB                               //
//                              VER: 1.1                                 //
//***********************************************************************//
require_once('recaptchalib.php');
require_once('text2htmlmailer.php');

Class Mailer {

    private $_error;
    private $_outMailbox;
    private $_senderName;
    private $_sender;
    private $_mailSubject;
    private $_mailAdres;
    private $_fromMailAdres = "";
    private $_message;
    private $_header = "";
    private $_mailSubjectPrefix = " ";
    private $_replyToo;
    private $_postedMsg;
    private $_messageSplitter;
    private $_mailCopyAtachedFilePath;
    private $_atachFileName;
    private $_atachFilePath;
    private $_atachFileType;
    private $_fieldPadLength;
    private $_messageSplitterLength;
    private $_displayLabels = true;
    private $_silentMode = false;
    private $_minMessageLength = 0;
    private $_maxAtachedFileSize = 3145728; // 3MB
    private $_recaptchaPrivateKey;
    private $_fontSize;

    public function setOutMailbox($outMailbox) {
        $this->_outMailbox = $outMailbox;
    }

    public function setReplyToo($replyToo) {
        $this->_replyToo = $replyToo;
    }

    public function setDisplayLabels($displayLabels) {
        $this->_displayLabels = $displayLabels;
    }

    public function setSilentMode($silentMode) {
        $this->_silentMode = $silentMode;
    }

    public function setMinMessageLength($minMessageLength) {
        $this->_minMessageLength = $minMessageLength;
    }

    public function setRecaptchaPrivateKey($recaptchaPrivateKey) {
        $this->_recaptchaPrivateKey = $recaptchaPrivateKey;
    }

    public function setFieldPadLength($fieldPadLength) {
        $this->_fieldPadLength = $fieldPadLength;
    }

    public function setMessageSplitterLength($messageSplitterLength) {
        $this->_messageSplitterLength = $messageSplitterLength;
    }

    public function setFontSize($fontSize) {
        $this->_fontSize = $fontSize;
    }

    /**
     * Remove dangerous input
     * @param string $userinput
     * @return string
     */
    private function _cleanUserInput($userinput) {
        $clean = str_ireplace(array('*',
            '\\r',
            '\\n',
            '%0a',
            '%0d',
            'mime',
            //'to:',
            'bcc',
            'cc:',
            '==',
            '\\'), "", $userinput);
        //$clean = strip_tags($clean);
        return $clean;
    }

    /**
     * Remove dangerous mail header input
     * @param string $userinput
     * @return string
     */
    private function _scrubHeaderInfo($userinput) {
        $clean = $this->_cleanUserInput($userinput);
        $clean = str_ireplace(array('content-type',
            'content',
            'multipart/mixed',
            'multipart',
            'mime-version',
            'mime',
            'to:',
            'bcc',
            'cc:',
            '==',
            '/',
            '\\'), "", $clean);
        //$clean = str_ireplace("@", "(at)", $clean);
        return $clean;
    }

    private function _checkRecaptcha() {
        $resp = recaptcha_check_answer($this->_recaptchaPrivateKey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
        unset($_POST["recaptcha_challenge_field"]);
        unset($_POST["recaptcha_response_field"]);
        if (!$resp->is_valid) {
            $this->_error = CONST_ERROR_CheckRecaptchaFail . $resp->error . ")";
            return false;
        } else {
            return true;
        }
    }

    /**
     * add spaces between field name and value,
     * to create good align column in monospace font
     * @param type $linetxt
     * @return type
     */
    private function _padText($linetxt) {
        $padlen = 1;
        $linetxt = str_replace("&amp;", "&", $linetxt);
        if (strlen($linetxt) < $this->_fieldPadLength) {
            $padlen = $this->_fieldPadLength - strlen($linetxt);
        }
        return $linetxt . str_repeat(" ", $padlen);
    }

    /**
     * Insert spaces before uppercase
     * @param type $s
     * @return string
     */
    private function _spaceBeforeUpperChars($s) {
        $res = $s[0];
        if (strlen($s) == 0) {
            return "";
        }
        $lastpos = strlen($s);
        for ($i = 1; $i < $lastpos - 1; $i++) {
            if ($s[$i] == strtoupper($s[$i]) &&
                    ($s[$i - 1] != strtoupper($s[$i - 1]) || $s[$i + 1] != strtoupper($s[$i + 1])) &&
                    strcmp($s[$i - 1], " ") != 0) {
                $res .= " ";
            }
            $res .= $s[$i];
        }
        $res .= $s[$lastpos - 1];
        return $res;
    }

    /**
     * Check for valid email address
     * @param string $email
     * @return boolean
     */
    private function _isValidEmail($email) {
        if (!preg_match("/^[^@]{1,64}@[^@]{1,255}$/", $email)) {
            return false;
        }
        $email_array = explode("@", $email);
        $local_array = explode(".", $email_array[0]);
        for ($i = 0; $i < sizeof($local_array); $i++) {
            if (!preg_match("/^(([A-Za-z0-9!#$%&'*+\/=?^_`{|}~-][A-Za-z0-9!#$%&'*+\/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$/", $local_array[$i])) {
                return false;
            }
        }
        if (!preg_match("/^\[?[0-9\.]+\]?$/", $email_array[1])) {
            $domain_array = explode(".", $email_array[1]);
            if (sizeof($domain_array) < 2) {
                return false;                   // Not enough parts to domain
            }
            for ($i = 0; $i < sizeof($domain_array); $i++) {
                if (!preg_match("/^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$/", $domain_array[$i])) {
                    return false;
                }
            }
        }
        return true;
    }

    private function _haveErrors() {
        if (strlen($this->_error) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getErrors() {
        if (strlen($this->_error) > 0) {
            return $this->_error;
        } else {
            return true;
        }
    }

    /**
     * Collect, scrub and construct user feedback
     */
    private function _prepareField() {
        $keyVal = "";
        $this->_message = "";
        foreach ($_POST as $key => $keyVal) {
            $displayKey = $this->_spaceBeforeUpperChars($key);
            switch (strtoupper($key)) {
                case "MAILADRES";
                case "MAILADRES*":
                    $preMailAdr = $this->_cleanUserInput(strtolower($keyVal));
                    if (strlen($preMailAdr) > 0) {
                        if (!$this->_isValidEmail($preMailAdr)) {
                            $this->_error .= CONST_ERROR_WrongEMailAddres;
                        } else {
                            $this->_mailAdres = $preMailAdr;
                        }
                    }
                    break;

                case "FROMMAILADRES":
                    $preMailAdr = $this->_cleanUserInput(strtolower($keyVal));
                    if (strlen($preMailAdr) > 0) {
                        if (!$this->_isValidEmail($preMailAdr)) {
                            $this->_error .= CONST_ERROR_WrongEMailAddres;
                        } else {
                            $this->_fromMailAdres = $preMailAdr;
                        }
                    }
                    break;

                case "CONTACTNAME";
                case "CONTACTNAME*":
                    $this->_senderName = $this->_scrubHeaderInfo($keyVal);
                    if (strlen($this->_senderName) == 0)
                        $this->_senderName = CONST_DEFAULT_ContactName;
                    break;

                case "SUBJECT";
                case "SUBJECT*":
                    $this->_mailSubject .= $this->_scrubHeaderInfo($keyVal);
                    if (strlen($this->_mailSubject) == 0)
                        $this->_mailSubject = CONST_DEFAULT_Subject;
                    if (strlen($this->_mailSubjectPrefix) > 0)
                        $this->_mailSubject = $this->_mailSubjectPrefix . $this->_mailSubject;
                    break;

                case "MESSAGE";
                case "MESSAGE*":
                    //$this->Message .= $this->PadText("\n[".CONST_LABEL_Message."]\n")."\n".$this->CleanUserInput($KeyVal);
                    $this->_message .= "\n".$this->_cleanUserInput($keyVal) . "\n";
                    break;

                case "recaptcha_challenge_field";
                case "recaptcha_response_field":
                    break;

                default:
                    if ($this->_displayLabels) {
                        $this->_message .= $this->_padText("[" . $displayKey . "]") . $this->_cleanUserInput($keyVal) . "\n";
                    } else {
                        $this->_message .= $this->_cleanUserInput($keyVal) . "\n";
                    }
                    break;
            }
            if ((strpos($key, '*') == (strlen($key) - 1)) && ($this->_cleanUserInput($keyVal) == "")) {
                $this->_error .= CONST_ERROR_Required_Field_Begin . $displayKey . CONST_ERROR_Required_Field_End;
            }
        }

        #=== Initialize sender header info ==========/
        if ($this->_fromMailAdres == "")
            $this->_fromMailAdres = $this->_mailAdres;

        $this->_sender = 'From: "' . $this->_senderName . '" <' . $this->_fromMailAdres . ">\r\n";

        #=== Add closing line under message =========/
        $this->_message .= $this->_messageSplitter;
        #=== Check if enough info is provided =======/
        if ((strlen($this->_error) == 0) && (strlen($this->_message) < $this->_minMessageLength)) {
            $this->_error .= CONST_ERROR_NoInformationReceived;
        }
        #=== If error display 'Go back' =============/
        if (strlen($this->_error) > 0) {
            $this->_error .= CONST_ERROR_GoBack;
        }
    }

    /**
     * Convert plain (email) text to html for feedback on web page
     */
    private function _preparePostedMsgHtmlFeedback() {
        $this->_postedMsg = nl2br(htmlspecialchars(stripslashes($this->_message)));
        $this->_postedMsg = str_replace("  ", "&nbsp;&nbsp;", $this->_postedMsg);
        $this->_postedMsg = str_replace("[", "[<b><i>", $this->_postedMsg);
        $this->_postedMsg = str_replace("]", "</i></b>]", $this->_postedMsg);
    }

    private function _prepareMailHeader() {
        $this->_header = $this->_sender;
        //$this->_header .= "\r\n"."x-sender-ip: " . gethostbyaddr($_SERVER['REMOTE_ADDR']) . "\r\n";
        $this->_header .= "Message-ID: <" . time() . "ddfm@" . $_SERVER['SERVER_NAME'] . ">\r\n";

        if ($this->_replyToo != "")
            $this->_header .= "Reply-To: " . $this->_replyToo;
    }

    public function processMail() {
        $this->_messageSplitter = str_repeat("_", $this->_messageSplitterLength);
        $this->_message = $this->_messageSplitter . "\n";

        /* If recaptch used, check it */
        if (isset($_POST["recaptcha_challenge_field"]) && (!$this->_checkRecaptcha())) {
            return $this->_error;
        }

        $this->_prepareField();
        $this->_preparePostedMsgHtmlFeedback();
        $this->_prepareMailHeader();

        $haveAtach = $this->_saveAtachedFile();

        if ($this->_haveErrors()) {
            return $this->_error;
        }
        if ($haveAtach) {
            $mailResult = $this->_sendMailWithAtach(/*$this->_outMailbox, $this->_atachFilePath, $this->_atachFileName*/);
        } else {
            $mailResult = $this->_sendMail();
            //$mailResult = mail($this->_outMailbox, $this->_mailSubject, $this->_message, $this->_header, "-f " . $this->_fromMailAdres);
        }
        if ($mailResult == false) {
            $feedbackTxt = CONST_ERROR_NoSendMail . " <a href='mailto:" . $this->_outMailbox . "'>" . $this->_outMailbox . "</a></p>\n";
        } else {
            $feedbackTxt = CONST_ALLOK_FEEDBACK;
        }

        //=== Out html feedback =====================/
        if ($this->_haveErrors()) {
            return $this->_error;
        }
        if (!$this->_silentMode) {
            echo $feedbackTxt;
            
            $displayMessage = $this->_message;
            while($pos= strpos($displayMessage,"[_")){      //remove fileds with "_" in name from user responce
                $endPos = strpos($displayMessage, "\n", $pos+1 );
                $tmpMessage = substr($displayMessage, 0, $pos-1);
                $tmpMessage .= substr($displayMessage, $endPos+1);
                $displayMessage = $tmpMessage;
            }
            
            $displayMessage = str_replace("[", "<div class='ContactFormFeedbackMessage'>[", $displayMessage);
            $offset = 0;
            while($pos = strpos($displayMessage,"<div class='ContactFormFeedbackMessage'>[",$offset)) {
                $endPos = strpos($displayMessage,"<br/>\n",$pos);
                $messagePart1 = substr($displayMessage,0,$endPos);
                $messagePart2 = substr($displayMessage, $endPos+5);
                $displayMessage = $messagePart1."</div>".$messagePart2;              
                $offset = $pos+5;                
            }
            echo "<br><div class='ContactFormFeedback'>" . $displayMessage . "</div><br/><br/>";
        }

        if (strlen($this->_error) > 0) {
            return $this->_error;
        } else {
            return true;
        }
    }

    private function _sendMailWithAtach() {
        $fp = fopen($this->_atachFilePath, "rb");
        if (!$fp) {
            $this->_error .= "Cannot open file";
            return False;
        }
        $file = fread($fp, filesize($this->_atachFilePath));
        fclose($fp);

        $EOL = "\r\n";
        $boundaryMixed = "mixed_".md5(uniqid(time()));        
        $boundary = md5(uniqid(time()));
        /* ============================================================================ */
        $headers = "MIME-Version: 1.0;" . $EOL;
        $headers .= 'From: "' . $this->_senderName . '" <' . $this->_fromMailAdres . '>' . $EOL;
        if ($this->_replyToo != "") {
            $headers .= "Reply-To: " . $this->_replyToo . $EOL;
        }
        $headers .= "Content-Type: multipart/mixed;" . $EOL . "\t boundary=\"$boundaryMixed\"$EOL$EOL";
        /* ============================================================================ */
        $headers .= "--" . $boundaryMixed . $EOL;        
        $headers .= "Content-Type: multipart/alternative;" . $EOL . "\t boundary=\"$boundary\"$EOL";
        /* ============================================================================ */
        $multipart = $EOL ."--" .  $boundary . $EOL;
        $multipart .= "Content-Type: text/plain; charset=utf-8" . $EOL . $EOL;
        $multipart .= strip_tags($this->_message);
        /* ============================================================================ */
        $multipart .= $EOL . "--" . $boundary . $EOL;
        $multipart .= "Content-Type: text/html; charset=utf-8" . $EOL . $EOL;
        if ($this->_fontSize > 0) {
            $multipart .= '<span style="font-family: Arial; font-size: ' . $this->_fontSize . 'px;">' . $this->_message . '</span>';
        } else {
            $multipart .= "<span>" . $this->_message . "</span>";
        }
        /* ============================================================================ */
        $multipart .= $EOL . "--" . $boundary. "--"  . $EOL;        
        $multipart .= $EOL . "--" . $boundaryMixed. $EOL;
        $multipart .= "Content-Type: application/octet-stream; name=\"$this->_atachFileName\"$EOL";
        $multipart .= "Content-Transfer-Encoding: base64" . $EOL;
        $multipart .= "Content-Disposition: attachment; filename=\"$this->_atachFileName\"$EOL";
        $multipart .= $EOL;
        $multipart .= chunk_split(base64_encode($file)) . $EOL;
        $multipart .= $EOL . "--" . $boundaryMixed . "--" . $EOL;
        /* ============================================================================ */
        if (!mail($this->_outMailbox, $this->_mailSubject, $multipart, $headers, "-f " . $this->_fromMailAdres)) {
            return False;
        } else {
            return True;
        }
    }
    
    private function _sendMail() {
        $EOL = "\r\n";
        //$boundaryMixed = "mixed_".md5(uniqid(time()));      
        $text2html = new Text2htmlMailer();
        
        $boundary = md5(uniqid(time()));
        /* ============================================================================ */
        $headers = "MIME-Version: 1.0;" . $EOL;
        $headers .= 'From: "' . $this->_senderName . '" <' . $this->_fromMailAdres . '>' . $EOL;
        if ($this->_replyToo != "") {
            $headers .= "Reply-To: " . $this->_replyToo . $EOL;
        }
        //$headers .= "Content-Type: multipart/mixed;" . $EOL . "\t boundary=\"$boundaryMixed\"$EOL$EOL";
        /* ============================================================================ */
        //$headers .= "--" . $boundaryMixed . $EOL;        
        $headers .= "Content-Type: multipart/alternative;" . $EOL . "\t boundary=\"$boundary\"$EOL";
        /* ============================================================================ */
        $multipart = $EOL ."--" .  $boundary . $EOL;
        $multipart .= "Content-Type: text/plain; charset=utf-8" . $EOL . $EOL;
        $multipart .= strip_tags($this->_message);
        /* ============================================================================ */
        $this->_message = $text2html->parse($this->_message);
        $multipart .= $EOL . "--" . $boundary . $EOL;
        $multipart .= "Content-Type: text/html; charset=utf-8" . $EOL . $EOL;
        if ($this->_fontSize > 0) {
            $multipart .= '<span style="font-family: Arial; font-size: ' . $this->_fontSize . 'px;">' . $this->_message . '</span>';
        } else {
            $multipart .= "<span>" . $this->_message . "</span>";
        }
        /* ============================================================================ */
        $multipart .= $EOL . "--" . $boundary. "--"  . $EOL;        
        /* ============================================================================ */
        if (!mail($this->_outMailbox, $this->_mailSubject, $multipart, $headers, "-f " . $this->_fromMailAdres)) {
            return False;
        } else {
            return True;
        }
    }    

    private function _saveAtachedFile() {
        if (isset($_FILES['AtachedFile']) && $_FILES['AtachedFile']['error'] != 4) {
            if ($_FILES['AtachedFile']['error'] != 1 && $_FILES['AtachedFile']['error'] != 0) {
                $this->_error .= 'File ' . $_FILES['AtachedFile'] . ' not loadet!';
                return false;
            } else {
                $filesize = $_FILES['AtachedFile']['size'];
                if ($_FILES['AtachedFile']['error'] == 1 || $filesize > $this->_maxAtachedFileSize) {
                    $this->ekrrors .= 'file size is greater than the maximum allowable (3MB)';
                    return false;
                } else {
                    $this->_atachFileName = $_FILES['AtachedFile']['name'];
                    $this->_atachFilePath = $_FILES['AtachedFile']['tmp_name'];

                    if (isset($this->_mailCopyAtachedFilePath)) {
                        $new_file_name = uniqid('image_') . '.' . end(explode(".", $this->_atachFileName));
                        copy($this->_atachFilePath, $this->_mailCopyAtachedFilePath . $new_file_name);
                    }
                    if (isset($_FILES['ufile']['type']))
                        $this->_atachFileType = $_FILES['ufile']['type'];
                    if ($this->_atachFileType == null || $this->_atachFileType == '')
                        $this->_atachFileType = 'unknown/unknown';
                }
            }
            return true;
        }
        return false;
    }

}

/**
 * The language for messages. Default English
 * @param string $lngCode
 */
function setLanguage($lngCode) {
    //$INCDIR=//"/data/www/includes/";
    $INCDIR = '';
    $LNGFILESTEM = "mailer_texts_";
    $lngFileLocal = $_SERVER['DOCUMENT_ROOT'] . "/" . $LNGFILESTEM . $lngCode . ".php";
    if (file_exists($lngFileLocal)) {
        require_once $lngFileLocal;
    } else {
        $lngFile = $INCDIR . $LNGFILESTEM . $lngCode . ".php";
        require_once $lngFile;
    }
}


/**
 * Send Email function
 * @param string $lngCode
 * @param string $mailToo
 * @param string $silentMode
 * @param string $replyToo
 * @param string $displayLabels
 * @param string $recaptchaPrivateKey
 * @param string $fieldPadLength
 * @param string $messageSplitterLength
 * @param string $minMessageLength set min message length what can be sent
 * @param integer $fontSize set font size in mail with atach 
 * @return boolena
 */
function mailOut($lngCode = "EN", $mailToo = "", $silentMode = false, $replyToo = "", $displayLabels = true, $recaptchaPrivateKey = "", $fieldPadLength = 20, $messageSplitterLength = 79, $minMessageLength = 1, $fontSize = 0) {
    setLanguage($lngCode);
    // Check if we have an address to send to
    if (strlen($mailToo) == 0) {
        echo CONST_ERROR_NoMailBox;
    }
    $mailer = new Mailer();
    $mailer->setSilentMode($silentMode);
    $mailer->setDisplayLabels($displayLabels);
    $mailer->setRecaptchaPrivateKey($recaptchaPrivateKey);
    $mailer->setFieldPadLength($fieldPadLength);
    $mailer->setMessageSplitterLength($messageSplitterLength);
    $mailer->setMinMessageLength($minMessageLength);
    $mailer->setFontSize($fontSize);
    $mailer->setOutMailbox($mailToo);
    $mailer->setReplyToo($replyToo);

    return $mailer->processMail();
    //return !$mailer->getErrors();
}

/**
 * Build Recaptcha html
 * @param string $publicKey
 * @param string $themeName (red, white, blackglass, clean)
 */
function mailBuildRecaptcha($publicKey, $themeName = 'red') {
    ?>
    <script type="text/javascript">
        var RecaptchaOptions = {theme: '<?php echo $themeName; ?>'};
    </script>
    <?php
    echo recaptcha_get_html($publicKey);
}
?>