<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator;

use function in_array;
use function count;

final readonly class ExecutionResult
{
    /**
     * @param list<string> $executed
     * @param list<string> $skipped
     */
    public function __construct(
        private int   $total,
        private array $executed,
        private array $skipped,
    ) {
    }

    public function total(): int
    {
        return $this->total;
    }

    public function executed(): int
    {
        return count($this->executed);
    }

    public function skipped(): int
    {
        return count($this->skipped);
    }

    public function hasExecutedAny(): bool
    {
        return $this->executed() > 0;
    }

    public function hasSkippedAny(): bool
    {
        return $this->skipped() > 0;
    }

    public function wasExecuted(string $description): bool
    {
        return in_array($description, $this->executed, true);
    }

    public function wasSkipped(string $description): bool
    {
        return in_array($description, $this->skipped, true);
    }

    /**
     * @return list<string>
     */
    public function skippedDescriptions(): array
    {
        return $this->skipped;
    }
}
