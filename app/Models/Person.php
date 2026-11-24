<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $table = 'persons';
   protected $fillable = [
    'user_id',
    'parent_id',
    'guardian_docs',
    'complex_id',
    'firstname',
    'lastname',
    'birth_date',
    'birth_city',
    'gender',
    'blood_type',
    'profession',
    'handicap',
    'phone',
    'address',
    'wilaya',
    'study_level',
    'education',
    'favorite_activity',
    'photo',
    'birth_certificate',
    'document_birth',
    'document_photo',
    'parent_firstname',
    'parent_lastname',
    'parent_phone',
    'parent_relation',
    'age_category_id',
    'club_id','license_number' ,'tuteur_fullname'
];

    protected $casts = [
        'guardian_docs' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Person::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Person::class, 'parent_id');
    }

    public function isChild(): bool
    {
        return !empty($this->parent_id);
    }

    public function ownerUser()
    {
        return $this->user ?: optional($this->parent)->user;
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function complex()
    {
        return $this->belongsTo(Complex::class, 'complex_id');
    }

    public function ageCategory()
    {
        return $this->belongsTo(AgeCategory::class);
    }

    /**
     * Si la categorie d'age est vide, la deduire de la date de naissance
     * (min_age <= age <= max_age) et l'enregistrer.
     */
    public function ensureAgeCategory(): self
    {
        if ($this->age_category_id || !$this->birth_date) {
            return $this;
        }

        $age = \Carbon\Carbon::parse($this->birth_date)->age;
        $cat = AgeCategory::where('min_age', '<=', $age)
            ->where('max_age', '>=', $age)
            ->orderByDesc('min_age')
            ->first();

        if ($cat) {
            $this->age_category_id = $cat->id;
            $this->save();
            $this->load('ageCategory');
        }

        return $this;
    }
public function age()
{
    return $this->hasOne(Person::class, 'user_id', 'id');
}


    public function dossier()
{
    return $this->hasOne(\App\Models\Dossier::class, 'person_id');
}

/**
 * Derniere reservation du proprietaire du compte (reservations.user_id).
 *
 * La table reservations porte user_id (et non person_id) : la quasi-totalite
 * des lignes n'a jamais de person_id, d'ou l'importance de cette cle.
 * Pour les membres de club / entreprise, plusieurs personnes partagent le
 * meme user_id : la reservation du compte leur est alors attribuee a chacune.
 */
public function lastReservation()
{
    return $this->hasOne(Reservation::class, 'user_id', 'user_id')->latestOfMany();
}
}
