<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator;

use Closure;
use Throwable;
use Vaened\DeltaOrchestrator\Bindings\Behavior;
use Vaened\DeltaOrchestrator\Exceptions\InvalidActionDefinition;
use Vaened\DeltaOrchestrator\Rules\Rule;

final class Action
{
    /**
     * @param array<int, Field|Behavior> $fields
     * @param Closure(Field ...$fields): mixed $apply
     * @param (Closure(Field ...$fields): Rule)|null $when
     * @param string|null $description
     * @param (Closure(ActionFailure): Throwable)|null $failureFactory
     */
    public function __construct(
        private readonly array   $fields,
        private readonly Closure $apply,
        private ?Closure         $when = null,
        private ?string          $description = null,
        private ?Closure         $failureFactory = null,
    )
    {
        if ($this->fields === []) {
            throw new InvalidActionDefinition(
                reason     : 'Action fields cannot be empty',
                description: $this->description,
            );
        }
    }

    /**
     * @param array<int, Field|Behavior> $fields
     * @param Closure(Field ...$fields): mixed $apply
     */
    public static function from(array $fields, Closure $apply): self
    {
        return new self(
            fields: $fields,
            apply : $apply,
        );
    }

    /**
     * @param array<int, Field|Behavior> $fields
     */
    public static function for(array $fields): Scope
    {
        return Scope::from($fields);
    }

    /**
     * @return array<int, Field|Behavior>
     */
    public function fields(): array
    {
        return $this->fields;
    }

    /**
     * @return Closure(Field ...$fields): mixed
     */
    public function apply(): Closure
    {
        return $this->apply;
    }

    /**
     * @return (Closure(Field ...$fields): Rule)|null
     */
    public function condition(): ?Closure
    {
        return $this->when;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    /**
     * @return (Closure(ActionFailure): Throwable)|null
     */
    public function failureFactory(): ?Closure
    {
        return $this->failureFactory;
    }

    /**
     * @param Closure(Field ...$fields): Rule $when
     */
    public function when(Closure $when): self
    {
        $this->when = $when;

        return $this;
    }

    public function describe(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param Closure(ActionFailure): Throwable $failureFactory
     */
    public function or(Closure $failureFactory): self
    {
        $this->failureFactory = $failureFactory;

        return $this;
    }
}
