<?php

namespace A3020\Migrate\Dto;

final class TableInfo
{
    /** @var string */
    private $name;

    /** @var int */
    private $size;

    /** @var int */
    private $totalRows;

    /** @var string */
    private $sqlStructure;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Table size in bytes.
     *
     * @return int
     */
    public function getSize()
    {
        return (int) $this->size;
    }

    /**
     * @param int $size
     */
    public function setSize($size)
    {
        $this->size = (int) $size;
    }

    /**
     * This is derived from INFORMATION_SCHEMA and not 100% accurate!
     *
     * @return int
     */
    public function getTotalRows()
    {
        return (int) $this->totalRows;
    }

    /**
     * @param int $totalRows
     */
    public function setTotalRows($totalRows)
    {
        $this->totalRows = (int) $totalRows;
    }

    /**
     * @return string
     */
    public function getSqlStructure()
    {
        return $this->sqlStructure;
    }

    /**
     * @param string $sqlStructure
     */
    public function setSqlStructure($sqlStructure)
    {
        $this->sqlStructure = $sqlStructure;
    }
}
