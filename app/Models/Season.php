<?php





namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    protected $fillable = [
        'name',
        'date_debut',
        'date_fin',
        'type_season',
    ];

    public const TYPES = [
        'session'   => 'حصة',
       
        'monthly'   => 'شهري',
    
 
        'season'    => 'موسم',
        'ticket'    => 'تذكرة',
    ];
}
