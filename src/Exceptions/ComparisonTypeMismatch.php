<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Exceptions;

use function get_debug_type;

final class ComparisonTypeMismatch extends DeltaOrchestratorException
{
    public static function forStrict(mixed $value, mixed $current): self
    {
        return new self(sprintf(
            'Strict comparison requires matching types. Got <%s> and <%s>.',
            get_debug_type($value),
            get_debug_type($current),
        ));
    }

    public static function forDateTime(mixed $value, mixed $current): self
    {
        return new self(sprintf(
            'DateTime comparison requires DateTimeInterface or parseable date strings. Got <%s> and <%s>.',
            get_debug_type($value),
            get_debug_type($current),
        ));
    }

    public static function forNumeric(mixed $value, mixed $current): self
    {
        return new self(sprintf(
            'Numeric comparison requires int, finite float, or numeric string values. Got <%s> and <%s>.',
            get_debug_type($value),
            get_debug_type($current),
        ));
    }

    public static function forArray(mixed $value, mixed $current): self
    {
        return new self(sprintf(
            'Array comparison requires array values on both sides. Got <%s> and <%s>.',
            get_debug_type($value),
            get_debug_type($current),
        ));
    }
}
