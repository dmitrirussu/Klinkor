<?php
/**
 * Created by Dumitru Russu.
 * Date: 24.07.2014
 * Time: 23:07
 * AppLauncher\Utils${NAME} 
 */

namespace AppLauncher\Utils;

use AppLauncher\Utils\Exceptions\MailException;

require_once PATH_LIBS.'/swiftmailer/lib/swift_required.php';

class Mail implements MailInterface {


	private $mailer;
	private $message;

	public function __construct($useSMTPConf = array(), $extraParams = '-f%s') {

		if ( $useSMTPConf ) {
			if ( !isset($useSMTPConf['host']) ) {
				throw new \InvalidArgumentException('Missing SMTP HOST');
			}

			if ( !isset($useSMTPConf['port']) ) {
				throw new \InvalidArgumentException('Missing SMTP PORT');
			}

			if ( !isset($useSMTPConf['password']) ) {
				throw new \InvalidArgumentException('Missing SMTP PASSWORD');
			}

			if ( !isset($useSMTPConf['username']) ) {
				throw new \InvalidArgumentException('Missing SMTP USERNAME');
			}

			$transport = \Swift_SmtpTransport::newInstance($useSMTPConf['host'], $useSMTPConf['port'])
				->setUsername($useSMTPConf['username'])
				->setPassword($useSMTPConf['password']);
		}
		else {
			$transport = new \Swift_MailTransport($extraParams);
		}
		$this->mailer = new \Swift_Mailer($transport);
		$this->message = new \Swift_Message();
	}

	public function setFrom($addresses, $name = null) {
		$this->message->setFrom($addresses, $name);

		return $this;
	}

	public function setSender($addresses, $name = null) {
		$this->message->setSender($addresses, $name);

		return $this;
	}

	public function setTo($addresses, $name = null) {
		$this->message->setTo($addresses, $name);

		return $this;
	}

	public function setBcc($addresses, $name = null) {
		$this->message->setTo($addresses, $name);

		return $this;
	}

	public function setCc($addresses, $name = null) {
		$this->message->setTo($addresses, $name);

		return $this;
	}

	public function setSubject($subject) {
		$this->message->setSubject($subject);
		return $this;
	}

	public function setBody($body, $contentType = null, $charset = 'utf-8') {

		if ( strcmp(trim($contentType), 'text/html') === 0) {
			$this->message->setBody(nl2br($body), $contentType, $charset);
		}
		else {
			$this->message->setBody(nl2br($body), $contentType, $charset);
		}

		return $this->message;
	}

	public function send() {
		if ( empty($this->message) ) {
			throw new MailException('Missing message instance');
		}
		return $this->mailer->send($this->message);
	}
}

interface MailInterface {
	public function setFrom($addresses, $name = null);
	public function setSender($addresses, $name = null);

	public function setTo($addresses, $name = null);
	public function setBcc($addresses, $name = null);
	public function setCc($addresses, $name = null);

	public function setSubject($subject);
	public function setBody($body, $contentType = null, $charset = null);

	public function send();
}