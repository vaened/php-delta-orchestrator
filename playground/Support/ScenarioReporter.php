<?php

declare(strict_types=1);

final class ScenarioReporter
{
    private array $executed = [];

    private array $skipped  = [];

    private array $failures = [];

    public function executed(string $message): void
    {
        $this->executed[] = $message;
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

        echo "\n=== FAILURES ===\n";
        foreach ($this->failures as $f) {
            echo "✖ $f\n";
        }
    }
}