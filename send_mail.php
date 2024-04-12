<?php
include "./db_connect.php";
require_once('Mail/PHPMailer_v5.1/class.phpmailer.php');

$obj = new DB_Connect();

$stmt = $obj->con1->prepare("SELECT c1.*, c1.complaint_no AS c_num FROM send_mail s1, customer_reg c1 WHERE s1.complaint_no=c1.complaint_no");
$stmt->execute();
$Res = $stmt->get_result();

while ($data = $Res->fetch_assoc()) {
    $from = "test@pragmanxt.com";
    $from_name = "Onelife";
    $complaint_no = $data['c_num'];
    $customer_name = $data['fname'] . " " . $data['lname'];
    $email = $data['email'];
    $date = date("d-m-Y", strtotime($data['date']));
    if($data['warranty']!=2){
        $subject = "New Complaint Registered : " . $complaint_no;
        $body = "
        Dear <b>$customer_name</b>,
        Your complaint has been registered successfully. Your complaint number is : <b>$complaint_no</b>
        Techinician will be allocated soon.<br/>
        
        Regards,
        OneLife Team.
        ";
    } else {
        $subject = "New Product Registered : " . $complaint_no;
        $body = "
        Dear <b>$fname $lname</b>,
        Your Product has been registered successfully. Your Warranty number is : <b>$complaint_no</b>
        Your warranty has been started from today ($date).<br/>
        
        Regards,
        OneLife Team.
        ";
    }

    $mail_res = smtpmailer($subject, $body, $email, $from, $from_name);
    if ($mail_res == 1) {
        setcookie("mail", "successfull", time() + 3600, "/");
    } else {
        setcookie("mail", urlencode($mail_res), time() + 3600, "/");
    }
}

function smtpmailer($subject, $body, $to, $from, $from_name)
{
    global $error;
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPAuth = true;

    $mail->SMTPKeepAlive = true;
    $mail->Mailer = "smtp";

    $mail->Host = 'mail.pragmanxt.com';
    $mail->Port = 465;
    $mail->SMTPSecure = 'ssl';
    $mail->Username = $from;
    $mail->Password = "Pragma@12345";

    $mail->IsHTML(true);
    $mail->SMTPDebug = 1;

    $mail->From = $from;
    $mail->FromName = $from_name;
    $mail->Sender = $from; // indicates ReturnPath header
    $mail->AddReplyTo($from, $from_name); // indicates ReplyTo headers

    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->AddAddress($to);

    $mail->Timeout = 60;

    if (!$mail->Send()) {
        $error = 'Mail error: ' . $mail->ErrorInfo;
        echo $error;
        return $error;
    } else {
        $error = 'Message sent!';
        echo $error;
        return "1";
    }
}
?>