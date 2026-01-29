<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Display the landing page with featured projects.
     */
    public function landing()
    {
        // Featured: Random 3 active projects or top funded
        $featured = Project::where('status', 'active')->inRandomOrder()->take(3)->get();

        // All Projects: Latest active, paginated
        $projects = Project::where('status', 'active')->latest()->paginate(9);

        return view('landing', compact('featured', 'projects'));
    }

    /**
     * Display a public listing of the resource.
     * Shows 'active' projects by default, also 'completed'/'canceled' for history if needed.
     * Filters: status, search.
     */
    /**
     * Display a public listing of the resource.
     * Shows 'active' projects by default, also 'completed'/'canceled' for history if needed.
     * Filters: status, search.
     */
    public function index(Request $request)
    {
        $query = Project::query();

        // Filter by Status: Default to 'active', or allow 'completed', 'canceled'.
        // Exclude 'pending' and 'rejected' from public feed.
        $allowedStatuses = ['active', 'completed', 'canceled'];

        if ($request->has('status') && in_array($request->status, $allowedStatuses)) {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', $allowedStatuses);
        }

        // Search by Title
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Pagination: 9 per page (grid layout usually looks better with 3x3)
        $projects = $query->latest()->paginate(9);

        return view('projects.index', compact('projects'));
    }
    /**
     * Display the entrepreneur's own projects.
     */
    public function myProjects()
    {
        $projects = Auth::user()->projects()->latest()->paginate(10);
        return view('projects.my_projects', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Check limit: Max 2 active/pending projects
        $activeOrPendingCount = Auth::user()->projects()
            ->whereIn('status', ['active', 'pending'])
            ->count();

        if ($activeOrPendingCount >= 2) {
            return redirect()->route('projects.my')->with('error', 'You can only have 2 active or pending projects at a time.');
        }

        return view('projects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Re-check limit to prevent race conditions or bypass
        $activeOrPendingCount = Auth::user()->projects()
            ->whereIn('status', ['active', 'pending'])
            ->count();

        if ($activeOrPendingCount >= 2) {
            return redirect()->route('projects.my')->with('error', 'Limit reached.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'funding_min' => 'required|numeric|min:1',
            'funding_max' => 'required|numeric|gt:funding_min',
            'deadline' => 'required|date|after:today',
            'image_url' => 'required|url',
            'video_url' => 'nullable|url',
        ]);

        Auth::user()->projects()->create([
            'title' => $request->title,
            'description' => $request->description,
            'funding_min' => $request->funding_min,
            'funding_max' => $request->funding_max,
            'funding_current' => 0.00,
            'status' => 'pending', // Waiting for Admin approval
            'deadline' => $request->deadline,
            'image_url' => $request->image_url,
            'video_url' => $request->video_url,
        ]);

        return redirect()->route('projects.my')->with('success', 'Project created successfully! Waiting for approval.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        // Users can see details.
        // If pending/rejected, only owner or admin (or logic to hide)
        if (in_array($project->status, ['pending', 'rejected'])) {
            if (Auth::id() !== $project->user_id && Auth::user()->role !== 'admin') {
                abort(403, 'This project is under review.');
            }
        }

        $project->load('updates', 'user');

        $userInvestments = collect();
        if (Auth::check() && Auth::user()->role === 'investor') {
            $userInvestments = Auth::user()->investments()
                ->where('project_id', $project->id)
                ->latest()
                ->get();
        }

        return view('projects.show', compact('project', 'userInvestments'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        if (Auth::id() !== $project->user_id) {
            abort(403);
        }
        return view('projects.edit', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        if (Auth::id() !== $project->user_id) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'funding_min' => 'required|numeric|min:1',
            'funding_max' => 'required|numeric|gt:funding_min',
            'deadline' => 'required|date|after:today',
            'image_url' => 'required|url',
            'video_url' => 'nullable|url',
        ]);

        // Ignore funding_current, status (unless handled specifically elsewhere)
        $project->update([
            'title' => $request->title,
            'description' => $request->description,
            'funding_min' => $request->funding_min,
            'funding_max' => $request->funding_max,
            'deadline' => $request->deadline,
            'image_url' => $request->image_url,
            'video_url' => $request->video_url,
        ]);

        return redirect()->route('projects.my')->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        if (Auth::id() !== $project->user_id && Auth::user()->role !== 'admin') {
            abort(403);
        }

        // Logic: Can only delete if funding == 0? Or cancel?
        // Requirement: "El usuario EMPRENDEDOR podrá retirar el dinero... el proyecto se pasará al estado de COMPLETADO".
        // "Si el propio emprendedor... cancela el proyecto... dinero devuelto".
        // For now, basic delete or cancel logic. Let's do delete for clean-up or cancel method separate.
        // Assuming destroy is "Delete"

        $project->delete();
        return redirect()->route('projects.my')->with('success', 'Project deleted.');
    }
}
