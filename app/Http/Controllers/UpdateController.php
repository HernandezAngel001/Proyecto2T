<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateController extends Controller
{
    /**
     * Store a new update (blog post) for a project.
     */
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'photo_url' => 'nullable|url',
        ]);

        $project = Project::findOrFail($request->project_id);

        if (Auth::id() !== $project->user_id) {
            abort(403, 'Unauthorized');
        }

        $project->updates()->create([
            'title' => $request->title,
            'description' => $request->description,
            'photo_url' => $request->photo_url,
        ]);

        return back()->with('success', 'Update posted successfully.');
    }
}
