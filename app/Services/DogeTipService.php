<?php

declare(strict_types=1);

namespace Monoverse\Services;

class DogeTipService
{
	public function __construct(
		private SimosNapService $simosnap
	) {
	}

	public function resolveAddress(array $profile): ?string
	{
		$source = trim(
			(string) ($profile['doge_tip_source'] ?? '')
		);

		if ($source === 'mydogemask') {
			$address = trim(
				(string) ($profile['doge_tip_address'] ?? '')
			);

			return $address !== ''
				? $address
				: null;
		}

		if ($source === 'simosnap') {
			$username = trim(
				(string) ($profile['username'] ?? '')
			);

			if ($username === '') {
				return null;
			}

			return $this->simosnap->getDogecoinAddress(
				$username
			);
		}

		return null;
	}
}
