<?php
include "./db_connect.php";
require_once('Mail/PHPMailer_v5.1/class.phpmailer.php');

$obj = new DB_Connect();

$stmt = $obj->con1->prepare("SELECT c1.*, c1.complaint_no AS c_num FROM send_mail s1, customer_reg c1 WHERE s1.complaint_no=c1.complaint_no");
$stmt->execute();
$Res = $stmt->get_result();
$data = $Res->fetch_assoc();

if ($Res->num_rows >= 1) {
    $from = "test@pragmanxt.com";
    $from_name = "Onelife";
    echo $complaint_no = $data['c_num'];
    echo $customer_name = $data['fname'] . " " . $data['lname'];
    echo $email = $data['email'];
    echo $date = date("d-m-Y", strtotime($data['date']));
    if($data['warranty']!=2){
        echo $subject = "Onelife Complaint Registered : " . $complaint_no;
        $body = "<h1>
        Dear <b>$customer_name</b>,
        Your complaint has been registered successfully. Your complaint number is : <b>$complaint_no</b>
        Techinician will be allocated soon.
        
        Regards,
        OneLife Team.
        </h1>";
    } else {
        echo $subject = "Onelife Product Registered : " . $complaint_no;
        $body = "<h1>
        Dear <b>$fname $lname</b>,
        Your Product has been registered successfully. Your Warranty number is : <b>$complaint_no</b>
        Your warranty has been started from today ($date)
        
        Regards,
        OneLife Team.
        </h1>";
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