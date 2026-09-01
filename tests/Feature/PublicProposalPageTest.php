<?php

namespace Tests\Feature;

use App\Models\Directory;
use App\Models\Proposal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicProposalPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_proposal_page_with_directory_returns_a_successful_response(): void
    {
        $directory = Directory::factory()->create();
        $proposal = Proposal::factory()->for($directory)->create();

        $response = $this->get("/r/{$directory->slug}/{$proposal->slug}");

        $response->assertStatus(200);
    }

    public function test_public_proposal_page_without_directory_returns_a_successful_response(): void
    {
        $proposal = Proposal::factory()->create(['directory_id' => null]);

        $response = $this->get("/r/{$proposal->slug}");

        $response->assertStatus(200);
    }

    public function test_unknown_proposal_returns_not_found(): void
    {
        $directory = Directory::factory()->create();

        $response = $this->get("/r/{$directory->slug}/nao-existe");

        $response->assertStatus(404);
    }

    public function test_unknown_proposal_without_directory_returns_not_found(): void
    {
        $response = $this->get('/r/nao-existe');

        $response->assertStatus(404);
    }

    public function test_expired_proposal_returns_not_found(): void
    {
        $directory = Directory::factory()->create();
        $proposal = Proposal::factory()->expired()->for($directory)->create();

        $response = $this->get("/r/{$directory->slug}/{$proposal->slug}");

        $response->assertStatus(404);
    }
}
