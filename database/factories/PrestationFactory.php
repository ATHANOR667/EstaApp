<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Prestation;

class PrestationFactory extends Factory
{
    protected $model = Prestation::class;

    public function definition()
    {
        return [
            'contact_organisateur' => $this->faker->email,
            'nom_structure_contractante' => $this->faker->company,
            'contact_artiste' => $this->faker->email,
            'nom_representant_legal_artiste' => $this->faker->name,
            // Ajoutez d'autres champs nécessaires selon votre modèle Prestation
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
