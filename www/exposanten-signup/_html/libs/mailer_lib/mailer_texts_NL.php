<?php
    define("CONST_FORMMAKEUP_LINE", "-------------------------------------------------\n");

    define("CONST_LABEL_Mailadres", "Emailadres");
    define("CONST_LABEL_ContactName","Contactpersoon");
    define("CONST_LABEL_Subject", "Onderwerp");
    define("CONST_LABEL_Message", "Bericht");
    
    define("CONST_DEFAULT_ContactName", "Anoniempje");
    define("CONST_DEFAULT_Subject", "(geen onderwerp)");

    define("CONST_ALLOK_FEEDBACK", "<h2 class='ContactFormFeedback'>Bevestiging Bericht</h2>\n".
            "<p class='ContactFormFeedback'>Dit is een bevestiging that uw bericht succesvol is opgenomen".
	    "in ons systeem. We zullen uw bericht zo spoedig mogelijk verwerken</p>\n");
   
    define("CONST_ERROR_NoMailBox", "<p class='ContactFormFeedback'>Oeps voor deze website is er geen email adres. ".
            "Verzending onmogelijk.</p>");
    define("CONST_ERROR_WrongEMailAddres", "<p class='ContactFormFeedback'>Onvolledig email adres. Verzending gestopt.</p>");
    define("CONST_ERROR_NoInformationReceived", "<p class='ContactFormFeedback'>Het formulier is leeg! Controleer de ".
            "instellingen van uw PC en/of de firewall policy van uw bedrijfsnetwerk</p>");
    define("CONST_ERROR_NoSendMail", "<h2 class='ContactFormFeedback'>Oops</h2>".
            "<p class='ContactFormFeedback'>We ondervinden technische problemen. ".
            "Het onderstaande bericht kan niet door ons worden verwerkt.\nProbeer het later nog eens of stuur het bericht direct naar: ");
    define("CONST_ERROR_CheckRecaptchaFail", "<p class='ContactFormFeedback'>De reCAPTCHA letters/cijfer zijn verkeerd ingevuld. ".
            "Ga terug om het juist te kunnen invullen. (reCAPTCHA zei: </p>");
    define("CONST_ERROR_Required_Field_Begin", "<p class='ContactFormFeedback'><i>'");
    define("CONST_ERROR_Required_Field_End", "'</i> &nbsp;is een verplicht veld.</p>");
    define("CONST_ERROR_GoBack", "<p>Ga terug en corrigeer de problemen.</p>");
?>
