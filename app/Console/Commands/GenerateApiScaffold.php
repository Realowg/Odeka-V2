<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateApiScaffold extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:scaffold {name} {--endpoints=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate API controller, routes, resources, and requests scaffolding';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $endpoints = $this->option('endpoints') ? explode(',', $this->option('endpoints')) : [];

        $this->info("Generating API scaffold for: {$name}");

        // Generate Controller
        $this->generateController($name, $endpoints);

        // Generate Routes
        $this->generateRoutes($name, $endpoints);

        // Generate Resource
        $this->generateResource($name);

        // Generate Request
        $this->generateRequest($name);

        $this->info("✅ API scaffold generated successfully!");
        $this->newLine();
        $this->info("Generated files:");
        $this->line("- app/Http/Controllers/Api/V1/{$name}Controller.php");
        $this->line("- routes/api/v1/" . strtolower($name) . ".php");
        $this->line("- app/Http/Resources/{$name}Resource.php");
        $this->line("- app/Http/Requests/Api/{$name}Request.php");
    }

    protected function generateController($name, $endpoints)
    {
        $controller = <<<EOT
<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\\{$name};
use Illuminate\Http\Request;
use App\Http\Requests\Api\\{$name}Request;
use App\Http\Resources\\{$name}Resource;

class {$name}Controller extends BaseController
{
EOT;

        // Add index method
        $controller .= <<<EOT

    /**
     * Get all {$name}s
     * 
     * @param Request \$request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request \$request)
    {
        \$items = {$name}::latest()->paginate(20);
        
        return \$this->paginatedResponse(\$items);
    }
EOT;

        // Add show method
        $controller .= <<<EOT

    
    /**
     * Get single {$name}
     * 
     * @param int \$id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(\$id)
    {
        \$item = {$name}::find(\$id);
        
        if (!\$item) {
            return \$this->notFoundResponse('{$name} not found');
        }
        
        return \$this->successResponse(
            new {$name}Resource(\$item)
        );
    }
EOT;

        // Add store method
        $controller .= <<<EOT

    
    /**
     * Create new {$name}
     * 
     * @param {$name}Request \$request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store({$name}Request \$request)
    {
        \$item = {$name}::create(\$request->validated());
        
        return \$this->successResponse(
            new {$name}Resource(\$item),
            '{$name} created successfully',
            201
        );
    }
EOT;

        // Add update method
        $controller .= <<<EOT

    
    /**
     * Update {$name}
     * 
     * @param {$name}Request \$request
     * @param int \$id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update({$name}Request \$request, \$id)
    {
        \$item = {$name}::find(\$id);
        
        if (!\$item) {
            return \$this->notFoundResponse('{$name} not found');
        }
        
        \$item->update(\$request->validated());
        
        return \$this->successResponse(
            new {$name}Resource(\$item),
            '{$name} updated successfully'
        );
    }
EOT;

        // Add destroy method
        $controller .= <<<EOT

    
    /**
     * Delete {$name}
     * 
     * @param int \$id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(\$id)
    {
        \$item = {$name}::find(\$id);
        
        if (!\$item) {
            return \$this->notFoundResponse('{$name} not found');
        }
        
        \$item->delete();
        
        return \$this->successResponse(null, '{$name} deleted successfully');
    }

EOT;

        // Add custom endpoints
        foreach ($endpoints as $endpoint) {
            $methodName = Str::camel($endpoint);
            $controller .= <<<EOT

    /**
     * {$endpoint} endpoint
     * 
     * @param Request \$request
     * @return \Illuminate\Http\JsonResponse
     */
    public function {$methodName}(Request \$request)
    {
        // TODO: Implement {$endpoint} logic
        return \$this->successResponse(null, '{$endpoint} endpoint');
    }

EOT;
        }

        $controller .= "}\n";

        $path = app_path("Http/Controllers/Api/V1/{$name}Controller.php");
        File::put($path, $controller);
    }

    protected function generateRoutes($name, $endpoints)
    {
        $routeName = strtolower($name);
        $controllerName = "{$name}Controller";

        $routes = <<<EOT
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\\{$controllerName};

/*
|--------------------------------------------------------------------------
| {$name} API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Resource routes
    Route::get('{$routeName}', [{$controllerName}::class, 'index']);
    Route::post('{$routeName}', [{$controllerName}::class, 'store']);
    Route::get('{$routeName}/{id}', [{$controllerName}::class, 'show']);
    Route::put('{$routeName}/{id}', [{$controllerName}::class, 'update']);
    Route::delete('{$routeName}/{id}', [{$controllerName}::class, 'destroy']);

EOT;

        // Add custom endpoints
        foreach ($endpoints as $endpoint) {
            $methodName = Str::camel($endpoint);
            $routes .= <<<EOT

    // Custom: {$endpoint}
    Route::post('{$routeName}/{$endpoint}', [{$controllerName}::class, '{$methodName}']);
EOT;
        }

        $routes .= "\n});\n";

        $path = base_path("routes/api/v1/{$routeName}.php");
        File::put($path, $routes);
    }

    protected function generateResource($name)
    {
        $resource = <<<EOT
<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class {$name}Resource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  \$request
     * @return array
     */
    public function toArray(\$request)
    {
        return [
            'id' => \$this->id,
            // TODO: Add more fields
            'created_at' => \$this->created_at,
            'updated_at' => \$this->updated_at,
        ];
    }
}
EOT;

        $path = app_path("Http/Resources/{$name}Resource.php");
        File::put($path, $resource);
    }

    protected function generateRequest($name)
    {
        $request = <<<EOT
<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class {$name}Request extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // TODO: Add validation rules
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator \$validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => \$validator->errors(),
                'code' => 'VALIDATION_ERROR'
            ], 422)
        );
    }
}
EOT;

        $path = app_path("Http/Requests/Api/{$name}Request.php");
        File::put($path, $request);
    }
}

