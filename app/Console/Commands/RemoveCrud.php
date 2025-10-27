<?php
// php artisan make:remove Banner
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class RemoveCrud extends Command
{
    protected $signature = 'remove:crud {name}';
    protected $description = 'Completely remove CRUD files, migrations, routes, requests, observer, controller, model, and provider code for a given model';

    public function handle()
    {
        $rawName = trim($this->argument('name'));

        // ❌ Validation: disallow spaces or underscores
        if (preg_match('/[\s_]/', $rawName)) {
            $this->error("❌ Invalid name format: '{$rawName}'.
                Use a single word or PascalCase name only (e.g., Book, Product, Banner).");
            return Command::FAILURE;
        }

        $name = strtolower($rawName);        // e.g. book
        $studly = Str::studly($rawName);     // e.g. Book
        $plural = Str::plural($name);        // e.g. books

        $this->info("🔄 Reverting CRUD setup for: {$studly}");

        /* ---------------------------------------------------
         | 1️⃣ Rollback Migration Before Deletion
         --------------------------------------------------- */
        $this->info("⏪ Rolling back migrations before deletion...");
        try {
            Artisan::call('migrate:rollback', ['--step' => 1]);
            $this->info(Artisan::output());
        } catch (\Exception $e) {
            $this->warn("⚠️ Migration rollback failed or nothing to rollback.");
        }

        /* ---------------------------------------------------
         | 2️⃣ Delete Migration Files
         --------------------------------------------------- */
        $migrationFiles = File::glob(database_path("migrations/*create_{$name}_table.php"));
        foreach ($migrationFiles as $file) {
            if ($this->confirm("🗑 Delete migration file: " . basename($file) . "?")) {
                File::delete($file);
                $this->info("✅ Deleted migration: " . basename($file));
            }
        }

        /* ---------------------------------------------------
         | 3️⃣ Delete Blade Views
         --------------------------------------------------- */
        $viewsPath = resource_path("views/admin/{$name}");
        if (File::exists($viewsPath)) {
            if ($this->confirm("🗑 Delete all Blade files in {$viewsPath}?")) {
                File::deleteDirectory($viewsPath);
                $this->info("✅ Deleted: {$viewsPath}");
            }
        } else {
            $this->warn("⚠️ No views found for {$studly}.");
        }

        /* ---------------------------------------------------
         | 4️⃣ Delete Request Files
         --------------------------------------------------- */
        $requestPath = app_path("Http/Requests/");
        $storeRequest = $requestPath . "Store{$studly}Request.php";
        $updateRequest = $requestPath . "Update{$studly}Request.php";

        foreach ([$storeRequest, $updateRequest] as $file) {
            if (File::exists($file)) {
                if ($this->confirm("🗑 Delete file: " . basename($file) . "?")) {
                    File::delete($file);
                    $this->info("✅ Deleted: " . basename($file));
                }
            }
        }

        /* ---------------------------------------------------
         | 5️⃣ Delete Observer File
         --------------------------------------------------- */
        $observerFile = app_path("Observers/{$studly}Observer.php");
        if (File::exists($observerFile)) {
            if ($this->confirm("🗑 Delete observer file: {$studly}Observer.php?")) {
                File::delete($observerFile);
                $this->info("✅ Deleted observer: {$studly}Observer.php");
            }
        } else {
            $this->warn("⚠️ No observer found for {$studly}.");
        }

        /* ---------------------------------------------------
         | 6️⃣ Delete Controller File
         --------------------------------------------------- */
        $controllerFile = app_path("Http/Controllers/Admin/{$studly}Controller.php");
        if (File::exists($controllerFile)) {
            if ($this->confirm("🗑 Delete controller file: {$studly}Controller.php?")) {
                File::delete($controllerFile);
                $this->info("✅ Deleted controller: {$studly}Controller.php");
            }
        } else {
            $this->warn("⚠️ No controller found for {$studly}.");
        }

        /* ---------------------------------------------------
         | 7️⃣ Delete Model File
         --------------------------------------------------- */
        $modelFile = app_path("Models/{$studly}.php");
        if (File::exists($modelFile)) {
            if ($this->confirm("🗑 Delete model file: {$studly}.php?")) {
                File::delete($modelFile);
                $this->info("✅ Deleted model: {$studly}.php");
            }
        } else {
            $this->warn("⚠️ No model found for {$studly}.");
        }

        /* ---------------------------------------------------
         | 8️⃣ Remove Routes
         --------------------------------------------------- */
        $routeFile = base_path('routes/web.php');
        if (File::exists($routeFile)) {
            $content = File::get($routeFile);
            if (Str::contains($content, $name)) {
                if ($this->confirm("🗑 Remove routes related to '{$name}' from web.php?")) {
                    $pattern = "/Route::.*{$name}.*;\n?/i";
                    $updated = preg_replace($pattern, '', $content);
                    File::put($routeFile, $updated);
                    $this->info("✅ Cleaned up routes for {$name}");
                }
            } else {
                $this->warn("⚠️ No routes found for {$name}.");
            }
        }

        /* ---------------------------------------------------
         | 9️⃣ Remove EventServiceProvider entries
         --------------------------------------------------- */
        $providerFile = app_path('Providers/EventServiceProvider.php');
        if (File::exists($providerFile)) {
            $content = File::get($providerFile);
            if (Str::contains($content, $studly)) {
                if ($this->confirm("🗑 Remove EventServiceProvider entries related to {$studly}?")) {
                    $updated = preg_replace("/.*{$studly}.*\n?/", '', $content);
                    File::put($providerFile, $updated);
                    $this->info("✅ Cleaned EventServiceProvider for {$studly}");
                }
            } else {
                $this->warn("⚠️ No EventServiceProvider code found for {$studly}.");
            }
        }

        $this->info("🎉 CRUD removal completed successfully for {$studly}!");
        return 0;
    }
}
