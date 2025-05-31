<?php

use Avexsoft\FilamentPurl\Models\Redirect;
use Illuminate\Support\Facades\Route;

Route::name('purl')
    ->get('purl/{slug}', function (string $slug) {
        $database = Redirect::whereSlug($slug)
            ->where('is_active', true)
            ->firstOrFail();

        if (empty($database->url)) {
            abort(404);
        }

        return redirect($database->url);
    })->where('path', '.*');
