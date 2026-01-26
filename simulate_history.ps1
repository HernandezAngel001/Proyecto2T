# simulate_history.ps1

$projectRoot = "c:\xampp\htdocs\copiaseg\DWES\pts\Proyecto2T"
$backupDir = "c:\xampp\htdocs\copiaseg\DWES\pts\Proyecto2T_Source_Backup"
$gitDir = "$projectRoot\.git"

# 1. Backup existing files to use as source
# If backup exists from previous run, we can use it. If not, create it.
if (-not (Test-Path $backupDir)) {
    Write-Host "Backing up files to $backupDir..."
    New-Item -ItemType Directory -Path $backupDir | Out-Null
    Copy-Item "$projectRoot\*" $backupDir -Recurse -Force -Exclude ".git"
} else {
    Write-Host "Using existing backup at $backupDir..."
}

# 2. Reset Git Repository
Write-Host "Resetting Git Repository..."
if (Test-Path $gitDir) { 
    Rename-Item $gitDir "$projectRoot\.git_old_$(Get-Date -Format 'yyyyMMddHHmmss')"
}

# Clear working directory (except .git_old backups and script)
Get-ChildItem $projectRoot -Exclude ".git_old*", "simulate_history.ps1" | Remove-Item -Recurse -Force

Set-Location $projectRoot
git init
git checkout -b main

# Helper function to copy and commit
function Commit-Step {
    param (
        [string]$Message,
        [string]$Date,
        [string[]]$Files,
        [string]$Branch = "main"
    )
    
    # Ensure branch exists and checkout
    $currentBranch = (git branch --show-current).Trim()
    if ($currentBranch -ne $Branch) {
        $branchExists = git branch --list $Branch
        if ($branchExists) {
            git checkout $Branch
        } else {
            git checkout -b $Branch
        }
    }

    # Copy files from backup
    foreach ($file in $Files) {
        $sourcePath = "$backupDir\$file"
        $destPath = "$projectRoot\$file"
        
        if (Test-Path $sourcePath) {
            $parent = Split-Path $destPath
            if ($parent -and !(Test-Path $parent)) { New-Item -ItemType Directory -Path $parent -Force | Out-Null }
            Copy-Item $sourcePath $destPath -Force
        } elseif (Test-Path "$backupDir\$file") {
             Copy-Item "$backupDir\$file" "$projectRoot" -Recurse -Force
        }
    }

    git add .
    $env:GIT_AUTHOR_DATE = "$Date 12:00:00"
    $env:GIT_COMMITTER_DATE = "$Date 12:00:00"
    git commit -m "$Message" --allow-empty
}

function Merge-Step {
    param (
        [string]$From,
        [string]$To,
        [string]$Date
    )
    git checkout $To
    $env:GIT_AUTHOR_DATE = "$Date 18:00:00"
    $env:GIT_COMMITTER_DATE = "$Date 18:00:00"
    git merge $From --no-ff -m "Merge branch '$From' into $To"
}

# --- Day 1: Jan 26 ---
# Main has ONLY initial commit
Commit-Step -Branch "main" -Date "2026-01-26" -Message "Initial commit" -Files @(".gitignore")

# Branch Development IMMEDIATELY
git checkout -b development

# Feature Setup
Commit-Step -Branch "feature/setup" -Date "2026-01-26" -Message "Docs: Add README" -Files @("README.md")
Commit-Step -Branch "feature/setup" -Date "2026-01-26" -Message "Docs: Add CHANGELOG" -Files @("CHANGELOG.md")
Commit-Step -Branch "feature/setup" -Date "2026-01-26" -Message "Setup: Add composer.json" -Files @("composer.json")
Commit-Step -Branch "feature/setup" -Date "2026-01-26" -Message "Setup: Add package.json" -Files @("package.json")
Commit-Step -Branch "feature/setup" -Date "2026-01-26" -Message "Setup: Add artisan" -Files @("artisan")
Commit-Step -Branch "feature/setup" -Date "2026-01-26" -Message "Setup: Add config files" -Files @("config")
Commit-Step -Branch "feature/setup" -Date "2026-01-26" -Message "Setup: Add bootstrap" -Files @("bootstrap")
Commit-Step -Branch "feature/setup" -Date "2026-01-26" -Message "Setup: Add public assets" -Files @("public")
Commit-Step -Branch "feature/setup" -Date "2026-01-26" -Message "Setup: Add environment example" -Files @(".env.example")
Merge-Step -From "feature/setup" -To "development" -Date "2026-01-26"

# --- Day 2: Jan 27 ---
# Feature Auth
Commit-Step -Branch "feature/auth" -Date "2026-01-27" -Message "Database: Add users migration" -Files @("database/migrations/0001_01_01_000000_create_users_table.php")
Commit-Step -Branch "feature/auth" -Date "2026-01-27" -Message "Database: Add cache migration" -Files @("database/migrations/0001_01_01_000001_create_cache_table.php")
Commit-Step -Branch "feature/auth" -Date "2026-01-27" -Message "Models: Add User model" -Files @("app/Models/User.php")
Commit-Step -Branch "feature/auth" -Date "2026-01-27" -Message "Controllers: Add Base Controller" -Files @("app/Http/Controllers/Controller.php")
Commit-Step -Branch "feature/auth" -Date "2026-01-27" -Message "Controllers: Add Auth controllers" -Files @("app/Http/Controllers/Auth")
Commit-Step -Branch "feature/auth" -Date "2026-01-27" -Message "Views: Add Auth directory" -Files @("resources/views/auth")
Commit-Step -Branch "feature/auth" -Date "2026-01-27" -Message "Views: Add Login view" -Files @("resources/views/auth/login.blade.php")
Commit-Step -Branch "feature/auth" -Date "2026-01-27" -Message "Views: Add Register view" -Files @("resources/views/auth/register.blade.php")
Merge-Step -From "feature/auth" -To "development" -Date "2026-01-27"

# --- Day 3: Jan 28 ---
# Feature UI
Commit-Step -Branch "feature/ui" -Date "2026-01-28" -Message "Views: Create App Layout" -Files @("resources/views/layouts/app.blade.php")
Commit-Step -Branch "feature/ui" -Date "2026-01-28" -Message "Views: Create Guest Layout" -Files @("resources/views/layouts/guest.blade.php")
Commit-Step -Branch "feature/ui" -Date "2026-01-28" -Message "Views: Add Landing Page" -Files @("resources/views/landing.blade.php")
Commit-Step -Branch "feature/ui" -Date "2026-01-28" -Message "Views: Add Dashboard" -Files @("resources/views/dashboard.blade.php")
Commit-Step -Branch "feature/ui" -Date "2026-01-28" -Message "Assets: Add CSS" -Files @("resources/css/app.css")
Commit-Step -Branch "feature/ui" -Date "2026-01-28" -Message "Assets: Add JS" -Files @("resources/js/app.js")
Commit-Step -Branch "feature/ui" -Date "2026-01-28" -Message "Assets: Add Bootstrap JS" -Files @("resources/js/bootstrap.js")
Commit-Step -Branch "feature/ui" -Date "2026-01-28" -Message "Config: Add Vite config" -Files @("vite.config.js")
Merge-Step -From "feature/ui" -To "development" -Date "2026-01-28"

# --- Day 4: Jan 29 ---
# Feature Projects
Commit-Step -Branch "feature/projects" -Date "2026-01-29" -Message "Database: Add projects table migration" -Files @("database/migrations/*create_projects_table.php")
Commit-Step -Branch "feature/projects" -Date "2026-01-29" -Message "Models: Add Project model" -Files @("app/Models/Project.php")
Commit-Step -Branch "feature/projects" -Date "2026-01-29" -Message "Controllers: Add ProjectController" -Files @("app/Http/Controllers/ProjectController.php")
Commit-Step -Branch "feature/projects" -Date "2026-01-29" -Message "Requests: Add ProjectRequest" -Files @("app/Http/Requests/ProjectRequest.php")
Commit-Step -Branch "feature/projects" -Date "2026-01-29" -Message "Views: Add Projects Index" -Files @("resources/views/projects/index.blade.php")
Commit-Step -Branch "feature/projects" -Date "2026-01-29" -Message "Views: Add Projects Create" -Files @("resources/views/projects/create.blade.php")
Commit-Step -Branch "feature/projects" -Date "2026-01-29" -Message "Views: Add Projects Show" -Files @("resources/views/projects/show.blade.php")
Commit-Step -Branch "feature/projects" -Date "2026-01-29" -Message "Logic: Implement project logic" -Files @("app/Http/Controllers/UpdateController.php")
Merge-Step -From "feature/projects" -To "development" -Date "2026-01-29"

# --- Day 5: Jan 30 ---
# Feature Investments
Commit-Step -Branch "feature/investments" -Date "2026-01-30" -Message "Database: Add investments table migration" -Files @("database/migrations/*create_investments_table.php")
Commit-Step -Branch "feature/investments" -Date "2026-01-30" -Message "Models: Add Investment model" -Files @("app/Models/Investment.php")
Commit-Step -Branch "feature/investments" -Date "2026-01-30" -Message "Controllers: Add InvestmentController" -Files @("app/Http/Controllers/InvestmentController.php")
Commit-Step -Branch "feature/investments" -Date "2026-01-30" -Message "Views: Add Investment views" -Files @("resources/views/investments")
Merge-Step -From "feature/investments" -To "development" -Date "2026-01-30"

# --- Day 6: Jan 31 ---
# Feature Admin
Commit-Step -Branch "feature/admin" -Date "2026-01-31" -Message "Middleware: Add Admin middleware" -Files @("app/Http/Middleware")
Commit-Step -Branch "feature/admin" -Date "2026-01-31" -Message "Views: Add Admin Dashboard" -Files @("resources/views/admin/dashboard.blade.php")
Merge-Step -From "feature/admin" -To "development" -Date "2026-01-31"

# Feature API
Commit-Step -Branch "feature/api" -Date "2026-01-31" -Message "Routes: Add API routes" -Files @("routes/api.php")
Commit-Step -Branch "feature/api" -Date "2026-01-31" -Message "Config: Configure Sanctum" -Files @("config/sanctum.php")
Merge-Step -From "feature/api" -To "development" -Date "2026-01-31"

# --- Day 7: Feb 01 ---
# Final Polish - NO MERGE TO DEVELOPMENT
Commit-Step -Branch "feature/cleanup" -Date "2026-02-01" -Message "Routes: Finalize web routes" -Files @("routes/web.php")
Commit-Step -Branch "feature/cleanup" -Date "2026-02-01" -Message "Routes: Add console routes" -Files @("routes/console.php")
Commit-Step -Branch "feature/cleanup" -Date "2026-02-01" -Message "Database: Add DatabaseSeeder" -Files @("database/seeders/DatabaseSeeder.php")
Commit-Step -Branch "feature/cleanup" -Date "2026-02-01" -Message "Database: Add other seeders" -Files @("database/seeders")
Commit-Step -Branch "feature/cleanup" -Date "2026-02-01" -Message "Tests: Add initial tests" -Files @("tests")
Commit-Step -Branch "feature/cleanup" -Date "2026-02-01" -Message "Lang: Add language files" -Files @("resources/lang")
Commit-Step -Branch "feature/cleanup" -Date "2026-02-01" -Message "Storage: Configure storage" -Files @("storage")
Commit-Step -Branch "feature/cleanup" -Date "2026-02-01" -Message "Docs: Updating secret documentation" -Files @("secret.md")
# Intentionally NOT merging feature/cleanup to development
# Intentionally NOT merging development to main

Write-Host "History simulation complete! Current branch is feature/cleanup"
