<?php

    @@HEADER@@

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

    }
