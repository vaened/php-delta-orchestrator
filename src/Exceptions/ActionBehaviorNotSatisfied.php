<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Exceptions;

final class ActionBehaviorNotSatisfied extends DeltaOrchestratorException
{
    public function __construct(
        ?string $description = null,
    )
    {
        parent::__construct(
            $description !== null
                ? "Action behavior was not satisfied: {$description}."
                : 'Action behavior was not satisfied.',
        );
    }
}
