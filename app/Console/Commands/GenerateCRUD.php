<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class GenerateCRUD extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud {name : Class (singular) for example User}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create CRUD operations';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    private $nameSingular, $namePlural, $nameSingularLowerCase, $namePluralLowerCase, $nameSingularCamelCase, $namePluralCamelCase, $nameTitleCase;

    public function __construct() {
        parent::__construct();
    }


    public function handle() {
        //get base name from console command input
        $name = $this->argument('name');

        $this->nameSingular = $name;
        $this->namePlural = Str::plural($name);
        $this->nameSingularLowerCase = Str::lower(Str::snake($name));
        $this->namePluralLowerCase = Str::lower(Str::plural(Str::snake($name)));
        $this->nameSingularCamelCase = Str::camel($name);
        $this->namePluralCamelCase = Str::camel(Str::plural($name));

        $slug = Str::snake($name);
        $slug = str_replace('_', ' ', $slug);
        $this->nameTitleCase = Str::title($slug);

        //generate the crud files
        $this->model();
        $this->migration();
        $this->factory();
        $this->seeder();
        $this->controller();
        $this->request();
        $this->views();
        $this->route();
        $this->test();
        $this->permissions();

        //cleaning up
        system('composer dump-autoload');
    }

    /**
     * Get stub file from selected type.
     * This file will be used for generating file according it's type
     *
     * @param string $type
     * @return string
     */
    protected function getStub($type) {
        return file_get_contents(resource_path("stubs/$type.stub"));
    }

    /**
     * Generate the Model file
     * The model will be located at app/Models/
     *
     * @param string $name
     */
    protected function model() {
        $modelTemplate = str_replace(
            ['{{modelName}}'],
            [$this->nameSingular],
            $this->getStub('Model')
        );

        if(!file_exists($path = app_path('/Models')))
            mkdir($path, 0777, true);

        file_put_contents(app_path("/Models/{$this->nameSingular}.php"), $modelTemplate);

        print "Model created at \e[32m/app/Models/{$this->nameSingular}.php \e[0m\n";
    }

    /**
     * Generate the database migration
     * The migration will be located at database/migrations
     *
     * @param string $name
     */
    protected function migration() {
        if(! Schema::hasTable($this->namePluralLowerCase)) {
            Artisan::call('make:migration create_' . $this->namePluralLowerCase . '_table');

            print "Migration created \n";
        }
    }

    /**
     * Generate the database factory
     * The factory will be located at database/factories
     *
     * @param string $name
     * @return void
     */
    protected function factory() {
        $factoryTemplate = str_replace(
            [
                '{{modelName}}',
            ],
            [
                $this->nameSingular,
            ],
            $this->getStub('Factory')
        );

        file_put_contents(base_path("/database/factories/{$this->nameSingular}Factory.php"), $factoryTemplate);

        print "Factory created at \e[32m/database/factories/{$this->nameSingular}Factory.php \e[0m\n";
    }

    /**
     * GGenerate the database seeder
     * The seeder will be located at database/seeders
     * The generated seeder will also registered on database/seeders/DatabaseSeeder.php
     *
     * @param string $name
     * @return void
     */
    protected function seeder() {
        $seederTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePlural}}',
                '{{modelNamePluralLowerCase}}',
            ],
            [
                $this->nameSingular,
                $this->namePlural,
                $this->namePluralLowerCase,
            ],
            $this->getStub('Seeder')
        );

        file_put_contents(base_path("database/seeders/{$this->namePlural}TableSeeder.php"), $seederTemplate);

        print "Seeder created at \e[32m/database/seeders/{$this->namePlural}TableSeeder.php \e[0m\n";

        //add the seeder to DatabaseSeeder.php
        $file = base_path('database/seeders/DatabaseSeeder.php');
        $f = fopen($file, "r+");

        $oldstr = file_get_contents($file);
        $str_to_insert = "\t".'$this->call('."{$this->namePlural}TableSeeder::class);\n";
        $specificLine = '//reserved crud generator, do not delete or modify this line';

        while (($buffer = fgets($f)) !== false) {
            if (strpos($buffer, $specificLine) !== false) {
                $pos = ftell($f);
                $newstr = substr_replace($oldstr, $str_to_insert, $pos, 0);
                file_put_contents($file, $newstr);
                break;
            }
        }
        fclose($f);

        print "Seeder successfully registered at \e[32m/database/seeders/DatabaseSeeder.php \e[0m\n";
    }

    /**
     * Generate the resource controller
     * The controller will be located at app/Https/Controllers
     *
     * @return void
     */
    protected function controller() {
        $namespace = 'App\Http\Controllers';

        $controllerTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNameSingularLowerCase}}',
                '{{modelNameSingularCamelCase}}',
                '{{modelNamePluralCamelCase}}',
                '{{modelNameTitleCase}}',
                '{{namespace}}',
            ],
            [
                $this->nameSingular,
                $this->namePluralLowerCase,
                $this->nameSingularLowerCase,
                $this->nameSingularCamelCase,
                $this->namePluralCamelCase,
                $this->nameTitleCase,
                $namespace,
            ],
            $this->getStub('Controller')
        );

        file_put_contents(app_path("/Http/Controllers/{$this->nameSingular}Controller.php"), $controllerTemplate);

        print "Controller created at \e[32m/app/Http/Controllers/{$this->nameSingular}Controller.php \e[0m\n";
    }

    /**
     * Generate the resource request
     * The request will be located at app/Https/Requests
     *
     * @return void
     */
    protected function request() {
        $requestTemplate = str_replace(
            ['{{modelName}}'],
            [$this->nameSingular],
            $this->getStub('Request')
        );

        if(!file_exists($path = app_path('/Http/Requests')))
            mkdir($path, 0777, true);

        file_put_contents(app_path("/Http/Requests/{$this->nameSingular}Request.php"), $requestTemplate);

        print "Request created at \e[32m/app/Http/Requests/{$this->nameSingular}Request.php \e[0m\n";
    }


    /**
     * Generate the resource views
     * The views will be located at resources/views/admin/{plural_name}
     * The generated views are index, add, edit and show
     *
     * @return void
     */
    protected function views() {
        $folder = $this->namePluralLowerCase;

        if(!file_exists($path = base_path("Resources/views/{$folder}")))
            mkdir($path, 0777, true);

        $createTemplate = str_replace(
            [
                '{{modelNameSingularLowerCase}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelName}}',
                '{{modelNameSingularCamelCase}}',
                '{{modelNameTitleCase}}',
            ],
            [
                $this->nameSingularLowerCase,
                $this->namePluralLowerCase,
                $this->nameSingular,
                $this->nameSingularCamelCase,
                $this->nameTitleCase,
            ],
            $this->getStub('views/create')
        );

        $showTemplate = str_replace(
            [
                '{{modelNameSingularLowerCase}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelName}}',
                '{{modelNameSingularCamelCase}}',
                '{{modelNameTitleCase}}',
            ],
            [
                $this->nameSingularLowerCase,
                $this->namePluralLowerCase,
                $this->nameSingular,
                $this->nameSingularCamelCase,
                $this->nameTitleCase,
            ],
            $this->getStub('views/show')
        );

        $editTemplate = str_replace(
            [
                '{{modelNameSingularLowerCase}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelName}}',
                '{{modelNameSingularCamelCase}}',
                '{{modelNameTitleCase}}',
            ],
            [
                $this->nameSingularLowerCase,
                $this->namePluralLowerCase,
                $this->nameSingular,
                $this->nameSingularCamelCase,
                $this->nameTitleCase,
            ],
            $this->getStub('views/edit')
        );

        $indexTemplate = str_replace(
            [
                '{{modelNameSingularLowerCase}}',
                '{{modelNamePluralCamelCase}}',
                '{{modelName}}',
                '{{modelNamePlural}}',
                '{{modelNameTitleCase}}',
            ],
            [
                $this->nameSingularLowerCase,
                $this->namePluralCamelCase,
                $this->nameSingular,
                $this->namePlural,
                $this->nameTitleCase,
            ],
            $this->getStub('views/index')
        );


        file_put_contents(base_path("Resources/views/{$folder}/index.blade.php"), $indexTemplate);
        print "View (index) created at \e[32m/Resources/views/{$folder}/index.blade.php \e[0m\n";

        file_put_contents(base_path("Resources/views/{$folder}/create.blade.php"), $createTemplate);
        print "View (create) created at \e[32m/Resources/views/{$folder}/create.blade.php \e[0m\n";

        file_put_contents(base_path("Resources/views/{$folder}/show.blade.php"), $showTemplate);
        print "View (show) created at \e[32m/Resources/views/{$folder}/show.blade.php \e[0m\n";

        file_put_contents(base_path("Resources/views/{$folder}/edit.blade.php"), $editTemplate);
        print "View (edit) created at \e[32m/Resources/views/{$folder}/edit.blade.php \e[0m\n";

    }

    /**
     * Register the resource route to routes/web.php
     *
     * @return void
     */
    protected function route() {
        $file = base_path("routes/web.php");
        $f = fopen($file, "r+");

        $oldstr = file_get_contents($file);
        $str_to_insert = "\t".'Route::resource(\''.'/' . strtolower($this->nameSingularLowerCase) . "', '{$this->nameSingular}Controller');\n";
        $specificLine = '//reserved crud generator, do not delete or modify this line';

        while (($buffer = fgets($f)) !== false) {
            if (strpos($buffer, $specificLine) !== false) {
                $pos = ftell($f);
                $newstr = substr_replace($oldstr, $str_to_insert, $pos, 0);
                file_put_contents($file, $newstr);
                break;
            }
        }
        fclose($f);

        print "Resource route /".strtolower($this->nameSingularLowerCase)." successfully registered at \e[32m/Routes/web.php \e[0m\n";
    }

    /**
     * Generate the test feature for generated CRUD
     * The test feature will be located at tests/Feature/Admin
     *
     * @return void
     */
    protected function test() {
        $testTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNameSingularLowerCase}}',
            ],
            [
                $this->nameSingular,
                $this->namePluralLowerCase,
                $this->nameSingularLowerCase,
            ],
            $this->getStub('Test')
        );

        if(!file_exists($path = base_path('/tests/Feature')))
            mkdir($path, 0777, true);

        file_put_contents(base_path("/tests/Feature/{$this->nameSingular}ResourceTest.php"), $testTemplate);
        print "Test created at \e[32m/tests/Feature/{$this->nameSingular}ResourceTest.php \e[0m\n";
    }

    protected function permissions()
    {

        //add the permissions group to PermissionsTableSeeder.php
        $file = base_path('database/seeders/PermissionsTableSeeder.php');
        $f = fopen($file, "r+");

        $oldstr = file_get_contents($file);
        $str_to_insert = " \n \t \t'{$this->nameTitleCase} Management', \n \t \t \t 'Create {$this->nameTitleCase}', \n \t \t \t 'Edit {$this->nameTitleCase}',  \n \t \t \t 'Delete {$this->nameTitleCase}', \n";
        $specificLine = '//reserved crud generator, do not delete or modify this line';

        while (($buffer = fgets($f)) !== false) {
            if (strpos($buffer, $specificLine) !== false) {
                $pos = ftell($f);
                $newstr = substr_replace($oldstr, $str_to_insert, $pos, 0);
                file_put_contents($file, $newstr);
                break;
            }
        }
        fclose($f);

        print "Permissions successfully registered at \e[32m/database/seeders/PermissionsTableSeeder.php \e[0m\n";
    }
}
