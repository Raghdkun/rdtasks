<?php

namespace App\Queries;

use App\Models\TimeEntry;
use App\Models\User;
use Auth;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TimeEntryDataTable.
 */
class TimeEntryDataTable
{
    /**
     * @param  array  $input
     * @return TimeEntry|Builder
     */
    public function get($input)
    {
        /** @var TimeEntry $query */
        $query = TimeEntry::with(['task.project', 'user', 'activityType'])
            ->select('time_entries.*')->where('task_id', $input['taskID']);

        /** @var User $user */
        $user = Auth::user();
        $userRole = $user['roles'][0]->name;

        if ($userRole != 'Admin') {
            $query->where('user_id', getLoggedInUserId());
        }

        $query->when(
            isset($input['filter_activity']) && ! empty($input['filter_activity']),
            function (Builder $q) use ($input) {
                $q->where('activity_type_id', $input['filter_activity']);
            }
        );

        $query->when(
            isset($input['filter_date']) && ! empty($input['filter_date']),
            function (Builder $q) use ($input) {
                $timeEntryDate = explode(' - ', $input['filter_date']);
                $q->whereDate('start_time', '>=', $timeEntryDate[0])
                    ->whereDate('end_time', '<=', $timeEntryDate[1]);
            }
        );

        if (! $user->can('manage_time_entries')) {
            return $query->OfCurrentUser();
        }

        return $query;
    }
}
