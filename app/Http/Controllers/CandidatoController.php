<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidato;
use App\Http\Resources\CandidatosCollection;
use App\Http\Resources\CandidatoResource;
use App\Http\Resources\CandidatoByIdResource;
use Illuminate\Support\Facades\Auth;

class CandidatoController extends Controller
{
    public function createLead(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'manager') {
            // Validar y guardar los datos del candidato
            $candidato = new Candidato;
            $candidato->name = $request->input('name');
            $candidato->source = $request->input('source');
            $candidato->owner = $request->input('owner');
            $candidato->created_by = Auth::id(); // Asignar el ID del usuario autenticado como propietario
            $candidato->save();

            return response()->json(
                [
                    'meta' => [
                        'success' => true,
                        'errors' => []
                    ],
                    'data' => $candidato
                ],200
                
                //['message' => 'Candidato creado con éxito']
            );
        }else{
            return response()->json(['message' => 'Permission denied'], 403);
        }
    }

    public function leads()
    {
        // Obtener y retornar todos los candidatos
        //$candidatos = Candidato::all();

        //return CandidatosCollection::collection($candidatos);
        $user = Auth::user();
        //print_r($user['role']);
        if ($user->role === 'manager') {
            $candidatos = Candidato::get();
        } else if ($user->role === 'agent') {
            $candidatos = Candidato::where('owner', $user->id)->get();
        } else {
            // Manejar otros roles o situaciones no válidas según tus requerimientos
            return response()->json(['message' => 'Permission denied'], 403);
        }
        return new CandidatosCollection(
            $candidatos
            //(Candidato::all())
        );

        //return response()->json($candidatos);

        /*return new PostCollection(
            (Post::latest()->paginate())
        );*/
    }

    public function lead($id)
    {
        $candidato = Candidato::find($id);
        if(!$candidato){
            return response()->json(['meta' => [
                'success' => false,
                'errors' => [
                    "No Lead found"
                ]
            ]
            ], 404);
        }
        return new CandidatoByIdResource($candidato);
        // Obtener y retornar todos los candidatos
        //$candidatos = Candidato::all();
        //return response()->json($candidatos);
    }
}