<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Exceptions;

use function get_debug_type;

final class InvalidPatchValueProvided extends DeltaOrchestratorException
{
    public function __construct(mixed $value)
    {
        parent::__construct(sprintf(
            'Schema patch callback must return PatchValue. Got <%s>.',
            get_debug_type($value),
        ));
    }
}
