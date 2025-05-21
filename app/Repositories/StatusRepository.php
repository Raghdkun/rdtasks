<?php

namespace App\Repositories;

use App\Models\Status;
use App\Models\Tag;
use App\Models\Task;

/**
 * Class TagRepository.
 */
class StatusRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
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

    /**
     * Configure the Model.
     **/
    public function model()
    {
        return Status::class;
    }

    /**
     * @param  array  $input
     * @return bool
     */
    public function store($input)
    {
        $status = Status::latest('created_at')->max('status');
        if (isset($input['name']) && $input['name'] != '') {
            $status = Status::create([
                'name' => $input['name'],
                'order' => $input['order'],
                'status' => $status + 1,
            ]);

            return $status;
        }

        return true;
    }

    /**
     * @return mixed
     */
    public function getTagList()
    {
        return Tag::toBase()->orderBy('name')->pluck('name', 'id');
    }

    /**
     * @param  int  $id
     * @return bool
     */
    public function deleteStatus($id)
    {
        $status = Status::findOrFail($id);
        $tasksCount = Task::where('status', $status->status)->count();
        if ($status->status == 0 || $status->status == 1 || $tasksCount > 0) {
            return false;
        }

        $status->delete();

        return true;
    }
}
