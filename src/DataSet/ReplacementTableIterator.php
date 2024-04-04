<?php declare(strict_types=1);
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\DbUnit\DataSet;

use Iterator;
use OuterIterator;

/**
 * The default table iterator.
 */
class ReplacementTableIterator implements ITableIterator, OuterIterator
{
    /**
     * @var ITableIterator
     */
    protected $innerIterator;

    /**
     * @var array
     */
    protected $fullReplacements;

    /**
     * @var array
     */
    protected $subStrReplacements;

    /**
     * Creates a new replacement table iterator object.
     */
    public function __construct(ITableIterator $innerIterator, array $fullReplacements = [], array $subStrReplacements = [])
    {
        $this->innerIterator      = $innerIterator;
        $this->fullReplacements   = $fullReplacements;
        $this->subStrReplacements = $subStrReplacements;
    }

    /**
     * Adds a new full replacement.
     *
     * Full replacements will only replace values if the FULL value is a match
     *
     * @param string $value
     * @param string $replacement
     */
    public function addFullReplacement($value, $replacement): void
    {
        $this->fullReplacements[$value] = $replacement;
    }

    /**
     * Adds a new substr replacement.
     *
     * Substr replacements will replace all occurances of the substr in every column
     *
     * @param string $value
     * @param string $replacement
     */
    public function addSubStrReplacement($value, $replacement): void
    {
        $this->subStrReplacements[$value] = $replacement;
    }

    /**
     * Returns the current table.
     *
     * @return ITable
     */
    public function getTable()
    {
        return $this->current();
    }

    /**
     * Returns the current table's meta data.
     *
     * @return ITableMetadata
     */
    public function getTableMetaData()
    {
        $this->current()->getTableMetaData();
    }

    /**
     * Returns the current table.
     *
     * @return ITable
     */
    public function current(): mixed
    {
        return new ReplacementTable($this->innerIterator->current(), $this->fullReplacements, $this->subStrReplacements);
    }

    /**
     * Returns the name of the current table.
     *
     * @return string
     */
    public function key(): mixed
    {
        return $this->current()->getTableMetaData()->getTableName();
    }

    /**
     * advances to the next element.
     */
    public function next(): void
    {
        $this->innerIterator->next();
    }

    /**
     * Rewinds to the first element.
     */
    public function rewind(): void
    {
        $this->innerIterator->rewind();
    }

    /**
     * Returns true if the current index is valid.
     */
    public function valid(): bool
    {
        return $this->innerIterator->valid();
    }

    public function getInnerIterator(): ?Iterator
    {
        return $this->innerIterator;
    }
}
