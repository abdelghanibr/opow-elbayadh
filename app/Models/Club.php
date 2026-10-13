<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    /**
     * Documents du dossier, dans l'ordre d'affichage du formulaire.
     * Source unique de vérité partagée par le contrôleur et la vue.
     */
    public const DOSSIER_FILES = [
        'agrement'                => 'اعتماد النادي',
        'statut'                  => 'القانون الأساسي',
        'bureau_members'          => 'قائمة أعضاء المكتب',
        'coaches_certificates'    => 'شهادات المدربين',
        'federation_affiliation'  => 'شهادة الانخراط في الرابطة',
        'insurance_certificate'   => 'شهادة التأمين',
        'rules_book'              => 'الإتفاقية',
        'minutes_meeting'         => 'محضر الجمعية',
        'exploitation_request'    => 'طلب الاستغلال',
    ];

    /** Taille maximale d'un document, en kilo-octets (2 Mo). */
    public const DOSSIER_MAX_KB = 2048;

    /** Types MIME acceptés pour un document du dossier. */
    public const DOSSIER_MIMES = 'pdf,jpg,jpeg,png';

    protected $fillable = [
        'user_id',
        'activity_id',
        'nom',
        'numero_agrement',
        'date_expiration',
        'attachments',
        'entity_type' ,
        'company_type',
        'status',
        'validatio',
        'date_validation',
        'validated_by','etat'  ,'validated_at' 


 
    ];
public function validator()
{
    return $this->belongsTo(User::class, 'validated_by');
}
    protected $casts = [
        'attachments' => 'array', // <-- مهم جدًا
    ];

    /**
     * `attachments` est toujours décodé jusqu'à ce qu'on obtienne un tableau.
     *
     * L'historique contient deux écritures incompatibles : les anciennes lignes
     * stockent une liste plate (`["url"]`) et le contrôleur faisait
     * `json_encode()` alors que le cast `array` encode déjà. Ces lignes sont
     * donc imbriquées deux fois dans la base, et un `json_decode()` unique
     * renvoyait une chaîne au lieu d'un tableau indexé par nom de document.
     *
     * @return Attribute<array<int|string, mixed>, array<int|string, mixed>>
     */
    protected function attachments(): Attribute
    {
        return Attribute::make(
            get: static function ($value): array {
                $decoded = static::decodeAttachments($value);

                // Ancienne format : liste plate sans clé, on rattache le seul
                // document à la clé attendue par le formulaire.
                if ($decoded !== [] && array_is_list($decoded)) {
                    $decoded = ['agrement' => reset($decoded)];
                }

                return $decoded;
            },
        );
    }

    /**
     * Décode une valeur JSON imbriquée jusqu'à obtenir un tableau.
     */
    protected static function decodeAttachments($value): array
    {
        for ($depth = 0; $depth < 4; $depth++) {
            if (! is_string($value) || $value === '') {
                break;
            }

            $decoded = json_decode($value, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                break;
            }

            // Le décodage peut renvoyer une chaîne qui est encore un document
            // JSON valide : il faut continuer tant qu'on progresse.
            if (! is_array($decoded) && ! is_string($decoded)) {
                break;
            }

            $value = $decoded;
        }

        return is_array($value) ? $value : [];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // أعضاء النادي
    public function members()
    {
        return $this->hasMany(Person::class);
    }

    // النشاط الرياضي الموحد للنادي (une seule activite par club/company)
    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }
}
