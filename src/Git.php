<?php declare(strict_types = 1);

namespace Utilitte\Console;

use LogicException;

final class Git
{

	public function __construct(
		private string $directory,
	)
	{
	}

	public function isGitDirectory(): bool
	{
		return is_dir($this->directory . '/.git');
	}

	public function hasUncommittedFiles(): bool
	{
		$this->validate();

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
		$this->validate();

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

	private function validate(): void
	{
		if (!$this->isGitDirectory()) {
			throw new LogicException(sprintf('Directory %s, is not a git directory.', $this->directory));
		}
	}

}
