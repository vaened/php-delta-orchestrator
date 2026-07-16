<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Exceptions;

use Closure;
use Throwable;
use Vaened\DeltaOrchestrator\ActionFailure;
use Vaened\DeltaOrchestrator\Bindings\Behavior;
use Vaened\DeltaOrchestrator\ExecutionResult;
use Vaened\DeltaOrchestrator\Field;

final class ActionBehaviorNotSatisfied extends DeltaOrchestratorException implements ActionFailure
{
    public function __construct(
        private readonly Behavior         $behavior,
        private readonly Field            $field,
        private readonly ?string          $actionDescription = null,
        private readonly ?ExecutionResult $progressUntilFailure = null,
        private readonly ?Closure         $failureFactory = null,
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

    /**
     * @return (Closure(ActionFailure): Throwable)|null
     */
    public function failureFactory(): ?Closure
    {
        return $this->failureFactory;
    }

    public function rethrow(): never
    {
        throw $this->failureFactory !== null
            ? ($this->failureFactory)($this)
            : $this;
    }
}
