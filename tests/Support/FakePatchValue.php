<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Tests\Support;

use Vaened\DeltaOrchestrator\Patch\PatchValue;

/**
 * @template TValue
 *
 * @extends PatchValue<TValue>
 */
final readonly class FakePatchValue extends PatchValue
{
    /**
     * @param TValue $value
     */
    public function __construct(
        bool $present,
        mixed $value,
    )
    {
        parent::__construct($present, $value);
    }

    protected static function normalize(mixed $value): mixed
    {
        return $value;
    }
}
