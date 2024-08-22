<?php declare(strict_types = 1);

namespace Utilitte\Console;

use InvalidArgumentException;

final class Composer
{

	public function __construct(
		private string $directory,
	)
	{
		$this->directory = rtrim($this->directory, '/');
		$file = $this->directory . '/composer.json';

		if (!file_exists($file)) {
			throw new InvalidArgumentException(sprintf('Composer file %s does not exist.', $file));
		}
	}

	public static function tryCreate(string $directory): ?self
	{
		if (self::isComposerDirectory($directory)) {
			return new self($directory);
		}

		return null;
	}

	public function update(): void
	{
		CommandLine::passthru('composer update', $this->directory);
	}

	public function install(): void
	{
		CommandLine::passthru('composer install', $this->directory);
	}

	public static function isComposerDirectory(string $directory): bool
	{
		return file_exists($directory . '/composer.json');
	}

}
