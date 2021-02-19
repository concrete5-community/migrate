<?php

namespace A3020\Migrate\Dto;

final class StructureInformation
{
    /** @var int */
    private $totalTables;

    /** @var int */
    private $totalSize;

    /** @var TableInfo[] */
    protected $tables;

    /**
     * @return int
     */
    public function getTotalTables()
    {
        return (int) $this->totalTables;
    }

    /**
     * @param int $totalTables
     */
    public function setTotalTables($totalTables)
    {
        $this->totalTables = (int) $totalTables;
    }

    /**
     * Total size in bytes.
     *
     * @return int
     */
    public function getTotalSize()
    {
        return (int) $this->totalSize;
    }

    /**
     * @return float
     */
    public function getTotalSizeInMb()
    {
        return round($this->getTotalSize() / 1024 / 1024, 2);
    }

    /**
     * @param int $totalSize
     */
    public function setTotalSize($totalSize)
    {
        $this->totalSize = (int) $totalSize;
    }

    public function addTable(TableInfo $tableInfo)
    {
        $this->tables[] = $tableInfo;
    }

    /**
     * @return TableInfo[]
     */
    public function getTables()
    {
        return $this->tables;
    }
}
