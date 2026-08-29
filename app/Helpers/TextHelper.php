<?php
declare(strict_types=1);

namespace Monoverse\Helpers;

class TextHelper
{	
	public static function linkMentions(string $text): string
	{
		$text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
	
		$text = preg_replace(
			'~(https?://[^\s<]+)~i',
			'<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>',
			$text
		);
	
		return preg_replace_callback(
			'/(?<!\w)@([A-Za-z0-9_\-]{2,32})/u',
			static function (array $matches): string {
				$username = $matches[1];
	
				return sprintf(
					'<a href="/profile/%1$s" class="mention">@%1$s</a>',
					htmlspecialchars($username, ENT_QUOTES, 'UTF-8')
				);
			},
			$text
		);
	}
}
