<?php

namespace Database\Factories;

use App\Models\MasterItem;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MasterItem>
 */
class MasterItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MasterItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['Electronics', 'Office Supplies', 'Furniture', 'IT Equipment', 'Kitchen Supplies'];
        $buyers = ['John Doe', 'Jane Smith', 'Robert Johnson', 'Emily Davis', 'Michael Brown'];

        return [
            'uuid' => Str::uuid(),
            'category_item_id' => $this->faker->numberBetween(1, 100),
            'aol_id' => $this->faker->bothify('AOL-####-????'),
            'item_code' => $this->faker->unique()->bothify('ITEM-####'),
            'item_name' => $this->faker->words(3, true),
            'item_category' => $this->faker->randomElement($categories),
            'buyer' => $this->faker->randomElement($buyers),
            'ppn' => $this->faker->randomFloat(2, 0, 10),
            'pph' => $this->faker->randomFloat(2, 0, 5),
        ];
    }
}
