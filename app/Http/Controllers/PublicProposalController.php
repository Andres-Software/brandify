<?php

namespace App\Http\Controllers;

use App\Models\Directory;
use App\Models\Proposal;
use App\Settings\GeneralSettings;
use Illuminate\Contracts\View\View;

class PublicProposalController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Directory $directory, Proposal $proposal): View
    {
        abort_unless($proposal->directory_id === $directory->id, 404);
        abort_if($proposal->isExpired(), 404);

        return view('public.proposal', [
            'proposal' => $proposal,
            'settings' => app(GeneralSettings::class),
        ]);
    }
}
