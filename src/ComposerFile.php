<?php declare(strict_types = 1);

namespace Utilitte\Console;

use InvalidArgumentException;
use LogicException;
use Nette\Utils\FileSystem;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Utilitte\Asserts\TypeAssert;

final class ComposerFile
{

	private string $file;

	/** @var mixed[] */
	private array $contents;

	public function __construct(private string $directory)
	{
		$file = $directory . '/composer.json';

		if (!file_exists($file)) {
			throw new InvalidArgumentException(sprintf('Composer file %s does not exist.', $file));
		}

		$this->file = $file;

		try {
			$this->contents = (array) Json::decode(FileSystem::read($file), Json::FORCE_ARRAY);
		} catch (JsonException $e) {
			throw new InvalidArgumentException(sprintf('Composer file %s is not a valid json.', $file), previous: $e);
		}
	}

	public function getName(): string
	{
		return TypeAssert::string($this->getSection('name'));
	}

	/**
	 * @return array<string, string>
	 */
	public function getRequire(): array
	{
		/** @var array<string, string> $require */
		$require = $this->getSectionWithDefault('require', []);

		return $require;
	}

	/**
	 * @return array<string, string>
	 */
	public function getDevRequire(): array
	{
		/** @var array<string, string> $require */
		$require = $this->getSectionWithDefault('dev-require', []);

		return $require;
	}

	/**
	 * @return array<string, string>
	 */
	public function getLibraries(): array
	{
		$libs = [];
		foreach (array_merge($this->getRequire(), $this->getDevRequire()) as $name => $version) {
			if (!str_contains($name, '/')) {
				continue;
			}

			$libs[$name] = $version;
		}

		return $libs;
	}

	public function getLibraryPath(string $name): string
	{
		return $this->directory . '/vendor/' . $name;
	}

	private function getSection(string $section): mixed
	{
		return $this->contents[$section]
			   ??
			   throw new LogicException(sprintf('Composer file %s does not have section %s.', $this->file, $section));
	}

	private function getSectionWithDefault(string $section, mixed $default): mixed
	{
		return $this->contents[$section] ?? $default;
	}

	public static function getParsedFileNullable(string $directory): ?self
	{
		try {
			return new self($directory);
		} catch (InvalidArgumentException) {
			return null;
		}
	}

}
