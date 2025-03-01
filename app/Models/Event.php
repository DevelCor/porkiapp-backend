<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'message',
        'pig_id',
        'farm_id',
        'treatment_id',
        'reminder_date',
        'type',
        'active',
    ];

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    public function pig()
    {
        return $this->belongsTo(Pig::class);
    }

    public function treatment()
    {
        return $this->belongsTo(PigTreatment::class, 'treatment_id');
    }

    public static function generateTreatmentsForPig(Pig $pig)
    {
        // Define el protocolo de tratamientos; puedes ajustarlo según tus necesidades.
        $protocols = [
            ['day' => 1,  'name' => 'Día 1 hierro+ complejo b', 'dosage' => 'Dose 1'],
            ['day' => 3,  'name' => 'Día 3 hierro+ complejo b', 'dosage' => 'Dose 3'],
            ['day' => 7,  'name' => 'Día 7 hierro+ complejo b', 'dosage' => 'Dose 7'],
            ['day' => 14, 'name' => 'Día 14 hierro+ complejo b', 'dosage' => 'Dose 14'],
            ['day' => 30, 'name' => 'Día 30 hierro+ complejo b', 'dosage' => 'Dose 30'],
            // Puedes agregar más protocolos o tratamientos adicionales aquí...
        ];

        // Iterar sobre cada protocolo para crear tratamientos y eventos
        foreach ($protocols as $protocol) {
            // Calcula la fecha de recordatorio basándose en la fecha de nacimiento del cerdo
            $reminderDate = Carbon::parse($pig->birth_date)->addDays($protocol['day']);

            // Crea el tratamiento con status "pending"
            $treatment = self::create([
                'pig_id'      => $pig->id,
                'name'        => $protocol['name'],
                'description' => $protocol['name'] . ' - ' . $protocol['dosage'],
                'dosage'      => $protocol['dosage'],
                'status'      => 'pending'
            ]);

            // Crea el evento asociado para avisar al usuario
            Event::create([
                'message'       => 'Administrar tratamiento: ' . $protocol['name'],
                'pig_id'        => $pig->id,
                'farm_id'       => $pig->farm_id,
                'treatment_id'  => $treatment->id,
                'reminder_date' => $reminderDate,
                'type'          => 'automatic',
                'active'        => true
            ]);
        }
    }
}
