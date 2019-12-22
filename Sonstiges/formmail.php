<?php
$recipient = "francesco.schmitt@aol.de";
$fmtResponse= implode("", file("response.htt"));
$fmtMail= implode("", file("mail.htt"));
foreach($_POST as $key=> $val) {
  $fmtResponse= str_replace("<$key>", $val, $fmtResponse);
  $fmtMail= str_replace("<$key>", $val, $fmtMail);
}
if ($_POST["access"] == "irregeheim") {
  mail($recipient, $_POST["subject"], $fmtMail);
}
echo $fmtResponse;
?>
