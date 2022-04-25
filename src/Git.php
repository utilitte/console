<?php declare(strict_types = 1);

namespace Utilitte\Console;

use InvalidArgumentException;

final class Git
{

	public function __construct(
		private string $directory,
	)
	{
		$this->directory = rtrim($this->directory, '/');
		
		if (!self::isGitDirectory($this->directory)) {
			throw new InvalidArgumentException(sprintf('Directory %s is not a git directory.', $this->directory));
		}
	}

	public static function isGitDirectory(string $directory): bool
	{
		return is_dir(rtrim($directory, '/') . '/.git');
	}

	public function hasUncommittedFiles(): bool
	{
		return (bool) $this->command('git status -s');
	}

	/**
	 * @return string[]
	 */
	public function getUncommitedFiles(): array
	{
		return $this->command('git status -s');
	}

	public function hasUnpushedCommits(): bool
	{
		$return = $this->command('git status -s -b');

		return str_contains($return[0], '[ahead');
	}

	/**
	 * @return string[]
	 */
	private function command(string $command): array
	{
		return CommandLine::command(sprintf('cd "%s" && %s', $this->directory, $command), true);
	}

}
