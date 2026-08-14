<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Exceptions;

final class InvalidActionDefinition extends DeltaOrchestratorException
{
    private function __construct(
        string  $reason,
        ?string $description = null,
    )
    {
        parent::__construct(
            $description !== null
                ? "Invalid action definition: $reason ($description)."
                : "Invalid action definition: $reason.",
        );
    }

    public static function emptyFields(?string $description = null): self
    {
        return new self(
            reason     : 'Action fields cannot be empty',
            description: $description,
        );
    }

    public static function unexpectedWhenResult(?string $description = null): self
    {
        return new self(
            reason     : 'Action when condition must return a Rule',
            description: $description,
        );
    }
}
