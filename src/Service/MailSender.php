<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class MailSender
{
	/** @var MailerInterface */
	private $mailer;
	
	/**
	 * Email address used to send out emails.
	 * @var string
	 */
	private $mailFrom;
	
	/**
	 * Admin contact email address.
	 * @var string
	 */
	private $mailTo;
	
	/**
	 * MailSender constructor.
	 * @param  string           $mailFrom
	 * @param  string           $mailTo
	 * @param  MailerInterface  $mailer
	 */
	public function __construct(string $mailFrom, string $mailTo, MailerInterface $mailer)
	{
		$this->mailer   = $mailer;
		$this->mailFrom = $mailFrom;
		$this->mailTo   = $mailTo;
	}
	
	/**
	 * @param  string  $to
	 * @param  string  $subject
	 * @param  string  $template
	 * @param  array   $variables
	 * @throws TransportExceptionInterface
	 */
	public function sendTwigEmail(string $to, string $subject, string $template, array $variables)
	{
		$email = (new TemplatedEmail())
			->from($this->mailFrom)
			->to($to)
			->subject($subject)
			->htmlTemplate($template)
			->context($variables);
		
		$this->mailer->send($email);
	}
}