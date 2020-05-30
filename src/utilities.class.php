<?php

    @@HEADER@@

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;

	require_once 'vendor/autoload.php';

    class Utilities {

		public static function formatShortDate($datetime, $locale) {
			$fmt_date = new IntlDateFormatter($locale, IntlDateFormatter::LONG, IntlDateFormatter::NONE);
			return $fmt_date->format($datetime);
		}

		public static function formatShortTime($datetime, $locale) {
			$fmt_time = new IntlDateFormatter($locale, IntlDateFormatter::NONE, IntlDateFormatter::SHORT);
			return $fmt_time->format($datetime);
		}

		public static function formatShortDateTime($datetime, $format, $locale) {
			$fmt_date = new IntlDateFormatter($locale, IntlDateFormatter::LONG, IntlDateFormatter::NONE);
			$fmt_time = new IntlDateFormatter($locale, IntlDateFormatter::NONE, IntlDateFormatter::SHORT);
			return sprintf($format, $fmt_date->format($datetime), $fmt_time->format($datetime));
		}

		public static function sendEmail($fromName, $fromEmailAddress, $toName, $toEmailAddress, $subject, $message)
		{
			$mail = new PHPMailer;
			$mail->CharSet = 'UTF-8';
			$mail->Encoding = 'quoted-printable';
			$mail->isMail();
			$mail->isHTML(false);
			$mail->setFrom($fromEmailAddress, $fromName);
			$mail->addAddress($toEmailAddress, $toName);
			$mail->Subject = $subject;
			$mail->Body = $message;
			return $mail->send();
		}

    }
