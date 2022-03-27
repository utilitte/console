<?php declare(strict_types = 1);

namespace Utilitte\Console;

use InvalidArgumentException;

final class SemanticVersion
{

	private string $prefix;

	private int $major;

	private ?int $minor = null;

	private ?int $patch = null;

	public function __construct(string $version)
	{
		if (!preg_match('#(v)?(\d+)(?:\.(\d+))?(?:\.(\d+))?#', $version, $matches, PREG_UNMATCHED_AS_NULL)) {
			throw new InvalidArgumentException(sprintf('Version %s is not semver.', $version));
		}

		$this->prefix = $matches[1];
		$this->major = (int) $matches[2];
		$this->minor = $this->convertStringToInt($matches[3]);
		$this->patch = $this->convertStringToInt($matches[4]);
	}

	public function withMajorIncrease(): self
	{
		return $this->withMajorAddition(1)
			->withMinor(0)
			->withPatch(0);
	}

	public function withMinorIncrease(): self
	{
		return $this->withMinorAddition(1)
			->withPatch(0);
	}

	public function withPatchIncrease(): self
	{
		return $this->withPatchAddition(1);
	}

	public function withMajor(int $number): self
	{
		$clone = clone $this;
		$clone->major = $number;

		return $clone;
	}

	public function withMinor(int $number): self
	{
		$clone = clone $this;
		$clone->minor = $number;

		return $clone;
	}

	public function withPatch(int $number): self
	{
		$clone = clone $this;
		$clone->patch = $number;

		return $clone;
	}

	public function withMajorAddition(int $addition): self
	{
		$clone = clone $this;
		$clone->major += $addition;

		return $clone;
	}

	public function withMinorAddition(int $addition): self
	{
		$clone = clone $this;
		$clone->minor += $addition;

		return $clone;
	}

	public function withPatchAddition(int $addition): self
	{
		$clone = clone $this;
		$clone->patch += $addition;

		return $clone;
	}

	private function convertStringToInt(?string $version): ?int
	{
		return $version === null ? null : (int) $version;
	}

	public function compareWith(self $version): int
	{
		return self::compare($this, $version);
	}

	public static function compare(self $version1, self $version2): int
	{
		if ($version1->major > $version2->major) {
			return 1;
		} else if ($version1->major < $version2->major) {
			return -1;
		}

		if ($version1->minor > $version2->minor) {
			return 1;
		} else if ($version1->minor < $version2->minor) {
			return -1;
		}

		if ($version1->patch > $version2->patch) {
			return 1;
		} else if ($version1->patch < $version2->patch) {
			return -1;
		}

		return 0;
	}

	public function __toString(): string
	{
		$version = $this->prefix . max(0, $this->major);

		if ($this->minor !== null) {
			$version .= '.' . max(0, $this->minor);
		}

		if (!$this->patch !== null) {
			$version .= '.' . max(0, $this->patch);
		}

		return $version;
	}

}
