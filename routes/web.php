<?php

use App\Http\Controllers\PublicProposalController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('welcome');

Route::get('/{directory:slug}/{proposal:slug}', PublicProposalController::class)
    ->name('proposals.public');
