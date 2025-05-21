<?php

namespace App\Repositories;

use App\Models\Tax;

/**
 * Class TaxRepository
 *
 * @version April 6, 2020, 6:48 am UTC
 */
class TaxRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'tax',
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Tax::class;
    }
}
