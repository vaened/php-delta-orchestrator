<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Rules;

use Closure;
use Vaened\DeltaOrchestrator\Field;

final readonly class Boolean implements Rule
{
    private function __construct(
        private bool $value,
    )
    {
    }

    public static function from(bool $value): Closure
    {
        return static fn(Field ...$fields): self => new self($value);
    }

    /**
     * @param Closure(Field ...$fields): bool $resolver
     * @return Closure(Field ...$fields): Boolean
     */
    public static function resolve(Closure $resolver): Closure
    {
        return static fn(Field ...$fields): self => new self($resolver(...$fields));
    }

    public function satisfies(): bool
    {
        return $this->value;
    }
}
