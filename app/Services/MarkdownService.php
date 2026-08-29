<?php
declare(strict_types=1);

namespace Monoverse\Services;

require_once __DIR__ . '/../Libraries/Parsedown.php';

class MarkdownService
{
	private \Parsedown $parser;

	public function __construct()
	{
		$this->parser = new \Parsedown();

		$this->parser->setSafeMode(true);
		$this->parser->setMarkupEscaped(true);
		$this->parser->setBreaksEnabled(true);
	}

	public function render(?string $markdown): string
	{
		$markdown = trim((string) $markdown);

		if ($markdown === '') {
			return '';
		}

		return $this->parser->text($markdown);
	}
}
