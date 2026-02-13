<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MetodosController extends Controller
{
    public function index()
    {
        $metodos = DB::table('metodos_es')
            ->select(['id', 'descripcion'])
            ->orderBy('id')
            ->get();

        return response()->json($metodos);
    }
}
