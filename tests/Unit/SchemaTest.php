<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Tests\Unit;

use Vaened\DeltaOrchestrator\Exceptions\InvalidPatchValueProvided;
use Vaened\DeltaOrchestrator\Patch\StringPatchValue;
use Vaened\DeltaOrchestrator\Schema;
use Vaened\DeltaOrchestrator\Tests\TestCase;

final class SchemaTest extends TestCase
{
    public function test_it_throws_when_patch_callback_does_not_return_patch_value(): void
    {
        $schema = new Schema(
            payload: ['name' => 'Juan'],
            current: ['name' => 'Pedro'],
        );

        $this->expectException(InvalidPatchValueProvided::class);
        $this->expectExceptionMessage('Schema patch callback must return PatchValue. Got <string>.');

        $schema->define(
            patch  : static fn(array $payload) => $payload['name'],
            current: static fn(array $current) => $current['name'],
        );
    }

    public function test_it_accepts_patch_value_from_patch_callback(): void
    {
        $schema = new Schema(
            payload: ['name' => 'Juan'],
            current: ['name' => 'Pedro'],
        );

        $name = $schema->define(
            patch  : static fn(array $payload) => new StringPatchValue(true, $payload['name']),
            current: static fn(array $current) => $current['name'],
        );

        self::assertTrue($name->isPresent());
        self::assertSame('Juan', $name->value());
        self::assertSame('Pedro', $name->current());
    }
}
