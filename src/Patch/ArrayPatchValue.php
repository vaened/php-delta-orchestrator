<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Patch;

/**
 * @implements PatchValue<array<mixed>|null>
 */
final readonly class ArrayPatchValue implements PatchValue
{
    private bool $present;

    /**
     * @var array<mixed>|null
     */
    private ?array $value;

    /**
     * @param array<mixed>|null $value
     */
    public function __construct(
        bool $present,
        ?array $value,
    ) {
        $this->present = $present;
        $this->value   = self::normalize($value);
    }

    public function isPresent(): bool
    {
        return $this->present;
    }

    /**
     * @return array<mixed>|null
     */
    public function value(): ?array
    {
        return $this->value;
    }

    /**
     * @param array<mixed>|null $value
     * @return array<mixed>|null
     */
    private static function normalize(?array $value): ?array
    {
        if ($value === null) {
            return null;
        }

        $normalized = [];

        foreach ($value as $key => $item) {
            if ($item instanceof PatchValue) {
                $normalized[$key] = $item->value();
                continue;
            }

            if (is_array($item)) {
                $normalized[$key] = self::normalize($item);
                continue;
            }

            $normalized[$key] = $item;
        }

        return $normalized;
    }
}
