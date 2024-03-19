<?php

ob_start();

    require_once('PHPMailer_v5.1/class.phpmailer.php'); //library added in download source.

    $msg  = urldecode($_REQUEST['msg']);
    $subj = urldecode($_REQUEST['subject']);
    $to   = urldecode($_REQUEST['to']);
   
    $name = urldecode($_REQUEST['name']);
	$from=urldecode($_REQUEST['from']);
   
	
	

   smtpmailer($to,$from, $name ,$subj, $msg);
   // header("location:../index.php");
    function smtpmailer($to, $from, $from_name, $subject, $body, $is_gmail = true)
    {
    
        global $error;
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPAuth = true; 
        if($is_gmail)
        {
		
           // $mail->SMTPSecure = 'ssl'; 
			$mail->SMTPKeepAlive = true;
			$mail->Mailer = "smtp";
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = 465;  
			$mail->SMTPSecure = 'ssl';   
            $mail->Username = 'pragmatestmail@gmail.com';  
            $mail->Password = 'Pragma@jay';   
        }
        else
        {
	
           $mail->Host = 'smtpout.secureserver.net';
             $mail->Username = 'pragmatestmail@gmail.com';  
            $mail->Password = 'Pragma@jay';   
        }

        $mail->IsHTML(true);		
		$mail->SMTPDebug = 1; 
        $mail->From="pragmatestmail@gmail.com";
        $mail->FromName=$from_name;
        $mail->Sender=$from; // indicates ReturnPath header
        $mail->AddReplyTo($from, $from_name); // indicates ReplyTo headers
//        $mail->AddCC('cc@site.com.com', 'CC: to site.com');
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AddAddress($to);
        if(!$mail->Send())
        {			
            $error = 'Mail error: '.$mail->ErrorInfo;
				echo $error;
            return $error;
		  
        }
        else
        {
            $error = 'Message sent!';
		//	echo $error;
            return "1";
        }
    }
  
//	header("location:../contact-us.php?msg=".$error);
?>