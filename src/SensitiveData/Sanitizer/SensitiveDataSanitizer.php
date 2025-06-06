<?php
declare(strict_types=1);

namespace EonX\EasyUtils\SensitiveData\Sanitizer;

use EonX\EasyUtils\Common\Helper\CollectorHelper;
use EonX\EasyUtils\SensitiveData\Hydrator\ObjectHydratorInterface;
use EonX\EasyUtils\SensitiveData\Transformer\ObjectTransformerInterface;

final readonly class SensitiveDataSanitizer implements SensitiveDataSanitizerInterface
{
    /**
     * @var string[]
     */
    private array $keysToMask;

    /**
     * @var \EonX\EasyUtils\SensitiveData\Transformer\ObjectTransformerInterface[]
     */
    private array $objectTransformers;

    /**
     * @var \EonX\EasyUtils\SensitiveData\Sanitizer\StringSanitizerInterface[]
     */
    private array $stringSanitizers;

    /**
     * @param string[] $keysToMask
     */
    public function __construct(
        array $keysToMask,
        private string $maskPattern,
        ?iterable $objectTransformers = null,
        ?iterable $stringSanitizers = null,
    ) {
        $this->keysToMask = \array_map(
            static fn (string $keyToMask): string => \mb_strtolower($keyToMask),
            $keysToMask
        );
        $this->objectTransformers = CollectorHelper::orderLowerPriorityFirstAsArray(
            CollectorHelper::filterByClass($objectTransformers ?? [], ObjectTransformerInterface::class)
        );
        $this->stringSanitizers = CollectorHelper::orderLowerPriorityFirstAsArray(
            CollectorHelper::filterByClass($stringSanitizers ?? [], StringSanitizerInterface::class)
        );
    }

    public function sanitize(mixed $data): mixed
    {
        if (\is_array($data)) {
            return $this->sanitizeArray($data);
        }

        if (\is_object($data)) {
            return $this->sanitizeObject($data);
        }

        if (\is_string($data)) {
            $decodedJson = \json_decode(
                json: $data,
                associative: true,
                flags: \JSON_BIGINT_AS_STRING
            );

            if (\is_array($decodedJson)) {
                return \json_encode($this->sanitizeArray($decodedJson), \JSON_THROW_ON_ERROR);
            }

            $decodedJson = \json_decode(
                json: \stripslashes($data),
                associative: true,
                flags: \JSON_BIGINT_AS_STRING
            );

            if (\is_array($decodedJson)) {
                return \json_encode($this->sanitizeArray($decodedJson), \JSON_THROW_ON_ERROR);
            }

            return $this->sanitizeString($data);
        }

        return $data;
    }

    private function sanitizeArray(array $data): array
    {
        foreach ($data as $key => $value) {
            $data[$key] = \in_array(\mb_strtolower((string)$key), $this->keysToMask, true)
                ? $this->maskPattern
                : $this->sanitize($value);
        }

        return $data;
    }

    /**
     * @template T of object
     *
     * @param T $object
     *
     * @return array|T
     */
    private function sanitizeObject(object $object): array|object
    {
        foreach ($this->objectTransformers as $objectTransformer) {
            if ($objectTransformer->supports($object)) {
                $sanitizedData = $this->sanitizeArray($objectTransformer->transform($object));

                return $objectTransformer instanceof ObjectHydratorInterface
                    ? $objectTransformer->hydrate($object, $sanitizedData)
                    : $sanitizedData;
            }
        }

        return $object;
    }

    private function sanitizeString(string $string): string
    {
        foreach ($this->stringSanitizers as $stringSanitizer) {
            $string = $stringSanitizer->sanitizeString($string, $this->maskPattern, $this->keysToMask);
        }

        return $string;
    }
}
