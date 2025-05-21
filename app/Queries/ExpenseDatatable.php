<?php

namespace App\Queries;

use App\Models\Expense;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ReportDataTable.
 */
class ExpenseDatatable
{
    /**
     * @return Builder
     */
    public function get()
    {
        $q = Expense::with(['user', 'client', 'project'])->select('expenses.*');

        return $q;
    }
}
