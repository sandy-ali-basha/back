<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateModelFiles extends Command
{
    protected $signature   = 'generate:model-files {model} {lunar}';
    protected $description = 'Generate Laravel files for a model (controller, service, operations, and route resource)';

    public function handle()
    {
        $modelName      = $this->argument('model');
        $serviceName      = Str::lower($modelName);
        $isLunar = false;
        if ($this->argument('lunar')=="lunar"){
            $isLunar = true;
            $modelClass     = "Lunar\\Models\\$modelName";

        }
        else{
            $modelClass     = "App\\Models\\$modelName";

        }
        $fillableFields = [];
        while ($this->confirm('Do you wish to add a model field?')) {
        $type = $this->ask('What is the field type?', 'string');
        $fieldName = $this->ask('What is the field name?', '');
        $fillableFields[$type] = $fieldName;
    }

        if ($this->confirm('Do you want to create  a model?')){
            $mainModelStub = File::get(base_path("app/stubs/mainModel.stub"));
            $modelTranslationStub = File::get(base_path('app/stubs/modelTranslation.stub'));
            $migrationTranslationStub = File::get(base_path('app/stubs/translationMigration.stub'));
            $migrationStub = File::get(base_path('app/stubs/migration.stub'));
            $mainModelContent = $this->replacePlaceholders($mainModelStub, $modelName,$serviceName,$modelClass, $fillableFields);
            $modelTranslationContent = $this->replacePlaceholders($modelTranslationStub, $modelName,$serviceName,$modelClass,$fillableFields);
            $migrationTranslationContent = $this->replacePlaceholders($migrationTranslationStub, $modelName,$serviceName,$modelClass,$fillableFields);
            $migrationContent = $this->replacePlaceholders($migrationStub, $modelName,$serviceName,$modelClass,$fillableFields);
            $modelTranslationPath = app_path("models/{$modelName}ModelTranslation.php");
            File::put($modelTranslationPath, $modelTranslationContent);
            $migrationTranslationPath = app_path("../database/migrations/create_{$serviceName}_translation.php");
            File::put($migrationTranslationPath, $migrationTranslationContent);
            $migrationPath = app_path("../database/migrations/create_{$serviceName}.php");
            File::put($migrationPath, $migrationContent);

            $mainModelPath = app_path("models/{$modelName}.php");
            File::put($mainModelPath, $mainModelContent);
        }

        $controllerStub = File::get(base_path('app/stubs/controller.stub'));
        $serviceStub = File::get(base_path('app/stubs/service.stub'));
        $collectionStub = File::get(base_path('app/stubs/collection.stub'));
        $resourceStub = File::get(base_path('app/stubs/resource.stub'));
        $addDataStub = File::get(base_path('app/stubs/dataAdd.stub'));
        $updateDataStub = File::get(base_path('app/stubs/dataUpdate.stub'));
        $opAddStub = File::get(base_path('app/stubs/operationAddData.stub'));
        $opUpdateStub = File::get(base_path('app/stubs/operationUpdateData.stub'));
        $opDeleteStub = File::get(base_path('app/stubs/operationDeleteData.stub'));

        // 3. Replace placeholders in the stub
        $controllerContent = $this->replacePlaceholders($controllerStub, $modelName,$serviceName,$modelClass, $fillableFields);
        $serviceContent = $this->replacePlaceholders($serviceStub, $modelName,$serviceName,$modelClass, $fillableFields);
        $collectionContent = $this->replacePlaceholders($collectionStub, $modelName,$serviceName,$modelClass, $fillableFields);
        $resourceContent = $this->replacePlaceholders($resourceStub, $modelName,$serviceName,$modelClass, $fillableFields);
        $addDataContent = $this->replacePlaceholders($addDataStub, $modelName,$serviceName,$modelClass, $fillableFields);
        $updateDataContent = $this->replacePlaceholders($updateDataStub, $modelName,$serviceName,$modelClass, $fillableFields);
        $opAddContent = $this->replacePlaceholders($opAddStub, $modelName,$serviceName,$modelClass, $fillableFields);
        $opUpdateContent = $this->replacePlaceholders($opUpdateStub, $modelName,$serviceName,$modelClass, $fillableFields);
        $opDeleteContent = $this->replacePlaceholders($opDeleteStub, $modelName,$serviceName,$modelClass, $fillableFields);

        $controllerPath = app_path("Http/Controllers/{$modelName}Controller.php");
        File::put($controllerPath, $controllerContent);
        $servicePath = app_path("services/{$modelName}Service.php");
        File::put($servicePath, $serviceContent);
        $collectionPath = app_path("http/Resources/{$modelName}Collection.php");
        File::put($collectionPath, $collectionContent);
        $resourcePath = app_path("http/Resources/{$modelName}Resource.php");
        File::put($resourcePath, $resourceContent);
        $addDataPath = app_path("http/Data/Add{$modelName}Data.php");
        File::put($addDataPath, $addDataContent);
        $updateDataPath = app_path("http/Data/Update{$modelName}Data.php");
        File::put($updateDataPath, $updateDataContent);
        $opAddPath = app_path("http/Patterns/Add{$modelName}.php");
        File::put($opAddPath,$opAddContent);
        $opUpdatePath = app_path("http/Patterns/Update{$modelName}.php");
        File::put($opUpdatePath,$opUpdateContent);
        $opDeletePath = app_path("http/Patterns/Delete{$modelName}.php");
        File::put($opDeletePath,$opDeleteContent);
        if ($isLunar){
            $modelStub = File::get(base_path('app/stubs/model.stub'));
            $modelContent = $this->replacePlaceholders($modelStub, $modelName,$serviceName,$modelClass,$fillableFields);
            $modelPath = app_path("models/{$modelName}Model.php");
            File::put($modelPath, $modelContent);

        }
        // 5. Display a success message
        $this->info("Controller generated successfully for model: $modelName");

    // 3. Generate the necessary files:
    //    - Service: Create a service file (e.g., ModelService.php) that communicates with the model
    //    - Operation Classes: Create separate classes (e.g., UpdateModelOperation.php, DeleteModelOperation.php)
    //      for specific operations
    //    - Route Resource: Add a route resource for the model in your routes/web.php file

    // 4. Use Laravel's Filesystem to create the necessary files and populate them with content

    // 5. Handle edge cases (e.g., existing files, validation, etc.)

    // 6. Display a success message
$this->info("Files generated successfully for model: $modelName");
}

    private function replacePlaceholders(string $stub, string $modelName,string $serviceName,$modelPath, array $fillableFields): string
    {
        $vars = "";
        $constructorVars = "";
        $data = "";
        $migrations = "";
        $values = "";
        foreach ($fillableFields as  $key=> $propertyValue) {
            $vars .= "public $key \$$propertyValue;\n";
            $constructorVars .= "$key \$$propertyValue,\n";
            $data .= "'".$propertyValue."'".' => $this->'."$propertyValue,\n";
            $migrations .= "\$table->$key('$propertyValue')->nullable();\n";
            $values .= "'$propertyValue',";

        }

        $replacements = [
            '$MODEL_NAME' => $modelName,
            '$MODEL_PATH' => $modelPath,
            '$SERVICE_NAME' => $serviceName,
            '$VARS' =>$vars,
            '$CONSTRUCTOR_VARS' =>$constructorVars,
            '$DATA' =>$data,
            '$MIGRATIONS' =>$migrations,
            '$VALUES' =>$values
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $stub);
    }
}
