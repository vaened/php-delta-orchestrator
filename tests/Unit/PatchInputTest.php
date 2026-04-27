<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Tests\Unit;

use DateTimeImmutable;
use Vaened\DeltaOrchestrator\Patch\PatchInput;
use Vaened\DeltaOrchestrator\Tests\TestCase;

final class PatchInputTest extends TestCase
{
    public function test_it_creates_string_patch_values_from_array_input(): void
    {
        $input = new PatchInput(
            input       : ['name' => 'Juan'],
            expectedKeys: ['name'],
        );

        $name = $input->string('name');

        self::assertTrue($name->isPresent());
        self::assertSame('Juan', $name->value());
    }

    public function test_it_marks_missing_keys_as_absent(): void
    {
        $input = new PatchInput(
            input       : ['name' => 'Juan'],
            expectedKeys: ['name'],
        );

        $age = $input->int('age');

        self::assertFalse($age->isPresent());
        self::assertNull($age->value());
    }

    public function test_it_uses_received_keys_to_detect_presence(): void
    {
        $input = new PatchInput(
            input       : ['name' => 'Juan', 'age' => 20],
            expectedKeys: ['name'],
        );

        self::assertTrue($input->string('name')->isPresent());
        self::assertFalse($input->int('age')->isPresent());
    }

    public function test_it_creates_typed_patch_values(): void
    {
        $input = new PatchInput(
            input       : [
                'age'       => '20',
                'score'     => '10.5',
                'enabled'   => 'true',
                'startDate' => '2026-04-27 10:20:30',
            ],
            expectedKeys: ['age', 'score', 'enabled', 'startDate'],
        );

        self::assertSame(20, $input->int('age')->value());
        self::assertSame(10.5, $input->float('score')->value());
        self::assertTrue($input->bool('enabled')->value());
        self::assertInstanceOf(DateTimeImmutable::class, $input->dateTimeImmutable('startDate')->value());
    }
}
