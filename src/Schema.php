<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator;

use Closure;
use Vaened\DeltaOrchestrator\Comparison\Comparator;
use Vaened\DeltaOrchestrator\Patch\PatchValue;

/**
 * @template TPayload
 * @template TCurrent
 */
final readonly class Schema
{
    /**
     * @param TPayload $payload
     * @param TCurrent $current
     */
    public function __construct(
        private mixed $payload,
        private mixed $current,
    )
    {
    }

    /**
     * @return TPayload
     */
    public function payload(): mixed
    {
        return $this->payload;
    }

    /**
     * @return TCurrent
     */
    public function current(): mixed
    {
        return $this->current;
    }

    /**
     * @template TValue
     * @template TCurrentValue
     *
     * @param Closure(TPayload): PatchValue<TValue> $patch
     * @param Closure(TCurrent): TCurrentValue $current
     * @param Comparator|Closure(TValue, TCurrentValue): bool|null $compare
     *
     * @return Field<TValue, TCurrentValue>
     */
    public function define(
        Closure                 $patch,
        Closure                 $current,
        Comparator|Closure|null $compare = null,
    ): Field
    {
        return new Field(
            patch     : fn(): mixed => $patch($this->payload),
            current   : fn(): mixed => $current($this->current),
            comparator: match (true) {
                $compare instanceof Comparator,
                    $compare instanceof Closure => $compare,
                $compare === null               => null,
                default                         => $compare(...),
            },
        );
    }
}
