<?php

namespace Acelle\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        ini_set('memory_limit', '-1');
        ini_set('pcre.backtrack_limit', 1000000000);
        
        // Fix Laravel 5.4 error
        // [Illuminate\Database\QueryException]
        // SQLSTATE[42000]: Syntax error or access violation: 1071 Specified key was too long; max key length is 767 bytes
        Schema::defaultStringLength(191);
        
        if (!\App::runningInConsole()){
            // This is just a trick for getting Controller name in view
            // See https://stackoverflow.com/questions/29549660/get-laravel-5-controller-name-in-view
            // @todo: fix this anti-pattern
            app('view')->composer('*', function ($view) {
                $action = app('request')->route()->getAction();
                $controller = class_basename($action['controller']);
                list($controller, $action) = explode('@', $controller);
                $view->with(compact('controller', 'action'));
            });
        }
        
        // extend substring validator
        Validator::extend('substring', function ($attribute, $value, $parameters, $validator) {
            $tag = $parameters[0];
            if (strpos($value, $tag) === false) {
                return false;
            }

            return true;
        });
        Validator::replacer('substring', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':tag', $parameters[0], $message);
        });

        // License validator
        Validator::extend('license', function($attribute, $value, $parameters, $validator) {
            return $value == '' || true;
        });

        // License error validator
        Validator::extend('license_error', function($attribute, $value, $parameters, $validator) {
            return false;
        });
        Validator::replacer('license_error', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':error', $parameters[0], $message);
        });
    }

    /**
     * Register any application services.
     */
    public function register()
    {
    }
}
