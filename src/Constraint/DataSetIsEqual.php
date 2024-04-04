<?php declare(strict_types=1);
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\DbUnit\Constraint;

use function get_class_methods;
use function get_parent_class;
use function in_array;
use function sprintf;
use PHPUnit\DbUnit\DataSet\IDataSet;
use PHPUnit\DbUnit\InvalidArgumentException;
use PHPUnit\Framework\Constraint\Constraint;

/**
 * Asserts whether or not two dbunit datasets are equal.
 */
class DataSetIsEqual extends Constraint
{
    /**
     * @var IDataSet
     */
    protected $value;

    /**
     * @var string
     */
    protected $failure_reason;

    /**
     * Creates a new constraint.
     */
    public function __construct(IDataSet $value)
    {
        if (in_array('__construct', get_class_methods(get_parent_class($this)), true)) {
            parent::__construct();
        }
        $this->value = $value;
    }

    /**
     * Returns a string representation of the constraint.
     */
    public function toString(): string
    {
        return sprintf(
            'is equal to expected %s',
            $this->value->__toString(),
        );
    }

    /**
     * Evaluates the constraint for parameter $other. Returns TRUE if the
     * constraint is met, FALSE otherwise.
     *
     * This method can be overridden to implement the evaluation algorithm.
     *
     * @param mixed $other value or object to evaluate
     */
    protected function matches($other): bool
    {
        if (!$other instanceof IDataSet) {
            throw new InvalidArgumentException(
                'PHPUnit_Extensions_Database_DataSet_IDataSet expected',
            );
        }

        return $this->value->matches($other);
    }

    /**
     * Returns the description of the failure.
     *
     * The beginning of failure messages is "Failed asserting that" in most
     * cases. This method should return the second part of that sentence.
     *
     * @param mixed $other evaluated value or object
     */
    protected function failureDescription($other): string
    {
        return $other->__toString() . ' ' . $this->toString();
    }
}
