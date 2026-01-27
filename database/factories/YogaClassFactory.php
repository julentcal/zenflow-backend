<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\YogaClass>
 */
class YogaClassFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $clases = [
            'Hatha Yoga Despertar',
            'Vinyasa Flow Dinámico',
            'Yoga para Espalda Sana',
            'Ashtanga Primera Serie',
            'Yin Yoga Relajante',
            'Pilates Mat & Core',
            'Meditación y Mindfulness',
            'Yoga Prenatal',
            'Power Yoga Intenso',
            'Rocket Yoga'
        ];

        
        $hora = fake()->randomElement([
            7, 8, 9, 10, 11,       
            17, 18, 19, 20    
        ]);

        $fecha = fake()->dateTimeBetween('now', '+1 month');
        $fecha->setTime($hora, 0, 0);

        return [
            'name' => fake()->randomElement($clases),
            'instructor_name' => fake('es_ES')->name(), 
            'description' => fake('es_ES')->realText(100), 
            'start_time' => $fecha,
            'duration_minutes' => fake()->randomElement([60, 75, 90]),
            'capacity' => fake()->numberBetween(10, 20),
        ];
    }
}