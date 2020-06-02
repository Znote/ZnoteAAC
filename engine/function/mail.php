<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail {
	protected $_config = false;

	/**
	 * @param  array $config
	 * @access public
	 * @return void
	**/
	public function __construct($config) {
		$this->_config = $config;
	}

	/**
	 * Sets the cache expiration limit (IMPORTANT NOTE: seconds, NOT ms!).
	 *
	 * @param  string $to, string $title, string $text, string $accname
	 * @access public
	 * @return boolean
	**/
	public function sendMail($to, $title, $text, $accname = '') {
		//SMTP needs accurate times, and the PHP time zone MUST be set
		//This should be done in your php.ini, but this is how to do it if you don't have access to that
		//date_default_timezone_set('Etc/UTC');

		require_once __DIR__.'/../../PHPMailer/src/Exception.php';
		require_once __DIR__.'/../../PHPMailer/src/PHPMailer.php';
		require_once __DIR__.'/../../PHPMailer/src/SMTP.php';

		//Create a new PHPMailer instance
		$mail = new PHPMailer();

		//Tell PHPMailer to use SMTP
		$mail->isSMTP();

		//Enable SMTP debugging
		// 0 = off (for production use)
		// 1 = client messages
		// 2 = client and server messages
		$mail->SMTPDebug = ($this->_config['debug']) ? 2 : 0;

		//Ask for HTML-friendly debug output
		$mail->Debugoutput = 'html';

		//Set the hostname of the mail server
		$mail->Host = $this->_config['host'];

		//Set the SMTP port number - likely to be 25, 465 or 587
		$mail->Port = $this->_config['port'];

		//Whether to use SMTP authentication
		$mail->SMTPAuth = true;
		$mail->SMTPSecure = $this->_config['securityType'];

		//Username to use for SMTP authentication
		$mail->Username = $this->_config['username'];

		//Password to use for SMTP authentication
		$mail->Password = $this->_config['password'];

		//Set who the message is to be sent from
		$mail->setFrom($this->_config['email'], $this->_config['fromName']);

		//Set who the message is to be sent to
		$mail->addAddress($to, $accname);

		//Set the subject line
		$mail->Subject = $title;

		// Body
		$mail->Body = $text;

		// Convert HTML -> plain for legacy mail recievers
		// Create new lines instead of <br> html tags.
		$text = str_replace("<br>", "\n", $text);
		$text = str_replace("<br\>", "\n", $text);
		$text = str_replace("<br \>", "\n", $text);
		// Then get rid of the rest of the html tags.
		$text = strip_tags($text);

		//Replace the plain text body with one created manually
		$mail->AltBody = $text;


		//send the message, check for errors
		$status = false;
		if (!$mail->send()) {
			echo "Mailer Error: " . $mail->ErrorInfo;
			exit();
		} else {
			$status = true;
		}
		return $status;
	}
}
