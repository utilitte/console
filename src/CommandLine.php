<?php declare(strict_types = 1);

namespace Utilitte\Console;

use LogicException;

final class CommandLine
{

	private static string $currentDir;

	public static function passthru(string $command, ?string $workingDir = null): int
	{
		if ($workingDir) {
			$command = sprintf('cd "%s" && %s', $workingDir, $command);
		}

		passthru($command, $result);

		return $result;
	}

	/**
	 * @param string $command
	 * @param bool $silent
	 * @return string[]
	 */
	public static function command(string $command, bool $silent = false): array
	{
		if ($silent) {
			$state = exec($command, $output, $return);
		} else {
			$state = system($command, $return);
			$output[0] = $state;
		}

		if ($state === false) {
			throw new LogicException("Command $command errored");
		}

		if ($return !== 0) {
			throw new LogicException("Command $command exited with code $return", 1);
		}

		return $output;
	}

	public static function getCwd(): string
	{
		if (!isset(self::$currentDir)) {
			$working = getcwd();

			if ($working === false) {
				throw new LogicException('Cannot get current working dir.');
			}

			self::$currentDir = $working;
		}

		return self::$currentDir;
	}

}
