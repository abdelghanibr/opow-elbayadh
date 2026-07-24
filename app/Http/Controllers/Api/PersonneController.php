<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Person;
use Illuminate\Support\Facades\DB;

class PersonneController extends Controller
{


public function index()
{
    $persons = Person::orderByDesc('id')->get();

    return response()->json([
        'status' => 'success',
        'count'  => $persons->count(),
        'data'   => $persons
    ]);
}

}
