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
final class Schema
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
     * @param callable(TPayload): PatchValue<TValue> $value
     * @param callable(TCurrent): TCurrentValue $current
     * @param Comparator|callable(TValue, TCurrentValue): bool|null $compare
     *
     * @return Field<TValue, TCurrentValue>
     */
    public function define(
        callable                 $value,
        callable                 $current,
        Comparator|callable|null $compare = null,
    ): Field
    {
        return new Field(
            value     : fn(): mixed => $value($this->payload),
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
