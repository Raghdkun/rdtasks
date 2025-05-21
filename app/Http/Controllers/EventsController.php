<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateEventRequest;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventsController extends AppBaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|JsonResponse|View
     */
    public function index()
    {
        return view('events.index');
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(CreateEventRequest $request)
    {
        $input = $request->all();
        $input['added_by'] = getLoggedInUserId();

        $event = Event::create($input);
        activity()
            ->causedBy(getLoggedInUser())
            ->withProperties(['modal' => Event::class, 'data' => ''])
            ->performedOn($event)
            ->useLog('Event Created')
            ->log('Created new event '.$input['title']);

        return $this->sendSuccess('Event added successfully');
    }

    /**
     * @return JsonResponse
     */
    public function getEventsData()
    {
        $events = Event::all();
        $data = [];
        foreach ($events as $event) {
            $result['id'] = $event->id;
            $result['title'] = $event->title;
            $result['type'] = $event->type;
            $result['description'] = $event->description;
            $result['start'] = Carbon::parse($event->start_date)->toDateTimeString();
            $result['end'] = Carbon::parse($event->end_date)->toDateTimeString();
            $result['color'] = '#ffffff';
            $result['textColor'] = '#333333';
            $result['borderColor'] = '#d9d9d9';
            $result['start_time'] = Carbon::parse($event->start_date)->format('h:i A');
            $result['end_time'] = Carbon::parse($event->end_date)->format('h:i A');
            $data[] = $result;
        }

        return $this->sendResponse($data, 'Events retrieved successfully.');
    }

    /**
     * @param  Event  $event
     * @return JsonResponse
     */
    public function edit(Event $event)
    {
        return $this->sendResponse($event, 'Events retrieved successfully.');
    }

    /**
     * @param  CreateEventRequest  $request
     * @param  Event  $event
     * @return JsonResponse
     */
    public function update(CreateEventRequest $request, Event $event)
    {
        $input = $request->all();
        $event->update($input);

        return $this->sendSuccess('Event updated successfully');
    }

    /**
     * @param  Request  $request
     * @param  Event  $event
     * @return JsonResponse
     */
    public function dropUpdate(Request $request, Event $event)
    {
        $input = $request->all();
        $event->update($input);

        return $this->sendSuccess('Event updated successfully');
    }

    /**
     * @param  Event  $event
     * @return JsonResponse
     */
    public function destroy(Event $event)
    {
        try {
            $event->delete();
        } catch (\Exception $e) {
        }

        return $this->sendSuccess('Event deleted successfully');
    }
}
