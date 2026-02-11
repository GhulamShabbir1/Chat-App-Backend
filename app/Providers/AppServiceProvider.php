<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\GridFS\GridFSAdapter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register GridFS adapter for Flysystem
        Storage::extend('gridfs', function ($app, $config) {
            $database = $app['db']->connection('mongodb')->getMongoDB();
            $bucket = $database->selectGridFSBucket(['bucketName' => $config['bucket'] ?? 'fs']);
            return new \League\Flysystem\GridFS\GridFSAdapter($bucket);
        });
    }
}
