<?php
declare(strict_types=1);

namespace Monoverse\Helpers;

final class DateHelper
{
	public static function formatDateTime(
		string $dateTime,
		string $format = 'd/m/Y H:i'
	): string {
		$timestamp = self::timestamp($dateTime);

		if ($timestamp === null) {
			return $dateTime;
		}

		return date($format, $timestamp);
	}

	public static function timeAgo(
		string $dateTime,
		bool $short = false,
		string $locale = 'it'
	): string {
		$timestamp = self::timestamp($dateTime);

		if ($timestamp === null) {
			return $dateTime;
		}

		$locale = strtolower(trim($locale));

		if (!in_array($locale, ['it', 'en'], true)) {
			$locale = 'it';
		}

		$diff = time() - $timestamp;

		if ($diff < 0) {
			return self::formatDateTime($dateTime);
		}

		if ($short) {

			if ($diff < 86400) {
				return $locale === 'en'
					? 'Today'
					: 'Oggi';
			}

			if ($diff < 172800) {
				return $locale === 'en'
					? 'Yesterday'
					: 'Ieri';
			}

			if ($diff < 604800) {
				$days = (int) floor($diff / 86400);

				if ($locale === 'en') {
					return $days === 1
						? '1 day ago'
						: $days . ' days ago';
				}

				return $days === 1
					? '1 giorno fa'
					: $days . ' giorni fa';
			}

			return self::formatDateTime(
				$dateTime,
				'd/m/Y'
			);
		}

		if ($diff < 60) {
			return $locale === 'en'
				? 'Now'
				: 'Adesso';
		}

		if ($diff < 3600) {
			$minutes = (int) floor($diff / 60);

			if ($locale === 'en') {
				return $minutes === 1
					? '1 minute ago'
					: $minutes . ' minutes ago';
			}

			return $minutes === 1
				? '1 minuto fa'
				: $minutes . ' minuti fa';
		}

		if ($diff < 86400) {
			$hours = (int) floor($diff / 3600);

			if ($locale === 'en') {
				return $hours === 1
					? '1 hour ago'
					: $hours . ' hours ago';
			}

			return $hours === 1
				? '1 ora fa'
				: $hours . ' ore fa';
		}

		if ($diff < 172800) {
			return $locale === 'en'
				? 'Yesterday'
				: 'Ieri';
		}

		if ($diff < 604800) {
			$days = (int) floor($diff / 86400);

			if ($locale === 'en') {
				return $days === 1
					? '1 day ago'
					: $days . ' days ago';
			}

			return $days === 1
				? '1 giorno fa'
				: $days . ' giorni fa';
		}

		return self::formatDateTime($dateTime);
	}

	private static function timestamp(
		string $value
	): ?int {
		$value = trim($value);

		if ($value === '') {
			return null;
		}

		if (ctype_digit($value)) {
			return (int) $value;
		}

		$timestamp = strtotime($value);

		if ($timestamp === false) {
			return null;
		}

		return $timestamp;
	}
}
