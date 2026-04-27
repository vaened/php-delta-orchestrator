<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Tests\Support\Fixtures;

use Vaened\DeltaOrchestrator\Patch\DateTimeImmutablePatchValue;
use Vaened\DeltaOrchestrator\Patch\IntPatchValue;
use Vaened\DeltaOrchestrator\Patch\StringPatchValue;

final readonly class UpdateProfileCommand
{
    public function __construct(
        public StringPatchValue $name,
        public IntPatchValue $age,
        public DateTimeImmutablePatchValue $birthday,
    )
    {
    }
}
