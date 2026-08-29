<?php

declare(strict_types=1);

namespace Monoverse\Services;

class Translator
{
	private array $translations = [];

	public function __construct(
		private LocaleService $locale,
		private string $langPath
	) {
		$this->langPath = rtrim(
			$this->langPath,
			'/'
		);
	}

	public function getLocale(): string
	{
		return $this->locale->getCurrentLocale();
	}

	public function translate(
		string $key,
		array $replace = []
	): string {
		$key = trim($key);

		if ($key === '') {
			return '';
		}

		[$group, $item] = $this->parseKey($key);

		if (
			$group === ''
			|| $item === ''
		) {
			return $key;
		}

		$currentLocale =
			$this->locale->getCurrentLocale();

		$translation = $this->find(
			$currentLocale,
			$group,
			$item
		);

		if (
			$translation === null
			&& $currentLocale
				!== $this->locale->getDefaultLocale()
		) {
			$translation = $this->find(
				$this->locale->getDefaultLocale(),
				$group,
				$item
			);
		}

		if ($translation === null) {
			return $key;
		}

		foreach ($replace as $name => $value) {
			$translation = str_replace(
				':' . $name,
				(string) $value,
				$translation
			);
		}

		return $translation;
	}

	private function parseKey(
		string $key
	): array {
		$parts = explode(
			'.',
			$key,
			2
		);

		if (count($parts) !== 2) {
			return [
				'',
				'',
			];
		}

		return [
			trim($parts[0]),
			trim($parts[1]),
		];
	}

	private function find(
		string $locale,
		string $group,
		string $item
	): ?string {
		$translations = $this->loadGroup(
			$locale,
			$group
		);

		$value = $translations;

		foreach (
			explode('.', $item)
			as $segment
		) {
			if (
				!is_array($value)
				|| !array_key_exists(
					$segment,
					$value
				)
			) {
				return null;
			}

			$value = $value[$segment];
		}

		return is_string($value)
			? $value
			: null;
	}

	private function loadGroup(
		string $locale,
		string $group
	): array {
		if (
			isset(
				$this->translations[$locale][$group]
			)
		) {
			return $this->translations[$locale][$group];
		}

		$file =
			$this->langPath
			. '/'
			. $locale
			. '/'
			. $group
			. '.php';

		$translations = [];

		if (is_file($file)) {
			$loaded = require $file;

			if (is_array($loaded)) {
				$translations = $loaded;
			}
		}

		$this->translations[$locale][$group] =
			$translations;

		return $translations;
	}
}
