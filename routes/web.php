<?php

use App\Http\Controllers\PublicProposalController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('welcome');

Route::get('/r/{directorySlug}/{proposalSlug}', [PublicProposalController::class, 'withDirectory'])
    ->name('proposals.public.directory');

Route::get('/r/{proposalSlug}', [PublicProposalController::class, 'withoutDirectory'])
    ->name('proposals.public');
