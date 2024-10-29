<?php
declare(strict_types=1);

namespace EonX\EasyUtils\Laravel\Middleware;

use Closure;
use EonX\EasyUtils\Common\Trimmer\StringTrimmerInterface;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

final readonly class TrimStringsMiddleware
{
    /**
     * @var string[]
     */
    private array $exceptKeys;

    /**
     * @param string[]|null $exceptKeys
     */
    public function __construct(
        private StringTrimmerInterface $trimmer,
        ?array $exceptKeys = null,
    ) {
        $this->exceptKeys = $exceptKeys ?? [];
    }

    public function handle(Request $request, Closure $next): mixed
    {
        $this->clean($request);

        return $next($request);
    }

    private function clean(Request $request): void
    {
        $this->cleanParameterBag($request->query);

        if ($request->isJson()) {
            $this->cleanParameterBag($request->json());

            return;
        }

        if ($request->request !== $request->query) {
            $this->cleanParameterBag($request->request);
        }
    }

    private function cleanParameterBag(ParameterBag $bag): void
    {
        $trimmedBag = $this->trimmer->trim($bag->all(), $this->exceptKeys);

        $bag->replace($trimmedBag);
    }
}
