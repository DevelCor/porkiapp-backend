<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Event;
use Illuminate\Support\Facades\Log;

class PigTreatment extends Model
{
    use HasFactory;

    protected $table = 'pig_treatments';

    protected $fillable = [
        'pig_id',
        'name',
        'description',
        'dosage',
        'status', // 'pending' o 'administered'
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    /**
     * Relación: Un tratamiento pertenece a un cerdo.
     */
    public function pig()
    {
        return $this->belongsTo(Pig::class);
    }

    /**
     * Relación: Un tratamiento puede tener varios eventos asociados.
     */
    public function events()
    {
        return $this->hasMany(Event::class, 'treatment_id');
    }

    public static function applyStandardProtocol(Pig $pig)
    {
        $daysOld = Carbon::parse($pig->birth_date)->diffInDays(Carbon::now());
        $daysOld = floor($daysOld);

        $newbornProtocol = [
            ['day' => 1,  'name' => 'Hierro + Complejo B', 'dosage' => '' ],
            ['day' => 3,  'name' => 'Hierro + Complejo B', 'dosage' => '' ],
            ['day' => 7,  'name' => 'Hierro + Complejo B', 'dosage' => '' ],
            ['day' => 14, 'name' => 'Hierro + Complejo B', 'dosage' => '' ],
            ['day' => 30, 'name' => 'Hierro + Complejo B', 'dosage' => '' ],

            // Desparasitante
            ['day' => 7,  'name' => 'Albendazol',  'dosage' => '' ],
            ['day' => 14, 'name' => 'Albendazol',  'dosage' => '' ],
            ['day' => 28, 'name' => 'Albendazol',  'dosage' => '' ],
            ['day' => 30, 'name' => 'Ivermectina', 'dosage' => '' ],
        ];

        $adultMaleProtocol = [
            ['day' => 30, 'name' => 'Vitaminas',    'dosage' => 'Aplicación mensual' ],
            ['day' => 90, 'name' => 'Desparacitar', 'dosage' => 'Aplicación trimestral'],
        ];

        $protocolsToApply = [];
        if ($daysOld < 30) {
            $protocolsToApply = array_merge($protocolsToApply, $newbornProtocol);
        }

        // Si es macho adulto (ejemplo: más de 180 días)
        if ($pig->gender === 'male' && $daysOld >= 180) {
            $protocolsToApply = array_merge($protocolsToApply, $adultMaleProtocol);
        }

        // Crear los tratamientos y eventos
        foreach ($protocolsToApply as $protocol) {
            $treatmentDay   = $protocol['day'];
            $treatmentName  = $protocol['name'];
            $treatmentDosage = $protocol['dosage'];

            // Fecha en la que se debería administrar el tratamiento
            $reminderDate = Carbon::parse($pig->birth_date)->addDays($treatmentDay);
            $status = ($daysOld > $treatmentDay) ? 'administered' : 'pending';

            $treatment = self::create([
                'pig_id'      => $pig->id,
                'name'        => $treatmentName,
                'description' => "$treatmentName - $treatmentDosage",
                'dosage'      => $treatmentDosage,
                'status'      => $status
            ]);

            if ($status === 'pending') {
                Event::create([
                    'message'       => "Administrar tratamiento: $treatmentName",
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

    public static function applyPostpartumProtocol(Pig $pig)
    {
        if ($pig->gender !== 'female') {
            return;
        }

        // Ejemplo: día 28 y 30 tras el parto
        $postpartumProtocol = [
            ['day' => 28, 'name' => 'Albendazol',          'dosage' => 'Postparto día 28'],
            ['day' => 28, 'name' => 'Ivermectina',         'dosage' => 'Postparto día 28'],
            ['day' => 30, 'name' => 'Hierro + Complejo B', 'dosage' => 'Postparto día 30'],
            ['day' => 30, 'name' => 'Vitaminas',           'dosage' => 'Postparto día 30'],
        ];

        $today = Carbon::now();

        foreach ($postpartumProtocol as $protocol) {
            $reminderDate = $today->copy()->addDays($protocol['day']);

            $treatment = self::create([
                'pig_id'      => $pig->id,
                'name'        => $protocol['name'],
                'description' => $protocol['name'].' - '.$protocol['dosage'],
                'dosage'      => $protocol['dosage'],
                'status'      => 'pending'
            ]);

            Event::create([
                'message'       => "Administrar tratamiento postparto: {$protocol['name']}",
                'pig_id'        => $pig->id,
                'farm_id'       => $pig->farm_id,
                'treatment_id'  => $treatment->id,
                'reminder_date' => $reminderDate,
                'type'          => 'postpartum',
                'active'        => true
            ]);
        }
    }
}
