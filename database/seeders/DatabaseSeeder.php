<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use App\Models\Investment;
use App\Models\Update;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Core Users
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@stemfounding.com',
            'password' => Hash::make('1234'),
            'role' => 'admin',
            'wallet' => 0.00,
            'is_banned' => false,
        ]);

        $entrepreneurAlpha = User::create([
            'name' => 'Elon Musk',
            'email' => 'elon@spacex.com',
            'password' => Hash::make('1234'),
            'role' => 'entrepreneur',
            'wallet' => 1000.00,
            'photo_url' => 'https://ui-avatars.com/api/?name=Elon+Musk&background=0D8ABC&color=fff',
        ]);

        $entrepreneurBeta = User::create([
            'name' => 'Marie Curie',
            'email' => 'marie@science.net',
            'password' => Hash::make('1234'),
            'role' => 'entrepreneur',
            'wallet' => 500.00,
            'photo_url' => 'https://ui-avatars.com/api/?name=Marie+Curie&background=random',
        ]);

        $investorWhale = User::create([
            'name' => 'Warren Buffett',
            'email' => 'warren@berkshire.com',
            'password' => Hash::make('1234'),
            'role' => 'investor',
            'wallet' => 1000000.00,
            'photo_url' => 'https://ui-avatars.com/api/?name=Warren+Buffett&background=green&color=fff',
        ]);

        $investorShark = User::create([
            'name' => 'Kevin O\'Leary',
            'email' => 'kevin@sharktank.com',
            'password' => Hash::make('1234'),
            'role' => 'investor',
            'wallet' => 500000.00,
            'photo_url' => 'https://ui-avatars.com/api/?name=Kevin+OLeary',
        ]);

        $bannedUser = User::create([
            'name' => 'Banned User',
            'email' => 'troll@internet.com',
            'password' => Hash::make('1234'),
            'role' => 'investor',
            'wallet' => 0.00,
            'is_banned' => true,
        ]);

        // 2. Create Projects (Simulating various states)

        // Case A: Active, Popular, Approaching Deadline
        $projectMars = Project::create([
            'user_id' => $entrepreneurAlpha->id,
            'title' => 'Mars Colony Habitat',
            'description' => 'A sustainable habitat for the first humans on Mars. Includes oxygen recycling and hydroponic gardens. We are close to the goal!',
            'funding_min' => 50000.00,
            'funding_max' => 500000.00,
            'funding_current' => 450000.00, // 90% Funded
            'status' => 'active',
            'deadline' => now()->addDays(5),
            'image_url' => 'https://images.unsplash.com/photo-1541873676-a18131494184?auto=format&fit=crop&q=80&w=1000',
        ]);

        // Case B: Active, Just Started, Low Funding
        $projectVtol = Project::create([
            'user_id' => $entrepreneurAlpha->id,
            'title' => 'Electric VTOL Jet',
            'description' => 'Vertical Take-Off and Landing electric jet for urban commuting. The future of taxi service.',
            'funding_min' => 100000.00,
            'funding_max' => 1000000.00,
            'funding_current' => 5000.00, // Just started
            'status' => 'active',
            'deadline' => now()->addDays(60),
            'image_url' => 'https://images.unsplash.com/photo-1559067515-bf7d799b6d42?auto=format&fit=crop&q=80&w=1000',
        ]);

        // Case C: Pending Admin Approval
        $projectFusion = Project::create([
            'user_id' => $entrepreneurBeta->id,
            'title' => 'Cold Fusion Reactor',
            'description' => 'Prototype for infinite clean energy. Needs peer review.',
            'funding_min' => 200000.00,
            'funding_max' => 2000000.00,
            'funding_current' => 0.00,
            'status' => 'pending',
            'deadline' => now()->addDays(90),
            'image_url' => 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?auto=format&fit=crop&q=80&w=1000',
        ]);

        // Case D: Rejected by Admin
        $projectScam = Project::create([
            'user_id' => $entrepreneurAlpha->id,
            'title' => 'Perpetual Motion Machine',
            'description' => 'Totally real physics-breaking machine.',
            'funding_min' => 10000.00,
            'funding_max' => 50000.00,
            'funding_current' => 0.00,
            'status' => 'rejected',
            'deadline' => now()->addDays(10),
            'image_url' => 'https://images.unsplash.com/photo-1518331840030-a33e36d4984f?auto=format&fit=crop&q=80&w=1000',
        ]);

        // Case E: Completed (Successful Funding)
        $projectVaccine = Project::create([
            'user_id' => $entrepreneurBeta->id,
            'title' => 'Universal Flu Vaccine',
            'description' => 'A vaccine that works for all strains of influenza.',
            'funding_min' => 30000.00,
            'funding_max' => 100000.00,
            'funding_current' => 100000.00, // Fully funded
            'status' => 'completed',
            'deadline' => now()->subDays(10), // Expired
            'image_url' => 'https://images.unsplash.com/photo-1584036561566-b93a945b5056?auto=format&fit=crop&q=80&w=1000',
        ]);

        // Case F: Canceled (Expired without reaching min goal)
        $projectFailed = Project::create([
            'user_id' => $entrepreneurBeta->id,
            'title' => 'Underwater City',
            'description' => 'Bioshock style city. Too ambitious.',
            'funding_min' => 5000000.00,
            'funding_max' => 10000000.00,
            'funding_current' => 150000.00, // Didn't reach min
            'status' => 'canceled',
            'deadline' => now()->subDays(5),
            'image_url' => 'https://images.unsplash.com/photo-1518182170546-0766be6f5a56?auto=format&fit=crop&q=80&w=1000',
        ]);

        // Case G: Active, Mid-Range
        $projectRobotics = Project::create([
            'user_id' => $entrepreneurAlpha->id,
            'title' => 'Home Assistant Robot',
            'description' => 'A robot that does your laundry and dishes.',
            'funding_min' => 20000.00,
            'funding_max' => 100000.00,
            'funding_current' => 45000.00,
            'status' => 'active',
            'deadline' => now()->addDays(20),
            'image_url' => 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?auto=format&fit=crop&q=80&w=1000',
        ]);

        // Case H: Fully Funded but still Active (Rare case if soft-cap logic existed, forcing Completed here to test logic)
        $projectSolar = Project::create([
            'user_id' => $entrepreneurBeta->id,
            'title' => 'Transparent Solar Panels',
            'description' => 'Windows that generate electricity.',
            'funding_min' => 60000.00,
            'funding_max' => 60000.00,
            'funding_current' => 60000.00,
            'status' => 'completed',
            'deadline' => now()->addDays(15),
            'image_url' => 'https://images.unsplash.com/photo-1508514177221-188b1cf16e9d?auto=format&fit=crop&q=80&w=1000',
        ]);

        // 3. Investments (Mixing recent and old)

        // Whale invests big in Mars
        Investment::create(['user_id' => $investorWhale->id, 'project_id' => $projectMars->id, 'amount' => 400000.00, 'created_at' => now()->subDays(10)]);

        // Shark invests in Mars too (recent)
        Investment::create(['user_id' => $investorShark->id, 'project_id' => $projectMars->id, 'amount' => 50000.00, 'created_at' => now()->subHours(5)]);

        // Shark invests in Robot
        Investment::create(['user_id' => $investorShark->id, 'project_id' => $projectRobotics->id, 'amount' => 45000.00, 'created_at' => now()->subDays(2)]);

        // Whale invested in Completed Vaccine project
        Investment::create(['user_id' => $investorWhale->id, 'project_id' => $projectVaccine->id, 'amount' => 100000.00, 'created_at' => now()->subDays(20)]);

        // 4. Updates
        Update::create([
            'project_id' => $projectMars->id,
            'title' => 'Structural Integrity Test Passed',
            'description' => 'The dome withstood the pressure test successfully.',
            'photo_url' => 'https://images.unsplash.com/photo-1541873676-a18131494184?auto=format&fit=crop&q=80&w=1000',
            'created_at' => now()->subDays(5),
        ]);

        Update::create([
            'project_id' => $projectMars->id,
            'title' => 'Oxygen Systems Online',
            'description' => 'We are breathing easy now.',
            'created_at' => now()->subDays(1),
        ]);

        Update::create([
            'project_id' => $projectVaccine->id,
            'title' => 'FDA Approval',
            'description' => 'We are distributing the vaccine worldwide!',
            'created_at' => now()->subDays(2),
        ]);
    }
}
