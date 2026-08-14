<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Exceptions;

use function get_debug_type;

final class InvalidPatchValue extends DeltaOrchestratorException
{
    public static function forInt(mixed $value): self
    {
        return new self(sprintf(
            'Invalid int patch value [%s].',
            get_debug_type($value),
        ));
    }

    public static function forFloat(mixed $value): self
    {
        return new self(sprintf(
            'Invalid float patch value [%s].',
            get_debug_type($value),
        ));
    }

    public static function forBool(mixed $value): self
    {
        return new self(sprintf(
            'Invalid bool patch value [%s].',
            get_debug_type($value),
        ));
    }

    public static function forDateTime(mixed $value): self
    {
        return new self(sprintf(
            'Invalid datetime patch value [%s].',
            get_debug_type($value),
        ));
    }

    public static function forArray(mixed $value): self
    {
        return new self(sprintf(
            'Invalid array patch value [%s].',
            get_debug_type($value),
        ));
    }
}
