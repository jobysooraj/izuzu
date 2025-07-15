<?php
namespace Botble\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class EqhitController extends Controller
{
    public function searchFig(Request $request)
    {
        $query = $request->input('q');
        $results = collect([
            ['id' => '010', 'text' => 'Engine, Emission'],
            ['id' => '011', 'text' => 'Cylinder Head'],
        ])->filter(fn($item) => str_contains(strtolower($item['text']), strtolower($query)))->values();
            dd($results);
        return response()->json(['results' => $results]);
    }

    public function searchPno(Request $request)
    {
        $query = $request->input('q');
        return response()->json([
            'results' => collect(range(1000, 1010))->map(fn($pno) => [
                'id' => $pno,
                'text' => "PNO $pno"
            ])->filter(fn($item) => str_contains($item['text'], $query))->values()
        ]);
    }

    public function searchName(Request $request)
    {
        $query = $request->input('q');
        return response()->json([
            'results' => collect(['Filter', 'Brake Pad', 'Headlamp', 'Bumper'])->map(fn($name) => [
                'id' => $name,
                'text' => $name
            ])->filter(fn($item) => str_contains(strtolower($item['text']), strtolower($query)))->values()
        ]);
    }

    public function searchModel(Request $request)
    {
        $query = $request->input('q');
        return response()->json([
            'results' => collect(['Toyota Corolla', 'Maruti 800', 'Honda City'])->map(fn($model) => [
                'id' => $model,
                'text' => $model
            ])->filter(fn($item) => str_contains(strtolower($item['text']), strtolower($query)))->values()
        ]);
    }
}
