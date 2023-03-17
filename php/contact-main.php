<?php
function setInnerHTML($element, $html)
{
    $fragment = $element->ownerDocument->createDocumentFragment();
    $fragment->appendXML($html);
    while ($element->hasChildNodes())
        $element->removeChild($element->firstChild);
    $element->appendChild($fragment);
}

$errors = [];
$errorMessage = '';

$htmlContent = file_get_contents("../email-contact.html");

if (!empty($_POST)) {
   $name = $_POST['name'];
   $email = $_POST['email'];
   $message = $_POST['message'];
   $emailSubject = $_POST['subject'];

   //its morbin time
    $doc = new DOMDocument();
    libxml_use_internal_errors(true);
    $doc->loadHTML($htmlContent);
    libxml_use_internal_errors(false);
    $senderName = $doc->getElementById('senderName');
    $senderEmail = $doc->getElementById('senderEmail');
    $subjTitle = $doc->getElementById('subjectTitle');
    $messageBody = $doc->getElementById('messageBody');
    $messageHeader = $doc->getElementById('messageHeader');
    setInnerHTML($senderEmail, $email);
    setInnerHTML($senderName, $name);
    setInnerHTML($subjTitle, "Subject: " . $emailSubject);
    setInnerHTML($messageBody, $message);
    setInnerHTML($messageHeader, "New Message From " . $name);

    $htmlContent = $doc->saveHTML();

   if (empty($name)) {
       $errors[] = 'Name is empty';
   }

   if (empty($email)) {
       $errors[] = 'Email is empty';
   } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
       $errors[] = 'Email is invalid';
   }

   if (empty($message)) {
       $errors[] = 'Message is empty';
   }

   if (empty($emailSubject)) {
       $errors[] = 'Subject is empty';
   }

   if (empty($errors)) {
       $toEmail = '2022sha01012@iacademy.edu.ph'; //change this later
       $emailSubject = 'New email from your contact form';
       $headers = ['From' => $email, 'Reply-To' => $email, 'Content-type' => 'text/html; charset=utf-8'];
       $bodyParagraphs = ["Name: {$name}", "Email: {$email}", "Message:", $message];
       $body = join(PHP_EOL, $bodyParagraphs);

       if (mail($toEmail, $emailSubject, $htmlContent, $headers))  {

           header('Location: ../contact-thank-you.html');
       } else {
           $errorMessage = 'Oops, something went wrong. Please try again later';
       }

   } else {

       $allErrors = join('<br/>', $errors);
       $errorMessage = "<p style='color: red;'>{$allErrors}</p>";
   }
}

?>