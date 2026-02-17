<?php

namespace App\Http\Controllers;

use App\Models\Oferta;
use App\Models\Rubro;
use Illuminate\Http\Request;
use Carbon\Carbon; 

class OfertaController extends Controller
{
    /**
     * Muestra las ofertas aprobadas y vigentes, clasificadas por rubro.
     */
    public function index()
    {
        $hoy = Carbon::now()->format('Y-m-d');

        // Obtenemos los rubros que tengan al menos una oferta activa
        
        $rubros = Rubro::with(['ofertas' => function ($query) use ($hoy) {
            $query->where('estado', 'aprobada')
                  ->where('fecha_inicio', '<=', $hoy) 
                  ->where('fecha_fin', '>=', $hoy)    
                  ->where(function ($q) {
                      // Que no tengan límite o que el límite no se haya alcanzado
                      $q->whereNull('cantidad_limite')
                        ->orWhereRaw('id IN (SELECT oferta_id FROM cupones GROUP BY oferta_id HAVING count(*) < ofertas.cantidad_limite)');
                  });
        }])->get();

        //Filtramos rubros vacíos si no quieres mostrarlos
        $rubrosConOfertas = $rubros->filter(function ($rubro) {
            return $rubro->ofertas->isNotEmpty();
        });

        // Retornamos respuesta JSON 
        return response()->json($rubrosConOfertas->values());
    }

    /**
     * Muestra el detalle de una oferta específica
     */
    public function show($id)
    {
        $oferta = Oferta::with('empresa')->find($id);

        if (!$oferta) {
            return response()->json(['message' => 'Oferta no encontrada'], 404);
        }

        return response()->json($oferta);
    }
}