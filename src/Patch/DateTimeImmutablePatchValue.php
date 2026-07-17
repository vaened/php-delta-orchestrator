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
 * @extends NormalizablePatchValue<DateTimeImmutable|null>
 */
final readonly class DateTimeImmutablePatchValue extends NormalizablePatchValue
{
    public function __construct(
        bool $present,
        DateTimeInterface|string|null $value,
    )
    {
        parent::__construct($present, $value);
    }

    protected static function normalize(mixed $value): ?DateTimeImmutable
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
