<?php
// php artisan make:crud Banner
// php artisan make:crud Banner --force
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;
use Throwable;

class MakeCrudCommand extends Command
{
    protected $signature = 'make:crud {name} {--force : Overwrite existing files if they exist}';
    protected $description = 'Generate full CRUD (Model, Migration, Requests, Controller, Observer, and Routes) and run migrations automatically.';

    protected array $createdFiles = []; // to rollback on failure

    public function handle()
    {
        $rawName = $this->argument('name');

        // ❌ Reject names with spaces or underscores
        if (preg_match('/[\s_]/', $rawName)) {
            $this->error("❌ Invalid name format: '{$rawName}'.
                Use PascalCase or singular word only (e.g., Book, Product, Banner).");
            return Command::FAILURE;
        }

        // ✅ Normalize name
        $name = Str::studly($rawName);
        $plural = Str::pluralStudly($name);
        $lower = Str::snake($name);
        $pluralLower = Str::plural($lower);

        $this->info("🚀 Generating CRUD for: {$name}");

        try {
            // ✅ 1. Create Model
            $modelPath = app_path("Models/{$name}.php");
            $this->safeCreate($modelPath, $this->getModelTemplate($name, $lower), 'Model');

            // ✅ 2. Create Migration
            $migrationPattern = database_path("migrations/*create_{$lower}_table.php");
            $existing = glob($migrationPattern);
            if (!$existing || $this->option('force')) {
                $migrationName = date('Y_m_d_His') . "_create_{$lower}_table.php";
                $migrationPath = database_path("migrations/{$migrationName}");
                $this->safeCreate($migrationPath, $this->getMigrationTemplate($lower), 'Migration');
            } else {
                $this->warn("⚠️ Migration already exists — skipped.");
            }

            // ✅ 3. Requests
            $this->createRequest("Store{$name}Request", $this->getStoreRequestTemplate($name));
            $this->createRequest("Update{$name}Request", $this->getUpdateRequestTemplate($name));

            // ✅ 4. Observer
            $observerPath = app_path("Observers/{$name}Observer.php");
            $this->safeCreate($observerPath, $this->getObserverTemplate($name), 'Observer');

            // ✅ 5. Controller
            $controllerPath = app_path("Http/Controllers/Admin/{$name}Controller.php");
            $this->safeCreate($controllerPath, $this->getControllerTemplate($name, $plural, $lower, $lower), 'Controller');

            // ✅ 6. Register Observer
            $this->registerObserver($name);

            // ✅ 7. Inject Routes
            $this->injectRoutes($name, $lower, $lower);

            // ✅ 8. Run migration
            $this->info("⚙️ Running database migration...");
            Artisan::call('migrate', ['--force' => true]);
            $this->info(Artisan::output());
            $this->info("✅ CRUD for {$name} created and migrated successfully!");
        } catch (\Throwable $e) {
            $this->error("❌ Error: {$e->getMessage()}");
            $this->rollbackFiles();
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    // ---------- SAFE FILE CREATION ----------
    private function safeCreate($path, $content, $type)
    {
        if (File::exists($path) && !$this->option('force')) {
            $this->warn("⚠️ {$type} already exists: {$path} — skipped.");
            return;
        }

        if (File::exists($path) && $this->option('force')) {
            $this->warn("🔄 Overwriting {$type}: {$path}");
        }

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);
        $this->createdFiles[] = $path;
        $this->info("✅ {$type} created: {$path}");
    }

    // ---------- ROLLBACK FILES ON FAILURE ----------
    private function rollbackFiles()
    {
        $this->warn("⚠️ Rolling back created files...");
        foreach (array_reverse($this->createdFiles) as $file) {
            if (File::exists($file)) {
                File::delete($file);
                $this->warn("🗑️ Deleted: {$file}");
            }
        }
    }

    // ---------- REQUEST FILE CREATION ----------
    private function createRequest($name, $content)
    {
        $path = app_path("Http/Requests/{$name}.php");
        $this->safeCreate($path, $content, 'Request');
    }

    // ---------- OBSERVER REGISTRATION ----------
    private function registerObserver($name)
    {
        $providerPath = app_path('Providers/EventServiceProvider.php');
        $content = File::get($providerPath);

        $modelImport = "use App\\Models\\{$name};";
        $observerImport = "use App\\Observers\\{$name}Observer;";
        $observeCode = "{$name}::observe({$name}Observer::class);";

        if (!str_contains($content, $modelImport)) {
            $content = preg_replace('/namespace App\\\\Providers;/', "namespace App\\Providers;\n\n{$modelImport}\n{$observerImport}", $content);
        }

        if (!str_contains($content, $observeCode)) {
            $content = preg_replace('/public function boot\(\)\n\s*\{/', "public function boot()\n    {\n        {$observeCode}", $content);
        }

        File::put($providerPath, $content);
        $this->info("🔗 {$name}Observer registered in EventServiceProvider.");
    }

    // ---------- ROUTE INJECTION ----------
    private function injectRoutes($name, $pluralLower, $lower)
    {
        $webPath = base_path('routes/web.php');
        $content = File::get($webPath);

        // 1) ensure controller import at top
        $useStatement = "use App\\Http\\Controllers\\Admin\\{$name}Controller;";
        if (!Str::contains($content, $useStatement)) {
            // insert after the opening <?php (if exists) otherwise at top
            if (preg_match('/<\?php\s*/', $content, $m, PREG_OFFSET_CAPTURE)) {
                $pos = $m[0][1] + strlen($m[0][0]);
                $content = substr_replace($content, PHP_EOL . $useStatement . PHP_EOL, $pos, 0);
            } else {
                $content = "<?php\n\n" . $useStatement . PHP_EOL . $content;
            }
        }

        // 2) build full route block (expandable; includes trash/restore/toggle routes example)
            $routes = <<<ROUTES

            // {$name} (resource)
            Route::get('{$lower}/data', [{$name}Controller::class, 'getData'])->name('admin.{$lower}.data');
            Route::post('{$lower}/{{$lower}}/toggle-status', [{$name}Controller::class, 'toggleStatus'])->name('admin.{$lower}.toggleStatus');
            Route::get('{$lower}/trash', [{$name}Controller::class, 'trash'])->name('admin.{$lower}.trash');
            Route::get('{$lower}/trash/data', [{$name}Controller::class, 'getTrashedData'])->name('admin.{$lower}.trash.data');
            Route::post('{$lower}/{id}/restore', [{$name}Controller::class, 'restore'])->name('admin.{$lower}.restore');
            Route::delete('{$lower}/{id}/force-delete', [{$name}Controller::class, 'forceDelete'])->name('admin.{$lower}.forceDelete');
            Route::delete('{$lower}/bulk-delete', [{$name}Controller::class, 'bulkDelete'])->name('admin.{$lower}.bulkDelete');
            Route::post('{$lower}/bulk-restore', [{$name}Controller::class, 'bulkRestore'])->name('admin.{$lower}.bulkRestore');
            Route::delete('{$lower}/bulk-force-delete', [{$name}Controller::class, 'bulkForceDelete'])->name('admin.{$lower}.bulkForceDelete');
            Route::post('admin/{$lower}/sort', [{$name}Controller::class, 'sort'])->name('admin.{$lower}.sort');
            Route::resource('{$lower}', {$name}Controller::class)->names('admin.{$lower}');

        ROUTES;

        // 3) Try to insert routes before the final require __DIR__... (and specifically before the admin group's closing "});")
        $requirePos = strpos($content, "require __DIR__");
        if ($requirePos !== false) {
            // find the last "});" that appears *before* the require statement
            $beforeRequire = substr($content, 0, $requirePos);
            $lastClosePos = strrpos($beforeRequire, "});");

            if ($lastClosePos !== false) {
                // insert route block right before that "});"
                $newBefore = substr_replace($beforeRequire, $routes, $lastClosePos, 0);
                $afterRequire = substr($content, $requirePos);
                $content = $newBefore . $afterRequire;
            } else {
                // fallback: insert directly before the require line
                $content = substr_replace($content, $routes, $requirePos, 0);
            }
        } else {
            // if require not found, append routes at end (last resort)
            if (!Str::contains($content, $routes)) {
                $content .= PHP_EOL . $routes;
            }
        }

        // 4) avoid duplicate insertion: if block already exists don't write duplicate
        // (simple check based on presence of the resource route line)
        if (Str::contains(File::get($webPath), "Route::resource('{$lower}', {$name}Controller::class)")) {
            $this->info("ℹ️ Routes for {$name} already present in web.php — no changes made.");
            // still write back the use statement changes (if any) — rewrite only the top import changes:
            File::put($webPath, $content);
            return;
        }

        // write final file
        File::put($webPath, $content);
        $this->info("🛣️ Routes for {$name} added in web.php");
    }

    // ---------- FILE TEMPLATES ----------
    private function getModelTemplate($name, $table)
    {
        return <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class {$name} extends Model
{
    use SoftDeletes;

    protected \$table = '{$table}';
    protected \$primaryKey = 'id';
    protected \$fillable = ['title', 'description', 'image', 'status', 'sort_order'];
    protected \$dates = ['deleted_at'];
}
PHP;
    }

    private function getMigrationTemplate($table)
    {
        return <<<PHP
<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('{$table}', function (Blueprint \$table) {
            \$table->id();
            \$table->string('title')->nullable();
            \$table->text('description')->nullable();
            \$table->string('image')->nullable();
            \$table->boolean('status')->default(1);
            \$table->integer('sort_order')->default(0);
            \$table->timestamps();
            \$table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('{$table}');
    }
};
PHP;
    }

    private function getStoreRequestTemplate($name)
    {
        return <<<PHP
<?php

namespace App\\Http\\Requests;

use Illuminate\\Foundation\\Http\\FormRequest;

class Store{$name}Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'image' => 'nullable|mimes:jpeg,jpg,png,gif,webp|max:10000',
            'description' => 'nullable|string',
        ];
    }
}
PHP;
    }

    private function getUpdateRequestTemplate($name)
    {
        return <<<PHP
<?php

namespace App\\Http\\Requests;

use Illuminate\\Foundation\\Http\\FormRequest;

class Update{$name}Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'image' => 'nullable|mimes:jpeg,jpg,png,gif,webp|max:10000',
            'description' => 'nullable|string',
        ];
    }
}
PHP;
    }

    private function getObserverTemplate($name)
    {
        $var = Str::camel($name);

        return <<<PHP
<?php

namespace App\\Observers;

use App\\Models\\{$name};
use Illuminate\\Support\\Facades\\File;

class {$name}Observer
{
    public function created({$name} \${$var}): void
    {
        //
    }

    public function updated({$name} \${$var}): void
    {
        if (\${$var}->isDirty('image')) {
            \$oldImage = \${$var}->getOriginal('image');
            if (\$oldImage && File::exists(public_path(\$oldImage))) {
                File::delete(public_path(\$oldImage));
            }
        }
    }

    public function deleted({$name} \${$var}): void
    {
        if (\${$var}->isForceDeleting()) {
            if (\${$var}->image && File::exists(public_path(\${$var}->image))) {
                File::delete(public_path(\${$var}->image));
            }
        }
    }

    public function restored({$name} \${$var}): void
    {
        //
    }

    public function forceDeleted({$name} \${$var}): void
    {
        //
    }
}
PHP;
    }

private function getControllerTemplate($name, $plural, $lower, $pluralLower)
{
    return <<<PHP
        <?php

        namespace App\\Http\\Controllers\\Admin;

        use App\\Http\\Controllers\\Controller;
        use App\\Models\\{$name};
        use Illuminate\\Http\\Request;
        use Yajra\\DataTables\\Facades\\DataTables;
        use Illuminate\\Support\\Facades\\File;
        use App\\Http\\Requests\\Store{$name}Request;
        use App\\Http\\Requests\\Update{$name}Request;
        use App\\Traits\\FileUploadTrait;

        class {$name}Controller extends Controller
        {
            use FileUploadTrait;

            public function index()
            {
                return view('admin.{$pluralLower}.index');
            }

            public function getData(Request \$request)
            {
                \$query = {$name}::orderBy('sort_order', 'asc');

                // Optional filters
                if (\$request->filled('status')) {
                    \$query->where('status', \$request->status);
                }

                if (\$request->filled('from_date') && \$request->filled('to_date')) {
                    \$query->whereBetween('created_at', [
                        \$request->from_date . ' 00:00:00',
                        \$request->to_date . ' 23:59:59'
                    ]);
                }

                return DataTables::of(\$query)
                    ->addColumn('image', function (\$row) {
                        if (!\$row->image) {
                            return '<span class="text-muted">No Image</span>';
                        }
                        // Lazy loading image
                        return '<img data-src="'.asset(\$row->image).'" class="lazy-load" width="120" />';
                    })
                    ->addColumn('status', function (\$row) {
                        \$checked = \$row->status ? 'checked' : '';
                        return '
                            <label class="switch">
                                <input type="checkbox" class="toggle{$name}Status" data-id="' . \$row->id . '" ' . \$checked . '>
                                <span class="slider round" title="Click to toggle status"></span>
                            </label>
                        ';
                    })
                    ->addColumn('created_at', function (\$row) {
                        return \$row->created_at ? \$row->created_at->format('d M, Y h:i A') : '-';
                    })
                    ->addColumn('action', function (\$row) {
                        \$edit = '<a href="'.url('admin/{$lower}/'.\$row->id.'/edit').'" class="btn btn-sm btn-info"><i class="la la-pencil"></i></a>';
                        \$delete = '<button class="btn btn-sm btn-danger delete{$name}" data-id="'.\$row->id.'"><i class="la la-trash"></i></button>';
                        return \$edit . ' ' . \$delete;
                    })
                    ->rawColumns(['image', 'status', 'action'])
                    ->make(true);
            }

            public function create()
            {
                return view('admin.{$pluralLower}.create');
            }

            public function store(Store{$name}Request \$request)
            {
                \$data = \$request->validated();

                if (\$request->hasFile('image')) {
                    \$data['image'] = \$this->uploadFile(\$request->file('image'), 'uploads/{$pluralLower}/', '{$lower}');
                }

                \$item = {$name}::create(\$data);

                log_activity('create', {$name}::class, \$item->id, 'Created new {$lower}: ' . (\$item->title ?? 'N/A'));

                return redirect('admin/{$lower}')
                    ->with('message', '{$name} added successfully!');
            }

            public function show({$name} \${$lower})
            {
                return view('admin.{$pluralLower}.show', compact('{$lower}'));
            }

            public function edit({$name} \${$lower})
            {
                return view('admin.{$pluralLower}.edit', compact('{$lower}'));
            }

            public function update(Update{$name}Request \$request, {$name} \${$lower})
            {
                \$data = \$request->validated();

                if (\$request->hasFile('image')) {
                    \$this->deleteFile(\${$lower}->image);
                    \$data['image'] = \$this->uploadFile(\$request->file('image'), 'uploads/{$pluralLower}/', '{$lower}');
                }

                \$oldData = \${$lower}->toArray();
                \${$lower}->update(\$data);

                log_activity('update', {$name}::class, \${$lower}->id, 'Updated {$lower}', [
                    'before' => \$oldData,
                    'after' => \${$lower}->toArray()
                ]);

                return redirect()->route('admin.{$lower}.index')->with('message', '{$name} updated successfully!');
            }

            public function destroy({$name} \${$lower})
            {
                \${$lower}->delete();
                log_activity('delete', {$name}::class, \${$lower}->id, "Deleted {$lower}");
                return response()->json(['success' => '{$name} deleted successfully.']);
            }

            public function toggleStatus({$name} \${$lower})
            {
                \$oldStatus = \${$lower}->status;
                \${$lower}->status = !\$oldStatus;
                \${$lower}->save();

                log_activity('status_toggle', {$name}::class, \${$lower}->id, 'Toggled status', [
                    'old_status' => \$oldStatus,
                    'new_status' => \${$lower}->status
                ]);

                return response()->json([
                    'success' => true,
                    'status' => \${$lower}->status ? 'Active' : 'Inactive',
                ]);
            }

            public function trash()
            {
                return view('admin.{$pluralLower}.trash');
            }

            public function getTrashedData(Request \$request)
            {
                \$items = {$name}::onlyTrashed()->orderByDesc('id')->get();

                return DataTables::of(\$items)
                    ->addColumn('checkbox', fn(\$row) =>
                        '<input type="checkbox" class="rowCheckbox" value="'.\$row->id.'">'
                    )
                    ->addColumn('image', function(\$row) {
                        return \$row->image
                            ? '<img data-src="'.asset(\$row->image).'" class="lazy-load" width="120" />'
                            : '<span class="text-muted">No Image</span>';
                    })
                    ->addColumn('action', function(\$row) {
                        \$restore = '<button class="btn btn-sm btn-success restore{$name}" data-id="'.\$row->id.'"><i class="la la-refresh"></i></button>';
                        \$delete = '<button class="btn btn-sm btn-danger forceDelete{$name}" data-id="'.\$row->id.'"><i class="la la-trash"></i></button>';
                        return \$restore . ' ' . \$delete;
                    })
                    ->rawColumns(['checkbox', 'image', 'action'])
                    ->make(true);
            }

            public function restore(\$id)
            {
                \$item = {$name}::withTrashed()->findOrFail(\$id);
                \$item->restore();
                log_activity('restore', {$name}::class, \$id, "Restored {$lower}");
                return response()->json(['success' => '{$name} restored successfully!']);
            }

            public function forceDelete(\$id)
            {
                \$item = {$name}::withTrashed()->findOrFail(\$id);
                \$this->deleteFile(\$item->image);
                \$item->forceDelete();

                log_activity('force_delete', {$name}::class, \$id, "Permanently deleted {$lower}");
                return response()->json(['success' => '{$name} permanently deleted.']);
            }

            public function bulkDelete(Request \$request)
            {
                \$ids = \$request->ids ?? [];
                if (empty(\$ids)) return response()->json(['error' => 'No items selected.'], 400);

                {$name}::whereIn('id', \$ids)->delete();
                log_activity('bulk_delete', {$name}::class, null, 'Bulk delete', ['ids' => \$ids]);
                return response()->json(['success' => 'Selected {$pluralLower} deleted successfully.']);
            }

            public function bulkRestore(Request \$request)
            {
                \$ids = \$request->ids ?? [];
                if (empty(\$ids)) return response()->json(['error' => 'No items selected.'], 400);

                {$name}::withTrashed()->whereIn('id', \$ids)->restore();
                log_activity('bulk_restore', {$name}::class, null, 'Bulk restore', ['ids' => \$ids]);
                return response()->json(['success' => 'Selected {$pluralLower} restored successfully.']);
            }

            public function bulkForceDelete(Request \$request)
            {
                \$ids = \$request->ids ?? [];
                if (empty(\$ids)) return response()->json(['error' => 'No items selected.'], 400);

                \$items = {$name}::withTrashed()->whereIn('id', \$ids)->get();
                foreach (\$items as \$item) {
                    \$this->deleteFile(\$item->image);
                    \$item->forceDelete();
                }

                log_activity('bulk_force_delete', {$name}::class, null, 'Bulk permanently delete', ['ids' => \$ids]);
                return response()->json(['success' => 'Selected {$pluralLower} permanently deleted.']);
            }

            public function sort(Request \$request)
            {
                \$order = \$request->input('order', []);
                if (!is_array(\$order) || empty(\$order)) {
                    return response()->json(['success' => false, 'message' => 'No order data received'], 400);
                }

                foreach (\$order as \$item) {
                    \$pos = \$item['position'] ?? \$item['newPosition'] ?? null;
                    \$id  = \$item['id'] ?? null;
                    if (\$id && \$pos !== null) {
                        {$name}::where('id', \$id)->update(['sort_order' => (int)\$pos]);
                    }
                }

                return response()->json(['success' => true, 'message' => 'Order updated successfully']);
            }
        }
    PHP;
}

}
