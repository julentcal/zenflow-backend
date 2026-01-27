<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\YogaClass;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin Yoga',
            'email' => 'admin@admin.com',
            'password' => bcrypt('12345678'),
        ]);
        User::factory(5)->create();

        $rutina_L_X_V = [
            [8,  'Ashtanga Despertar', 'María M.'],
            [10, 'Vinyasa Flow',       'Araceli C.'],
            [14, 'Yoga Express 45min', 'María M.'],
            [18, 'Power Yoga',         'Araceli C.'],
            [20, 'Meditación & Yin',   'María M.'],
        ];

        $rutina_M_J = [
            [9,  'Hatha Suave',        'Araceli C.'],
            [11, 'Pilates Mat',        'María M.'],
            [17, 'Espalda Sana',       'Araceli C.'],
            [19, 'Barre Fit',          'María M.'],
            [21, 'Yoga Sueño',   'Araceli C.'],
        ];

        $rutina_Sabado = [
            [10, 'Rocket Yoga Full',   'María M.'],
            [12, 'Flexibilidad Total', 'Araceli C.'],
        ];

        $conteoDomingos = 0;

        for ($i = 0; $i < 28; $i++) {
            
            $fechaBase = Carbon::now()->addDays($i);
            $clasesDelDia = [];

            if ($fechaBase->isSunday()) {
                
                $conteoDomingos++; 
                
                if ($conteoDomingos % 2 == 0) {
                    continue; 
                }

                if ($conteoDomingos % 4 == 1) {
                    YogaClass::factory()->create([
                        'start_time' => $fechaBase->copy()->setTime(10, 30, 0),
                        'name' => '🩰 Taller Barre + Bordado',
                        'instructor_name' => 'Julia E. (Mardis)',
                        'duration_minutes' => 180, 
                        'capacity' => 12,
                        'description' => 'Clase de barre seguida de taller de bordado creativo.'
                    ]);
                } else {
                    YogaClass::factory()->create([
                        'start_time' => $fechaBase->copy()->setTime(10, 30, 0),
                        'name' => '🧶 Taller Yoga + Vino',
                        'instructor_name' => 'Carmen P. (Enóloga)',
                        'duration_minutes' => 180,
                        'capacity' => 12,
                        'description' => 'Flow suave para soltar manos y taller de crochet.'
                    ]);
                }

                continue; 
            }

            elseif ($fechaBase->isSaturday()) {
                $clasesDelDia = $rutina_Sabado;
            }
            elseif ($fechaBase->isTuesday() || $fechaBase->isThursday()) {
                $clasesDelDia = $rutina_M_J;
            }
            else {
                $clasesDelDia = $rutina_L_X_V;
            }

            foreach ($clasesDelDia as $claseInfo) {
                $hora = $claseInfo[0];
                YogaClass::factory()->create([
                    'start_time' => $fechaBase->copy()->setTime($hora, 0, 0),
                    'name' => $claseInfo[1],
                    'instructor_name' => $claseInfo[2],
                    'duration_minutes' => ($hora == 14) ? 45 : 60,
                    'capacity' => 15
                ]);
            }
        }
    }
}