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
        // 1. Crear/actualizar ADMIN con saldo para pruebas
        User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin Yoga',
                'password' => bcrypt('12345678'),
                'credits' => 20, // <--- LE DAMOS SALDO PARA PROBAR
            ]
        );

        // 2. Crear usuarios normales (solo si faltan)
        $existingUsers = User::where('email', '!=', 'admin@admin.com')->count();
        $usersToCreate = max(0, 5 - $existingUsers);
        if ($usersToCreate > 0) {
            User::factory($usersToCreate)->create();
        }

        // 3. DEFINICIÓN DE RUTINAS
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
            [21, 'Yoga Sueño',         'Araceli C.'],
        ];

        $rutina_Sabado = [
            [10, 'Rocket Yoga Full',   'María M.'],
            [12, 'Flexibilidad Total', 'Araceli C.'],
        ];

        $conteoDomingos = 0;

        // 4. GENERAR CLASES PARA LOS PRÓXIMOS 28 DÍAS
        for ($i = 0; $i < 28; $i++) {
            
            $fechaBase = Carbon::now()->addDays($i);
            $clasesDelDia = [];

            // --- LÓGICA DE DOMINGOS (EVENTOS ESPECIALES) ---
            if ($fechaBase->isSunday()) {
                
                $conteoDomingos++; 
                
                // Saltamos domingos alternos (uno sí, uno no)
                if ($conteoDomingos % 2 == 0) {
                    continue; 
                }

                if ($conteoDomingos % 4 == 1) {
                    // EVENTO 1: BARRE
                    $startTime = $fechaBase->copy()->setTime(10, 30, 0);
                    YogaClass::updateOrCreate(
                        ['start_time' => $startTime, 'name' => '🩰 Taller Barre + Bordado'],
                        [
                            'instructor_name' => 'Julia E. (Mardis)',
                            'duration_minutes' => 180,
                            'capacity' => 12,
                            'credit_cost' => 4,
                            'description' => 'Clase de barre seguida de taller de bordado creativo. (Precio especial: 4 bonos)'
                        ]
                    );
                } else {
                    // EVENTO 2: YOGA + VINO
                    $startTime = $fechaBase->copy()->setTime(10, 30, 0);
                    YogaClass::updateOrCreate(
                        ['start_time' => $startTime, 'name' => '🧶 Taller Yoga + Vino'],
                        [
                            'instructor_name' => 'Carmen P. (Enóloga)',
                            'duration_minutes' => 180,
                            'capacity' => 12,
                            'credit_cost' => 4,
                            'description' => 'Flow suave para soltar manos y taller de crochet. (Precio especial: 4 bonos)'
                        ]
                    );
                }

                continue; // Termina el domingo, pasa al siguiente día
            }

            // --- LÓGICA DE DÍAS NORMALES ---
            elseif ($fechaBase->isSaturday()) {
                $clasesDelDia = $rutina_Sabado;
            }
            elseif ($fechaBase->isTuesday() || $fechaBase->isThursday()) {
                $clasesDelDia = $rutina_M_J;
            }
            else {
                $clasesDelDia = $rutina_L_X_V;
            }

            // Crear las clases normales del día
            foreach ($clasesDelDia as $claseInfo) {
                $hora = $claseInfo[0];
                $startTime = $fechaBase->copy()->setTime($hora, 0, 0);
                YogaClass::updateOrCreate(
                    ['start_time' => $startTime, 'name' => $claseInfo[1]],
                    [
                        'instructor_name' => $claseInfo[2],
                        'duration_minutes' => ($hora == 14) ? 45 : 60,
                        'capacity' => 15,
                        'credit_cost' => 1
                    ]
                );
            }
        }
    }
}