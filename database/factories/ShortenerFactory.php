<?php

namespace Database\Factories;

use App\Models\Shortener;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Shortener>
 */
class ShortenerFactory extends Factory
{
    protected $model = Shortener::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'UserID' => null,
            'Hash' => $this->faker->unique()->bothify('????##'),
            'Url' => $this->faker->url(),
            'Description' => 'Test Description',
            'Frequency' => $this->faker->numberBetween(0, 100),
            'CreationDate' => now(),
            'UpdateDate' => now(),
            'PublishDate' => null,
        ];
    }

    /**
     * State: for a specific user
     */
    public function forUser($userId): static
    {
        return $this->state(function (array $attributes) use ($userId) {
            return [
                'UserID' => $userId,
            ];
        });
    }

    /**
     * State: published
     */
    public function published(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'PublishDate' => now()->subDay(),
            ];
        });
    }

    /**
     * State: future publish
     */
    public function futurePublish(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'PublishDate' => now()->addDay(),
            ];
        });
    }

    /**
     * State: with custom hash
     */
    public function withHash(string $hash): static
    {
        return $this->state(function (array $attributes) use ($hash) {
            return [
                'Hash' => $hash,
            ];
        });
    }

    /**
     * State: deleted
     */
    public function deleted(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'DeleteDate' => now(),
            ];
        });
    }
}
