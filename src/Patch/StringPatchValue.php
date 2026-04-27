<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Patch;

use Stringable;

/**
 * @implements PatchValue<string|null>
 */
final readonly class StringPatchValue implements PatchValue
{
    private bool $present;

    private ?string $value;

    public function __construct(
        bool $present,
        string|int|float|Stringable|null $value,
    )
    {
        $this->present = $present;
        $this->value   = self::normalize($value);
    }

    public function isPresent(): bool
    {
        return $this->present;
    }

    public function value(): ?string
    {
        return $this->value;
    }

    private static function normalize(string|int|float|Stringable|null $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return (string)$value;
    }
}
