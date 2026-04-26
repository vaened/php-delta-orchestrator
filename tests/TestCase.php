<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Vaened\DeltaOrchestrator\Tests\Support\CreatesFields;

abstract class TestCase extends PHPUnitTestCase
{
    use CreatesFields;
}
