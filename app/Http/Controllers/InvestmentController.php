<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvestmentController extends Controller
{
    /**
     * Add funds to the user's wallet.
     * Simple simulation for this exercise.
     */
    public function addFunds(Request $request)
    {
        $request->validate(['amount' => 'required|numeric|min:1']);

        $user = Auth::user();
        if ($user->role !== 'investor') {
            // In requirements only investors have a wallet concept mentioned extensively,
            // but entrepreneurs might need it for refunds?
            // For now, let's limit to investors as per navbar logic.
            abort(403);
        }

        $user->increment('wallet', $request->amount);

        return back()->with('success', 'Funds added successfully!');
    }

    /**
     * Store a new investment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'amount' => 'required|numeric|min:1',
        ]);

        $user = Auth::user();
        $project = Project::findOrFail($request->project_id);
        $amount = $request->amount;

        // Validation 1: Role
        if ($user->role !== 'investor') {
            abort(403, 'Only investors can invest.');
        }

        // Validation 2: Project Status (Must be active)
        if ($project->status !== 'active') {
            return back()->with('error', 'This project is not active and cannot receive funds.');
        }

        // Validation 3: Balance
        if ($user->wallet < $amount) {
            return back()->with('error', 'Insufficient funds in wallet.');
        }

        // Validation 4: Max Funding (Hard Cap)
        $remaining = $project->funding_max - $project->funding_current;
        if ($amount > $remaining) {
            return back()->with('error', "You can only invest up to {$remaining}€.");
        }

        // Transaction
        DB::transaction(function () use ($user, $project, $amount) {
            // Deduct from wallet
            $user->decrement('wallet', $amount);

            // Add investment record
            Investment::create([
                'user_id' => $user->id,
                'project_id' => $project->id,
                'amount' => $amount,
            ]);

            // Increment project funding
            $project->increment('funding_current', $amount);
        });

        // Trigger check status logic (e.g. if 100% complete) - Simplified: here or in logic
        if ($project->funding_current >= $project->funding_max) {
            $project->update(['status' => 'completed']);
            // TODO: Trigger notification or money transfer (Simulated)
        }

        return redirect()->route('projects.show', $project)->with('success', 'Investment successful!');
    }

    /**
     * Withdraw an investment (Refund).
     * Rule: Only within 24 hours of making the investment.
     */
    public function destroy(Investment $investment)
    {
        if (Auth::id() !== $investment->user_id) {
            abort(403);
        }

        // Rule: 24 Hours
        if ($investment->created_at->diffInHours(now()) > 24) {
            return back()->with('error', 'Investments can only be withdrawn within 24 hours.');
        }

        // Rule: Project must not be completed/canceled? 
        // Generally if completed, money might be gone. But let's assume if it's active we can withdraw.
        if ($investment->project->status !== 'active') {
            return back()->with('error', 'Cannot withdraw from a completed or canceled project.');
        }

        DB::transaction(function () use ($investment) {
            $user = Auth::user();
            $project = $investment->project;
            $amount = $investment->amount;

            // Refund wallet
            $user->increment('wallet', $amount);

            // Decrease project funding
            $project->decrement('funding_current', $amount);

            // Delete investment record
            $investment->delete();
        });

        return back()->with('success', 'Investment withdrawn and funds refunded.');
    }
}
