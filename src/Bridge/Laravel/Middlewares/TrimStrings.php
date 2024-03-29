<?php
declare(strict_types=1);

namespace EonX\EasyUtils\Bridge\Laravel\Middlewares;

use Closure;
use EonX\EasyUtils\StringTrimmers\StringTrimmerInterface;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

final class TrimStrings
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
