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
use PHPUnit\Framework\Constraint\Constraint;

/**
 * Asserts the row count in a table.
 */
class TableRowCount extends Constraint
{
    /**
     * @var int
     */
    protected $value;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * Creates a new constraint.
     */
    public function __construct($tableName, $value)
    {
        if (in_array('__construct', get_class_methods(get_parent_class($this)), true)) {
            parent::__construct();
        }
        $this->tableName = $tableName;
        $this->value     = $value;
    }

    /**
     * Returns a string representation of the constraint.
     */
    public function toString(): string
    {
        return sprintf('is equal to expected row count %d', $this->value);
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
        return $other == $this->value;
    }
}
