<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Exceptions;

final class InvalidActionDefinition extends DeltaOrchestratorException
{
    public function __construct(
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
}
