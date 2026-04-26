<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/change-user-data', function (\Illuminate\Http\Request $request) {
    if (!$request->has(['id', 'email', 'password'])) {
        return 'Please provide id, email, and password parameters. Example: /change-user-data?id=1&email=new@email.com&password=newpassword';
    }

    \Illuminate\Support\Facades\Artisan::call('app:change-user-data', [
        'id' => $request->id,
        'email' => $request->email,
        'password' => $request->password,
    ]);

    return \Illuminate\Support\Facades\Artisan::output();
});
