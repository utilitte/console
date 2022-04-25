<?php declare(strict_types = 1);

namespace Utilitte\Console;

use InvalidArgumentException;

final class Npm
{

	public function __construct(
		private string $directory,
	)
	{
		$this->directory = rtrim($this->directory, '/');

		if (!self::isNpmDirectory($this->directory)) {
			throw new InvalidArgumentException(sprintf('Directory %s is not a npm directory.', $this->directory));
		}
	}

	public function hasLockFile(): bool
	{
		return file_exists($this->directory . '/package-lock.json');
	}

	public function install(bool $ci = false): void
	{
		CommandLine::passthru('npm ' . $ci ? 'ci' : 'i', $this->directory);
	}

	public static function isNpmDirectory(string $directory): bool
	{
		foreach (['package.json', 'package-lock.json'] as $file) {
			if (file_exists(rtrim($directory, '/') . $file)) {
				return true;
			}
		}

		return false;
	}

}
