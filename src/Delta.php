<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator;

/**
 * @template TPrevious
 * @template TNext
 */
final readonly class Delta
{
    /**
     * @param TPrevious $previous
     * @param TNext $next
     */
    public function __construct(
        private mixed $previous,
        private mixed $next,
    ) {
    }

    /**
     * @return TPrevious
     */
    public function previous(): mixed
    {
        return $this->previous;
    }

    /**
     * @return TNext
     */
    public function next(): mixed
    {
        return $this->next;
    }
}
