<?php

use Illuminate\Support\Facades\Route;

// Debug routes removed

// Include separate route files
require __DIR__.'/auth.php';
require __DIR__.'/workspace.php';
require __DIR__.'/team.php';
require __DIR__.'/channel.php';
require __DIR__.'/message.php';
require __DIR__.'/file.php';
