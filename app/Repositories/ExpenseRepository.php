<?php

namespace App\Repositories;

use App\Models\Expense;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class ExpenseRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'category',
    ];

    /**
     * Return searchable fields.
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    public function model()
    {
        return Expense::class;
    }

    /**
     * @param $input
     * @return bool
     */
    public function store($input)
    {
        $input['description'] = is_null($input['description']) ? '' : $input['description'];
        $input['date'] = is_null($input['date']) ? Carbon::now() : $input['date'];
        $input['created_by'] = getLoggedInUserId();

        $expense = Expense::create($input);
        try {
            /** @var Media $attachment */
            if (! empty($input['attachment'])) {
                foreach ($input['attachment'] as $files) {
                    $attachment = $expense->addMedia($files)->toMediaCollection(Expense::ATTACHMENT_PATH, config('app.media_disc'));
                    $attachment['file_url'] = $attachment->getFullUrl();
                }
            }
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }

        return $expense->fresh();
    }

    /**
     * @param  array  $input
     * @param  int  $id
     * @return Builder|Builder[]|Collection|Model
     */
    public function update($input, $id)
    {
        $expense = Expense::find($id);
        $input['description'] = is_null($input['description']) ? '' : $input['description'];
        $input['date'] = is_null($input['date']) ? Carbon::now() : $input['date'];
        $input['billable'] = isset($input['billable']) ? true : false;
        $expense->update($input);
        try {
            /** @var Media $attachment */
            if (! empty($input['attachment'])) {
                foreach ($input['attachment'] as $files) {
                    $expense->addMedia($files)->toMediaCollection(Expense::ATTACHMENT_PATH, config('app.media_disc'));
                }
            }
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }

        return $expense->fresh();
    }
}
