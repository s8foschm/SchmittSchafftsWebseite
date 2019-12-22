<?php
/**
 * PHPMailer simple contact form example.
 * If you want to accept and send uploads in your form, look at the send_file_upload example.
 */
//Import the PHPMailer class into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
require &apos;../vendor/autoload.php&apos;;
if (array_key_exists(&apos;to&apos;, $_POST)) {
    $err = false;
    $msg = &apos;&apos;;
    $email = &apos;&apos;;
    //Apply some basic validation and filtering to the subject
    if (array_key_exists(&apos;subject&apos;, $_POST)) {
        $subject = substr(strip_tags($_POST[&apos;subject&apos;]), 0, 255);
    } else {
        $subject = &apos;No subject given&apos;;
    }
    //Apply some basic validation and filtering to the query
    if (array_key_exists(&apos;query&apos;, $_POST)) {
        //Limit length and strip HTML tags
        $query = substr(strip_tags($_POST[&apos;query&apos;]), 0, 16384);
    } else {
        $query = &apos;&apos;;
        $msg = &apos;No query provided!&apos;;
        $err = true;
    }
    //Apply some basic validation and filtering to the name
    if (array_key_exists(&apos;name&apos;, $_POST)) {
        //Limit length and strip HTML tags
        $name = substr(strip_tags($_POST[&apos;name&apos;]), 0, 255);
    } else {
        $name = &apos;&apos;;
    }
    //Validate to address
    //Never allow arbitrary input for the &apos;to&apos; address as it will turn your form into a spam gateway!
    //Substitute appropriate addresses from your own domain, or simply use a single, fixed address
    if (array_key_exists(&apos;to&apos;, $_POST) and in_array($_POST[&apos;to&apos;], [&apos;sales&apos;, &apos;support&apos;, &apos;accounts&apos;])) {
        $to = $_POST[&apos;to&apos;] . &apos;@example.com&apos;;
    } else {
        $to = &apos;support@example.com&apos;;
    }
    //Make sure the address they provided is valid before trying to use it
    if (array_key_exists(&apos;email&apos;, $_POST) and PHPMailer::validateAddress($_POST[&apos;email&apos;])) {
        $email = $_POST[&apos;email&apos;];
    } else {
        $msg .= "Error: invalid email address provided";
        $err = true;
    }
    if (!$err) {
        $mail = new PHPMailer;
        $mail&#45;>isSMTP();
        $mail&#45;>Host = &apos;localhost&apos;;
        $mail&#45;>Port = 2500;
        $mail&#45;>CharSet = &apos;utf&#45;8&apos;;
        //It&apos;s important not to use the submitter&apos;s address as the from address as it&apos;s forgery,
        //which will cause your messages to fail SPF checks.
        //Use an address in your own domain as the from address, put the submitter&apos;s address in a reply&#45;to
        $mail&#45;>setFrom(&apos;contact@example.com&apos;, (empty($name) ? &apos;Contact form&apos; : $name));
        $mail&#45;>addAddress($to);
        $mail&#45;>addReplyTo($email, $name);
        $mail&#45;>Subject = &apos;Contact form: &apos; . $subject;
        $mail&#45;>Body = "Contact form submission\n\n" . $query;
        if (!$mail&#45;>send()) {
            $msg .= "Mailer Error: " . $mail&#45;>ErrorInfo;
        } else {
            $msg .= "Message sent!";
        }
    }
} ?>

<!DOCTYPE html>
<html>
<head>
    <meta http&#45;equiv="Content&#45;Type" content="text/html; charset=utf&#45;8">
    <title>PHPMailer Contact Form</title>
</head>
<body>
<h1>Contact us</h1>
<?php if (empty($msg)) { ?>
    <form method="post">
        <label for="to">Send to:</label>
        <select name="to" id="to">
            <option value="sales">Sales</option>
            <option value="support" selected="selected">Support</option>
            <option value="accounts">Accounts</option>
        </select><br>
        <label for="subject">Subject: <input type="text" name="subject" id="subject" maxlength="255"></label><br>
        <label for="name">Your name: <input type="text" name="name" id="name" maxlength="255"></label><br>
        <label for="email">Your email address: <input type="email" name="email" id="email" maxlength="255"></label><br>
        <label for="query">Your question:</label><br>
        <textarea cols="30" rows="8" name="query" id="query" placeholder="Your question"></textarea><br>
        <input type="submit" value="Submit">
    </form>
<?php } else {
    echo $msg;
} ?>
</body>
</html>
