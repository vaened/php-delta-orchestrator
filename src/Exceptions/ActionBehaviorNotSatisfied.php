<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Exceptions;

use Vaened\DeltaOrchestrator\Bindings\Behavior;
use Vaened\DeltaOrchestrator\ExecutionResult;
use Vaened\DeltaOrchestrator\Field;

final class ActionBehaviorNotSatisfied extends DeltaOrchestratorException
{
    public function __construct(
        private readonly Behavior        $behavior,
        private readonly Field           $field,
        private readonly ?string         $actionDescription = null,
        private readonly ?ExecutionResult $progressUntilFailure = null,
    )
    {
        parent::__construct(
            $this->actionDescription !== null
                ? "Action behavior was not satisfied: $this->actionDescription."
                : 'Action behavior was not satisfied.',
        );
    }

    public function actionDescription(): ?string
    {
        return $this->actionDescription;
    }

    public function behavior(): Behavior
    {
        return $this->behavior;
    }

    public function field(): Field
    {
        return $this->field;
    }

    public function progressUntilFailure(): ?ExecutionResult
    {
        return $this->progressUntilFailure;
    }
}
