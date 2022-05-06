<?php

declare(strict_types=1);

namespace EonX\EasyUtils\Tests\Bridge\Symfony;

use EonX\EasyUtils\Bridge\BridgeConstantsInterface;
use EonX\EasyUtils\SensitiveData\SensitiveDataSanitizerInterface;
use EonX\EasyUtils\Tests\AbstractSensitiveDataSanitizerTestCase;

final class SensitiveDataTest extends AbstractSensitiveDataSanitizerTestCase
{
    use SymfonyTestCaseTrait;

    protected function getSanitizer(?array $keysToMask = null): SensitiveDataSanitizerInterface
    {
        $container = $this->getKernel([BridgeConstantsInterface::PARAM_SENSITIVE_DATA_KEYS_TO_MASK => $keysToMask])
            ->getContainer();

        return $container->get(SensitiveDataSanitizerInterface::class);
    }
}
