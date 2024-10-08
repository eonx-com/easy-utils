<?php
declare(strict_types=1);

namespace EonX\EasyUtils\Math\Constraint;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class NumberGreaterOrEqual extends AbstractNumberComparison
{
    public string $message = 'number.should_be_greater_or_equal';
}
