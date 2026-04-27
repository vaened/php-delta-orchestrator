<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Exceptions;

use function get_debug_type;

final class StrictComparisonTypeMismatch extends DeltaOrchestratorException
{
    public function __construct(mixed $value, mixed $current)
    {
        parent::__construct(sprintf(
            'Strict comparison requires matching types. Got <%s> and <%s>.',
            get_debug_type($value),
            get_debug_type($current),
        ));
    }
}
