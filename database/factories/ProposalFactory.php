<?php

namespace Database\Factories;

use App\Models\Directory;
use App\Models\Proposal;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Proposal>
 */
class ProposalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(4);

        return [
            'directory_id' => Directory::factory(),
            'slug' => Str::slug($title).'-'.$this->faker->unique()->numberBetween(1, 999999),
            'description' => $title,
            'embed_src' => 'https://gamma.app/embed/'.Str::lower(Str::random(16)),
            'embed_raw' => sprintf(
                '<iframe src="https://gamma.app/embed/%s" style="width: 700px; max-width: 100%%; height: 450px" allow="fullscreen" title="%s"></iframe>',
                Str::lower(Str::random(16)),
                $title
            ),
            'expires_at' => null,
        ];
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subDay(),
        ]);
    }
}
