<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        $defaults = [
            'wilaya_ar'      => 'البيض',
            'wilaya_fr'      => 'EL-BAYADH',
            'office_short'   => 'OPOW EL-BAYADH',
            'office_label_fr'=> 'Office du Parc Omnisports de la wilaya de EL-BAYADH',
            'contact_email'  => 'contact@opow-elbayadh.dz',
            'contact_phone'  => '049613680',
            'contact_place'  => 'ديوان المركب المتعدد الرياضات لولاية البيض',
            'app_name_ar'    => 'ديوان المركب المتعدد الرياضات البيض',
        ];

        foreach ($defaults as $key => $value) {
            DB::table('settings')->insert([
                'key'        => $key,
                'value'      => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
