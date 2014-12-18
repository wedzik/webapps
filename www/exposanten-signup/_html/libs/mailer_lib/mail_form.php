<!DOCTYPE html>
<html lang="en">
    <STYLE type="text/css">
        body, p.ContactFormFeedback {
            font-family: "Arial";
            font-size: 14px;
        }

        .ContactFormFeedback {
            font-family: "Courier New";
            font-size: 14px;
        }

        h2.ContactFormFeedback {
            font-size: 16px;
        }

        a.form-error {
            text-decoration: none;
            color: #FF0000;
        }

        input.error, textarea.error {
            background-color: #FFC0C0;
        }
    </STYLE>

    <?php include("mailer_lib.php"); ?>
    <body>
        <?php
        define('RECAPCHA_PUBLIC_KEY', '6LeOWeMSAAAAAFBVCsV3zxTBfCWUryB9mCpYPe6Y');
        define('RECAPCHA_PRIVATE_KEY', '6LeOWeMSAAAAAIRlIyKx4PO64IBFqV0SHrDHqVmY');
        if (isset($_POST['Message'])) {
            if (isset($_POST['rndval'])) {
                unset($_POST['rndval']);
            }
            $mailRes = mailOut("EN", "codetest@easybow.com", false, "", true, RECAPCHA_PRIVATE_KEY, 20, 79, 10);
            if ($mailRes !== true) {
                echo $mailRes;
            }
        } else {
        ?>
        <form action="" lang="en" method="post" enctype="multipart/form-data">
                <table border="0" cellspacing="2" cellpadding="2" style="width: 350px;">
                    <tr><td colspan="2"><h2>Mail form</h2></td></tr>
                    <tr>
                        <td>Your Email Address</td>
                        <td><input type="text" name="YourEMAILAddress" value="" size="26" /></td></tr>
                    <tr><td colspan="2"><textarea name="Message" rows="7" cols="37"></textarea></td></tr>
                    <tr><td colspan="2" height="12" ></td></tr>
                    <tr><td colspan="2"><?php echo mailBuildRecaptcha(RECAPCHA_PUBLIC_KEY); ?></td></tr>
                    <tr><td height="12" colspan="2"></td></tr>
                    <tr><td colspan="2"><input type="submit" value="Send"/></td></tr>
                </table>
            </form>
        <?php } ?>
    </body>
</html>