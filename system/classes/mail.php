<?php
/*
=====================================================
 DataLife Engine - by SoftNews Media Group 
-----------------------------------------------------
 http://dle-news.ru/
-----------------------------------------------------
 Copyright (c) 2004,2011 SoftNews Media Group
=====================================================
 Данный код защищен авторскими правами
=====================================================
 Файл: mail.class.php
-----------------------------------------------------
 Назначение: Класс для отправки писем с сайта
=====================================================
*/
class dle_mail {
	
	var $site_name = "";
	var $from = "";
	var $to = "";
	var $subject = "";
	var $message = "";
	var $header = "";
	var $additional_parameters = null;
	var $error = "";
	var $bcc = array ();
	var $mail_headers = "";
	var $html_mail = 0;
	var $charset = 'windows-1251';
	
	var $smtp_fp = FALSE;
	var $smtp_msg = "";
	var $smtp_port = "";
	var $smtp_host = "localhost";
	var $smtp_user = "";
	var $smtp_pass = "";
	var $smtp_code = "";
	var $smtp_mail = "";
	var $send_error = FALSE;
	
	var $eol = "\n";
	
	var $mail_method = 'php';
	
	function dle_mail($config, $is_html = false) {
		$this->mail_method = $config['mail_metod'];
		
		$this->from = $config['admin_mail'];
		$this->charset = $config['charset'];
		$this->site_name = $config['home'];
		$this->additional_parameters = trim($config['mail_additional']) ? trim($config['mail_additional']) : null;
		$this->smtp_mail = trim($config['smtp_mail']) ? trim($config['smtp_mail']) : '';
		
		$this->smtp_host = $config['smtp_host'];
		$this->smtp_port = intval( $config['smtp_port'] );
		$this->smtp_user = $config['smtp_user'];
		$this->smtp_pass = $config['smtp_pass'];
		
		$this->html_mail = $is_html;
	}
	
	function compile_headers() {
		
		$this->subject = "=?" . $this->charset . "?b?" . base64_encode( $this->subject ) . "?=";
		$from = "=?" . $this->charset . "?b?" . base64_encode( $this->site_name ) . "?=";
		
		if( $this->html_mail ) {
			$this->mail_headers .= "MIME-Version: 1.0" . $this->eol;
			$this->mail_headers .= "Content-type: text/html; charset=\"" . $this->charset . "\"" . $this->eol;
		} else {
			$this->mail_headers .= "MIME-Version: 1.0" . $this->eol;
			$this->mail_headers .= "Content-type: text/plain; charset=\"" . $this->charset . "\"" . $this->eol;
		}
		
		if( $this->mail_method != 'smtp' ) {
			
			if( count( $this->bcc ) ) {
				$this->mail_headers .= "Bcc: " . implode( ",", $this->bcc ) . $this->eol;
			}
		
		} else {
			
			$this->mail_headers .= "Subject: " . $this->subject . $this->eol;
			
			if( $this->to ) {
				
				$this->mail_headers .= "To: " . $this->to . $this->eol;
			}
		
		}
		
		$this->mail_headers .= "From: \"" . $from . "\" <" . $this->from . ">" . $this->eol;
		
		$this->mail_headers .= "Return-Path: <" . $this->from . ">" . $this->eol;
		$this->mail_headers .= "X-Priority: 3" . $this->eol;
		$this->mail_headers .= "X-MSMail-Priority: Normal" . $this->eol;
		$this->mail_headers .= "X-Mailer: DLE PHP" . $this->eol;
	
	}
	
	function send($to, $subject, $message) {
		$this->to = preg_replace( "/[ \t]+/", "", $to );
		$this->from = preg_replace( "/[ \t]+/", "", $this->from );
		
		$this->to = preg_replace( "/,,/", ",", $this->to );
		$this->from = preg_replace( "/,,/", ",", $this->from );
		
		if( $this->mail_method != 'smtp' )
			$this->to = preg_replace( "#\#\[\]'\"\(\):;/\$!Ј%\^&\*\{\}#", "", $this->to );
		else
			$this->to = '<' . preg_replace( "#\#\[\]'\"\(\):;/\$!Ј%\^&\*\{\}#", "", $this->to ) . '>';


		$this->from = preg_replace( "#\#\[\]'\"\(\):;/\$!Ј%\^&\*\{\}#", "", $this->from );
		
		$this->subject = $subject;
		$this->message = $message;
		
		$this->message = str_replace( "\r", "", $this->message );
		
		$this->compile_headers();
		
		if( ($this->to) and ($this->from) and ($this->subject) ) {
			if( $this->mail_method != 'smtp' ) {

				if( !@mail( $this->to, $this->subject, $this->message, $this->mail_headers, $this->additional_parameters )  ) {

					if( !@mail( $this->to, $this->subject, $this->message, $this->mail_headers)  ) {

						$this->smtp_msg = "PHP Mail Error.";
						$this->send_error = true;

					}

				}
			
			} else {
				$this->smtp_send();
			}
		
		}
		
		$this->mail_headers = "";
	
	}
	
	function smtp_get_line() {
		$this->smtp_msg = "";
		
		while ( $line = fgets( $this->smtp_fp, 515 ) ) {
			$this->smtp_msg .= $line;
			
			if( substr( $line, 3, 1 ) == " " ) {
				break;
			}
		}
	}
	
	function smtp_send() {
		$this->smtp_fp = @fsockopen( $this->smtp_host, intval( $this->smtp_port ), $errno, $errstr, 30 );
		
		if( ! $this->smtp_fp ) {
			$this->smtp_error( "Could not open a socket to the SMTP server" );
			return;
		}
		
		$this->smtp_get_line();
		
		$this->smtp_code = substr( $this->smtp_msg, 0, 3 );
		
		if( $this->smtp_code == 220 ) {
			$data = $this->smtp_crlf_encode( $this->mail_headers . "\n" . $this->message );
			
			$this->smtp_send_cmd( "HELO " . $this->smtp_host );
			
			if( $this->smtp_code != 250 ) {
				$this->smtp_error( "HELO" );
				return;
			}
			
			if( $this->smtp_user and $this->smtp_pass ) {
				$this->smtp_send_cmd( "AUTH LOGIN" );
				
				if( $this->smtp_code == 334 ) {
					$this->smtp_send_cmd( base64_encode( $this->smtp_user ) );
					
					if( $this->smtp_code != 334 ) {
						$this->smtp_error( "Username not accepted from the server" );
						return;
					}
					
					$this->smtp_send_cmd( base64_encode( $this->smtp_pass ) );
					
					if( $this->smtp_code != 235 ) {
						$this->smtp_error( "Password not accepted from the SMTP server" );
						return;
					}
				} else {
					$this->smtp_error( "This SMTP server does not support authorisation" );
					return;
				}
			}

			if (!$this->smtp_mail) $this->smtp_mail = $this->from;

			$this->smtp_send_cmd( "MAIL FROM:<" . $this->smtp_mail . ">" );
			
			if( $this->smtp_code != 250 ) {
				$this->smtp_error( "Incorrect FROM address: $this->smtp_mail" );
				return;
			}
			
			$to_array = array ( $this->to );
			
			if( count( $this->bcc ) ) {
				foreach ( $this->bcc as $bcc ) {
					if( preg_match( "/^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,4}|[0-9]{1,4})(\]?)$/", str_replace( " ", "", $bcc ) ) ) {
						$to_array[] = "<".$bcc.">";
					}
				}
			}
		
			foreach ( $to_array as $to_email ) {
				$this->smtp_send_cmd( "RCPT TO:" . $to_email );
				
				if( $this->smtp_code != 250 ) {
					$this->smtp_error( "Incorrect email address: $to_email" );
					return;
					break;
				}
			}
			
			$this->smtp_send_cmd( "DATA" );
			
			if( $this->smtp_code == 354 ) {
				fputs( $this->smtp_fp, $data . "\r\n" );
			} else {
				$this->smtp_error( "Error on write to SMTP server" );
				return;
			}
			
			$this->smtp_send_cmd( "." );
			
			if( $this->smtp_code != 250 ) {
				$this->smtp_error("Error on send mail");
				return;
			}
			
			$this->smtp_send_cmd( "quit" );
			
			if( $this->smtp_code != 221 ) {
				$this->smtp_error("Error on quit");
				return;
			}
			
			@fclose( $this->smtp_fp );
		} else {
			$this->smtp_error( "SMTP service unaviable" );
			return;
		}
	}
	
	function smtp_send_cmd($cmd) {
		$this->smtp_msg = "";
		$this->smtp_code = "";
		
		fputs( $this->smtp_fp, $cmd . "\r\n" );
		
		$this->smtp_get_line();
		
		$this->smtp_code = substr( $this->smtp_msg, 0, 3 );
		
		return $this->smtp_code == "" ? FALSE : TRUE;
	}
	
	function smtp_error($err = "") {
		$this->smtp_msg = $err;
		$this->send_error = true;
		return;
	}
	
	function smtp_crlf_encode($data) {
		$data .= "\n";
		$data = str_replace( "\n", "\r\n", str_replace( "\r", "", $data ) );
		$data = str_replace( "\n.\r\n", "\n. \r\n", $data );
		
		return $data;
	}
}
?>