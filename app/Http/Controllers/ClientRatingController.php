<?php

namespace App\Http\Controllers;

use App\Models\ClientRating;
use App\Models\Task;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClientRatingController extends Controller
{
    /**
     * Display a listing of client ratings
     */
    public function index(Request $request)
    {
        $query = ClientRating::with(['task.project', 'client', 'ratedBy', 'editedBy']);

        // Apply filters
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('task_name')) {
            $query->whereHas('task', function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->task_name . '%');
            });
        }

        if ($request->filled('rating_min')) {
            $query->where('rating', '>=', $request->rating_min);
        }

        if ($request->filled('rating_max')) {
            $query->where('rating', '<=', $request->rating_max);
        }

        $clientRatings = $query->orderBy('created_at', 'desc')->paginate(15);
        $clients = Client::orderBy('name')->get();

        return view('client_ratings.index', compact('clientRatings', 'clients'));
    }

    /**
     * Show the form for creating a new client rating
     */
    public function create()
    {
        $tasks = Task::with(['project', 'client'])
            ->whereHas('project.client')
            ->orderBy('title')
            ->get();
        $clients = Client::orderBy('name')->get();

        return view('client_ratings.create', compact('tasks', 'clients'));
    }

    /**
     * Store a newly created client rating
     */
    public function store(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'client_id' => 'required|exists:clients,id',
            'rating' => 'required|integer|min:1|max:50',
            'comment' => 'nullable|string|max:1000'
        ]);

        // Check if rating already exists
        $existingRating = ClientRating::where('task_id', $request->task_id)
            ->where('client_id', $request->client_id)
            ->first();

        if ($existingRating) {
            return redirect()->back()->withErrors(['error' => 'A rating for this task and client already exists.']);
        }

        ClientRating::create([
            'task_id' => $request->task_id,
            'client_id' => $request->client_id,
            'rated_by' => Auth::id(),
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return redirect()->route('client-ratings.index')
            ->with('success', 'Client rating created successfully.');
    }

    /**
     * Show the form for editing a client rating
     */
    public function edit(ClientRating $clientRating)
    {
        $clientRating->load(['task.project', 'client', 'ratedBy']);
        return view('client_ratings.edit', compact('clientRating'));
    }

    /**
     * Update the specified client rating
     */
    public function update(Request $request, ClientRating $clientRating)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:50',
            'comment' => 'nullable|string|max:1000'
        ]);

        $clientRating->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
            'edited_by' => Auth::id(),
            'edited_at' => now()
        ]);

        return redirect()->route('client-ratings.index')
            ->with('success', 'Client rating updated successfully.');
    }

    /**
     * Remove the specified client rating
     */
    public function destroy(ClientRating $clientRating)
    {
        $clientRating->delete();
        return redirect()->route('client-ratings.index')
            ->with('success', 'Client rating deleted successfully.');
    }

    /**
     * Public page for clients to view their ratings
     */
    public function publicView($clientId, $token = null)
    {
        // You can implement token-based authentication here for security
        $client = Client::findOrFail($clientId);
        
        $ratings = ClientRating::with(['task.project'])
            ->where('client_id', $clientId)
            ->orderBy('created_at', 'desc')
            ->get();

        $averageRating = $ratings->avg('rating');
        $totalRatings = $ratings->count();

        return view('client_ratings.public', compact('client', 'ratings', 'averageRating', 'totalRatings'));
    }
}