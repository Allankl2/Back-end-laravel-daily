<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'API funcionando',
    ]);
});

require __DIR__.'/auth.php';
require __DIR__.'/user.php';
require __DIR__.'/note.php';
