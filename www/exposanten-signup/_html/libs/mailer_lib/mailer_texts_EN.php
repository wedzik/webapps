<?php
    define("CONST_FORMMAKEUP_LINE", "-------------------------------------------------\n");

    define("CONST_LABEL_Mailadres", "Email Address");
    define("CONST_LABEL_ContactName","Contact Name");
    define("CONST_LABEL_Subject", "Subject");
    define("CONST_LABEL_Message", "Message");

    define("CONST_DEFAULT_ContactName", "Anonymous");
    define("CONST_DEFAULT_Subject", "(no subject)");

    define("CONST_ALLOK_FEEDBACK", "<h2 class='ContactFormFeedback'>Message Confirmation</h2>\n".
            "<p class='ContactFormFeedback'>This is a confirmation that your message has been succesfully ".
            "entered in our system. Your message will soon be processed by us. </p>\n");

    define("CONST_ERROR_NoMailBox", "<p class='ContactFormFeedback'>Don't know where to send this email to. ".
            "Operation cancelled.</p>");
    define("CONST_ERROR_WrongEMailAddres", "<p class='ContactFormFeedback'>Invalid email address. Action canceled.</p>");
    define("CONST_ERROR_NoInformationReceived", "<p class='ContactFormFeedback'>No information received. ".
            "Please check the security of your system and/or with your administrator.</p>");
    define("CONST_ERROR_NoSendMail", "<h2 class='ContactFormFeedback'>Oops</h2>".
            "<p class='ContactFormFeedback'>We are experiencing technical problems. The message below can not ".
            "be processed by us. Try again later of email your message directly to: ");
    define("CONST_ERROR_CheckRecaptchaFail", "<p class='ContactFormFeedback'>The reCAPTCHA wasn't entered correctly. ".
            "Go back and try it again. (reCAPTCHA said: </p>");
    define("CONST_ERROR_Required_Field_Begin", "<p class='ContactFormFeedback'><i>'");
    define("CONST_ERROR_Required_Field_End", "'</i> &nbsp;is a required field.</p>");
    define("CONST_ERROR_GoBack", "<p>Go back and correct any errors.</p>");
    
    ?>