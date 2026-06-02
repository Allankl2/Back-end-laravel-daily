<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class NoteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $notes = $request->user()->notes()->latest()->limit(10)->get();

            return response()->json(['notes' => $notes]);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'tags'        => 'required|array|min:1',
            'tags.*'      => 'string',
        ]);

        try {
            $note = $request->user()->notes()->create($data);

            return response()->json(['note' => $note], 201);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
