<?php
/**
 * @author enea dhack <contact@vaened.dev>
 * @link https://vaened.dev DevFolio
 */

declare(strict_types=1);

namespace Vaened\DeltaOrchestrator;

use Closure;

use function call_user_func;

/**
 * @template TValue
 */
final class LazyValue
{
    private bool $resolved = false;

    /**
     * @var TValue|null
     */
    private mixed $value = null;

    /**
     * @param Closure(): TValue $resolver
     */
    public function __construct(
        private readonly Closure $resolver,
    )
    {
    }

    /**
     * @return TValue
     */
    public function get(): mixed
    {
        if (!$this->resolved) {
            $this->value    = call_user_func($this->resolver);
            $this->resolved = true;
        }

        return $this->value;
    }
}
