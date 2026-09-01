<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Settings\GeneralSettings;
use Illuminate\Contracts\View\View;

class PublicProposalController extends Controller
{
    /**
     * Proposta sem diretório: /r/{proposalSlug}
     */
    public function withoutDirectory(string $proposalSlug): View
    {
        $proposal = Proposal::query()
            ->whereNull('directory_id')
            ->where('slug', $proposalSlug)
            ->firstOrFail();

        return $this->render($proposal);
    }

    /**
     * Proposta dentro de um diretório: /r/{directorySlug}/{proposalSlug}
     */
    public function withDirectory(string $directorySlug, string $proposalSlug): View
    {
        $proposal = Proposal::query()
            ->whereHas('directory', fn ($query) => $query->where('slug', $directorySlug))
            ->where('slug', $proposalSlug)
            ->firstOrFail();

        return $this->render($proposal);
    }

    private function render(Proposal $proposal): View
    {
        abort_if($proposal->isExpired(), 404);

        return view('public.proposal', [
            'proposal' => $proposal,
            'settings' => app(GeneralSettings::class),
        ]);
    }
}
