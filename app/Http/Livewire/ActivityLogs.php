<?php

namespace App\Http\Livewire;

use App\Models\ProjectActivity;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class ActivityLogs extends Component
{
    public $userId;

    public $search = '';

    protected $listeners = [
        'refresh' => '$refresh',
        'userFilter',
    ];

    /**
     * @return array
     */
    public function searchableFields()
    {
        return [
            'log_name',
            'description',
        ];
    }

    /**
     * @return string
     */
    public function model()
    {
        return ProjectActivity::class;
    }

    public function updatingSearch()
    {
        $this->render();
    }

    /**
     * @param $id
     */
    public function userFilter($id)
    {
        $this->userId = $id;
    }

    public function render()
    {
        $result = [];
        $result['activityLogs'] = $this->searchActivity();
        $result['users'] = User::whereOwnerId(null)->whereOwnerType(null)->whereIsActive(true)->where('email_verified_at',
            '!=', null)->orderBy('name', 'asc')->pluck('name', 'id');

        foreach ($result['activityLogs'] as $activityLog) {
            $result['resultData'][] = json_decode($activityLog->properties);
        }

        return view('livewire.activity-logs', compact('result'));
    }

    public function searchActivity()
    {
        $query = ProjectActivity::with('createdBy')->orderBy('created_at', 'DESC');

        $query->when(! empty($this->search), function (Builder $q) {
            $q->where(function (Builder $q) {
                $q->Where('log_name', 'like', '%'.$this->search.'%');
                $q->orWhere('description', 'like', '%'.$this->search.'%');
            });
        });

        $query->when($this->userId != null && $this->userId != '', function (Builder $q) {
            $q->where('causer_id', '=', $this->userId);
        });

        return $query->paginate(25);
    }
}
