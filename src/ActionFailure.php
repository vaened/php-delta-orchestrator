<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator;

use Throwable;
use Vaened\DeltaOrchestrator\Bindings\Behavior;

interface ActionFailure extends Throwable
{
    public function behavior(): Behavior;

    public function field(): Field;

    public function actionDescription(): ?string;

    public function progressUntilFailure(): ?ExecutionResult;
}
