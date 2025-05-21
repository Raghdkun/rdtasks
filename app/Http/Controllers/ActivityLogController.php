<?php

namespace App\Http\Controllers;

use App\Models\ProjectActivity;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends AppBaseController
{
    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return Application|Factory|JsonResponse|View
     *
     * @throws \Throwable
     */
    public function index(Request $request)
    {
        $result = [];
        $input = $request->all();

        if ($request->ajax()) {
            $result['activityLogs'] = ProjectActivity::with('createdBy')->orderBy('created_at', 'DESC');

            $result['activityLogs']->when(isset($input['user_id']), function (Builder $query) use ($input) {
                $query->where('causer_id', '=', $input['user_id']);
            });

            $result['activityLogs'] = $result['activityLogs']->paginate(25);
            foreach ($result['activityLogs'] as $activityLog) {
                $result['resultData'][] = json_decode($activityLog->properties);
            }
            try {
                return $this->sendResponse($result, 'data retrieved');
            } catch (Exception $e) {
                return $this->sendError($e, 404);
            }
        }

        return view('activity_logs.index', compact('result'));
    }

    /**
     * @param $id
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy($id)
    {
        /** @var Activity $activityId */
        $activityId = Activity::findOrFail($id);
        $activityId->delete();

        return $this->sendSuccess('record has been deleted');
    }
}
