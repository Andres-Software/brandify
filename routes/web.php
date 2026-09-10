<?php

use App\Http\Controllers\PublicProposalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $clientSite = public_path('index.html');

    if (is_file($clientSite)) {
        return response()->file($clientSite);
    }

    return view('welcome');
})->name('welcome');

Route::get('/r/{directorySlug}/{proposalSlug}', [PublicProposalController::class, 'withDirectory'])
    ->name('proposals.public.directory');

Route::get('/r/{proposalSlug}', [PublicProposalController::class, 'withoutDirectory'])
    ->name('proposals.public');
