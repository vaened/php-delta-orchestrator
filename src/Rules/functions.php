<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Rules;

use Vaened\DeltaOrchestrator\Field;

function all(array|Field|Rule $rules): All
{
    return new All($rules);
}

function any(array|Field|Rule $rules): Any
{
    return new Any($rules);
}

function present(Field $field): Present
{
    return new Present($field);
}
