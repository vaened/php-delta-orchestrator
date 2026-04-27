<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator\Patch;

use DateTimeInterface;
use DateTimeImmutable;
use Exception;
use InvalidArgumentException;

/**
 * @implements PatchValue<DateTimeImmutable|null>
 */
final readonly class DateTimeImmutablePatchValue implements PatchValue
{
    private bool $present;

    private ?DateTimeImmutable $value;

    public function __construct(
        bool $present,
        DateTimeInterface|string|null $value,
    )
    {
        $this->present = $present;
        $this->value   = self::normalize($value);
    }

    public function isPresent(): bool
    {
        return $this->present;
    }

    public function value(): ?DateTimeImmutable
    {
        return $this->value;
    }

    private static function normalize(DateTimeInterface|string|null $value): ?DateTimeImmutable
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof DateTimeImmutable) {
            return $value;
        }

        if ($value instanceof DateTimeInterface) {
            return DateTimeImmutable::createFromInterface($value);
        }

        try {
            return new DateTimeImmutable($value);
        } catch (Exception) {
            throw new InvalidArgumentException(sprintf('Invalid datetime patch value [%s].', $value));
        }
    }
}
