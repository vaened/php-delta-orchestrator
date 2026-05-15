<?php

declare(strict_types=1);

final class ScenarioReporter
{
    /** @var list<string> */
    private array $executed = [];

    /** @var list<string> */
    private array $skipped  = [];

    /** @var list<string> */
    private array $failures = [];

    public function executed(string $message): void
    {
        $this->executed[] = $message;
    }

    public function skipped(string $message): void
    {
        $this->skipped[] = $message;
    }

    public function failure(string $message): void
    {
        $this->failures[] = $message;
    }

    public function dump(): void
    {
        echo "=== EXECUTED ACTIONS ===\n";
        foreach ($this->executed as $e) {
            echo "✔ $e\n";
        }

        echo "\n=== SKIPPED ACTIONS ===\n";
        foreach ($this->skipped as $s) {
            echo "➖ $s\n";
        }

        echo "\n=== FAILURES ===\n";
        foreach ($this->failures as $f) {
            echo "✖ $f\n";
        }
    }
}
