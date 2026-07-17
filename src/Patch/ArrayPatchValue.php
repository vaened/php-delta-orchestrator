<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Patch;

/**
 * @extends PatchValue<array<mixed>|null>
 */
final readonly class ArrayPatchValue extends PatchValue
{
    /**
     * @param array<mixed>|null $value
     */
    public function __construct(
        bool $present,
        ?array $value,
    ) {
        parent::__construct($present, $value);
    }

    /**
     * @param array<mixed>|null $value
     * @return array<mixed>|null
     */
    protected static function normalize(mixed $value): ?array
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
