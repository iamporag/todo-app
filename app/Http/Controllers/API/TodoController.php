<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Todo;

class TodoController extends Controller
{
     // Fetch ALL TODO
    public function index(Request $request)
    {
        // per_page query param (default 10)

        $perPage = $request->query('per_page',10);
        $search = $request->query('q');

        $todos = Todo::when($search, function($query,$search) {
            $query->where('title','LIKE',"%{$search}%");
        })
        -> paginate($perPage);

        return response()->json([
            'message' => 'Todo fetched successfully',
            'result' => [
                'data' => $todos->items(),
                      'meta' => [
                'total' => $todos->total(),
                'per_page' => $todos->perPage(),
                'current_page' => $todos->currentPage(),
                'last_page' => $todos->lastPage(),
                'from' => $todos->firstItem(),
                'to' => $todos->lastItem(),
            ],
            'links' => [
                'first' => $todos->url(1),
                'last' => $todos->url($todos->lastPage()),
                'prev' => $todos->previousPageUrl(),
                'next' => $todos->nextPageUrl(),
            ],
            ]
        ], 200);
    }

    // Create New TODO
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $todo = Todo::create([
            'title' => $validated['title'],
            'completed' => false,
        ]);

        $success = "Todo created successfully";

        return response()->json([
            "message" => "Todo created successfully",
            "result" => null,
        ],200);
    }

    // Fetch Single TODO
    public function show(Todo $todo)
    {
        return response()->json([
            'message' => 'Todo fetched successfully',
            'result' => [
                'data' => $todo,
            ]
        ],200);
    }

    // Update TODO
    public function update(Request $request, Todo $todo)
    {
        $validated = $request->validate([
          'title'=> 'sometimes|required|string|max:255',
          'completed' => 'sometimes|boolean',
        ]);

        $todo->update($validated);
        return response()->json([
            'message' => 'Todo updated successfully',
            'result' => null,
        ],200);
    }

    // Delete TODO
    public function destroy(Todo $todo)
    {
        $todo->delete();
        return response()->json(
            [
                'message' => 'Todo deleted successfully',
                'result' => null,
            
        ],200);
    }
}
