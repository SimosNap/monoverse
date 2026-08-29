<?php

declare(strict_types=1);

namespace Monoverse\Services;

use Monoverse\Core\Session;

class LocaleService
{
	private const SUPPORTED_LOCALES = [
		'it',
		'en',
	];

	public function __construct(
		private ?SettingsService $settings,
		private Session $session
	) {
	}

	public function getDefaultLocale(): string
	{
		if ($this->settings === null) {
			return 'it';
		}

		$locale = trim(
			(string) $this->settings->get(
				'default_locale',
				'it'
			)
		);

		if (
			!in_array(
				$locale,
				self::SUPPORTED_LOCALES,
				true
			)
		) {
			return 'it';
		}

		return $locale;
	}

	public function getAvailableLocales(): array
	{
		if ($this->settings === null) {
			return self::SUPPORTED_LOCALES;
		}

		$raw = trim(
			(string) $this->settings->get(
				'available_locales',
				'it'
			)
		);

		$locales = array_values(
			array_unique(
				array_filter(
					array_map(
						'trim',
						explode(',', $raw)
					),
					static fn (string $locale): bool =>
						in_array(
							$locale,
							self::SUPPORTED_LOCALES,
							true
						)
				)
			)
		);

		if ($locales === []) {
			return [
				$this->getDefaultLocale(),
			];
		}

		return $locales;
	}

	public function getCurrentLocale(): string
	{
		$availableLocales =
			$this->getAvailableLocales();

		$sessionLocale = trim(
			(string) $this->session->get(
				'locale',
				''
			)
		);

		if (
			$sessionLocale !== ''
			&& in_array(
				$sessionLocale,
				$availableLocales,
				true
			)
		) {
			return $sessionLocale;
		}

		$defaultLocale =
			$this->getDefaultLocale();

		if (
			in_array(
				$defaultLocale,
				$availableLocales,
				true
			)
		) {
			return $defaultLocale;
		}

		return $availableLocales[0];
	}

	public function setCurrentLocale(
		string $locale
	): bool {
		$locale = trim($locale);

		if (
			!in_array(
				$locale,
				$this->getAvailableLocales(),
				true
			)
		) {
			return false;
		}

		$this->session->set(
			'locale',
			$locale
		);

		return true;
	}

	public function isMultilingual(): bool
	{
		return count(
			$this->getAvailableLocales()
		) > 1;
	}
}
