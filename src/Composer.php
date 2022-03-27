<?php declare(strict_types = 1);

namespace Utilitte\Console;

use InvalidArgumentException;

final class Composer
{

	public function __construct(
		private string $directory,
	)
	{
		$file = $this->directory . '/composer.json';

		if (!file_exists($file)) {
			throw new InvalidArgumentException(sprintf('Composer file %s does not exist.', $file));
		}
	}

	public function update(): void
	{
		CommandLine::passthru('composer update', $this->directory);
	}

}
