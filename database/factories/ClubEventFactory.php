<?php

namespace Database\Factories;

use App\Models\ClubEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClubEventFactory extends Factory
{
    protected $model = ClubEvent::class;

    public function definition()
    {
        return [
            'club_id' => \App\Models\Club::inRandomOrder()->first()?->id ?? \App\Models\Club::factory(),
            'created_by' => \App\Models\User::where('role', 'club_manager')->first()?->id ?? \App\Models\User::factory()->create(['role' => 'club_manager'])->id,
            'title' => $this->faker->sentence(4),
            'type' => $this->faker->randomElement(['offline', 'lien_hoan', 'hop']),
            'start_time' => $this->faker->dateTimeBetween('-1 month', '+2 months'),
            'end_time' => $this->faker->dateTimeBetween('+2 hours', '+8 hours'),
            'location' => $this->faker->optional()->sentence(),
            'is_published' => $this->faker->boolean(80),
        ];
    }
}