<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Tests\Support\Fixtures;

use DateTimeImmutable;

final readonly class ProfileState
{
    public function __construct(
        public string $name,
        public int $age,
        public DateTimeImmutable $birthday,
    )
    {
    }
}
