<?php

namespace A3020\Migrate\Payload;

use A3020\Migrate\Database\ListTables;

final class Structure
{
    /**
     * @var ListTables
     */
    private $listTables;

    public function __construct(ListTables $listTables)
    {
        $this->listTables = $listTables;
    }

    /**
     * Used to export information about all tables.
     *
     * @return array
     */
    public function get()
    {
        $tables = $this->listTables->get();
        $totalSize = array_sum(array_column($tables, 'size'));

        return [
            'total_tables' => count($tables),
            'total_size' => $totalSize,
            'tables' => $tables,
        ];
    }
}
