<?php
/*
=====================================================
 DataLife Engine - by SoftNews Media Group 
-----------------------------------------------------
 http://dle-news.ru/
-----------------------------------------------------
 Copyright (c) 2004-2020 SoftNews Media Group
=====================================================
 This code is protected by copyright
=====================================================
 File: mail.class.php
-----------------------------------------------------
 Use: Mail class
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

require_once (DLEPlugins::Check(ROOT_DIR . '/engine/classes/mail/class.phpmailer.php'));

class dle_mail {

	public $mail = false;
	public $send_error = false;
	public $smtp_msg = "";
	public $from = false;
	public $html_mail = false;
	public $bcc = array ();
	public $keepalive = false;
	
	function __construct($config, $is_html = false) {
		
		$this->mail = new PHPMailer;
		$this->mail->CharSet = $config['charset'];
		$this->mail->Encoding = "base64";

		$config['mail_title'] = str_replace( '&amp;', '&', $config['mail_title'] );

		if( $config['mail_title'] ) {
			$this->mail->setFrom($config['admin_mail'], $config['mail_title']);
		} else {
			$this->mail->setFrom( $config['admin_mail'] );			
		}
		
		if($config['mail_metod'] == "smtp") {
			$this->mail->isSMTP();
			$this->mail->Timeout = 10;
			$this->mail->SMTPAutoTLS = false;
			$this->mail->Host = $config['smtp_host'];
			$this->mail->Port = intval( $config['smtp_port'] );
			$this->mail->SMTPSecure = $config['smtp_secure'];

			if($this->mail->SMTPSecure == 'ssl') {
				$this->mail->SMTPOptions = array("ssl"=>array("verify_peer"=>false,"verify_peer_name"=>false));
			}

			if($this->mail->SMTPSecure == 'tls') {
				$this->mail->SMTPOptions = array("tls"=>array("verify_peer"=>false,"verify_peer_name"=>false));
			}

			if( $config['smtp_user'] ) {
				$this->mail->SMTPAuth = true;
				$this->mail->Username = $config['smtp_user'];
				$this->mail->Password = $config['smtp_pass'];
			}
			
			if( $config['smtp_mail'] ) {
				$this->mail->From = $config['smtp_mail'];
				$this->mail->Sender = $config['smtp_mail'];
			}
		}
		
		$this->mail->XMailer = "DLE CMS";
		
		if ( $is_html ) {
			$this->mail->isHTML();
			$this->html_mail = true;
		}
	}
	
	function send($to, $subject, $message) {
		
		if( $this->from ) {
			$this->mail->addReplyTo($this->from, $this->from);
		}
		
		$this->mail->addAddress($to);
		$this->mail->Subject = $subject;
		
		if($this->mail->Mailer == 'smtp' AND $this->keepalive ) {
			$this->mail->SMTPKeepAlive = true;
		}
		
		if( $this->html_mail ) {
			$this->mail->msgHTML($message);
		} else {
			$this->mail->Body = $message;
		}

		if( count( $this->bcc ) ) {
			
			foreach($this->bcc as $bcc) {
				$this->mail->addBCC($bcc);
			}
			
		}
		
		if (!$this->mail->send()) {
			$this->smtp_msg = $this->mail->ErrorInfo;
			$this->send_error = true;
		}
		
		$this->mail->clearAllRecipients();
		$this->mail->clearAttachments();
	
	}

    function addCustomHeader($name, $value = null) {
        $this->mail->addCustomHeader( $name, $value );
    }

	function addAttachment($path, $name = '', $encoding = 'base64', $type = '', $disposition = 'attachment') {
		$this->mail->addAttachment( $path, $name, $encoding, $type, $disposition );
	}
}
?>