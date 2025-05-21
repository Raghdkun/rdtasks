<?php

namespace App\Http\Livewire;

use App\Models\Status;
use App\Models\Task;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Component;

class Kanban extends Component
{
    public $allTasks;

    public $taskStatus;

    public $tags;

    public $project = ' ';

    public $userFilter;

    /**
     * @return Application|Factory|View
     */
    public function render()
    {
        $query = Task::with(['taskAssignee', 'comments', 'media']);
        $query->when(! empty($this->project), function (Builder $q) {
            $q->where('project_id', $this->project);
        });
        if(request()->route()->url == 'tasks'){
            $query->when(! empty($this->userFilter), function (Builder $q) {
                $q->whereHas('taskAssignee', function (Builder $q) {
                    $q->where('user_id', $this->userFilter);
                });
            });
        }
        
        $this->allTasks = [];
        $this->allTasks = $query->where('project_id', $this->project)->get();
        $this->taskStatus = Status::toBase()->orderBy('order', 'ASC')->get();

        return view('livewire.kanban');
    }

    protected $listeners = [
        'loadByProject',
        'loadByUser',
        'refresh' => '$refresh',
    ];

    /**
     * @param $projectId
     */
    public function loadByProject($projectId)
    {
        $this->project = $projectId;
    }

    /**
     * @param $userId
     */
    public function loadByUser($userId)
    {
        $this->userFilter = $userId;
    }
}
