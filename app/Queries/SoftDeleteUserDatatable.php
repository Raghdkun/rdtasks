<?php

namespace App\Queries;


use App\Models\User;
use phpDocumentor\Reflection\Types\Null_;

/**
 * Class SoftDeleteUserDatatable
 */
class SoftDeleteUserDatatable
{
    public function get()
    {
        $q = User::onlyTrashed();

        return $q;
    }
}
