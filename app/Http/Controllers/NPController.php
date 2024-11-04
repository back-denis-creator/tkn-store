<?php

namespace App\Http\Controllers;

use App\Overrides\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NPController extends Controller
{
    public function cities(Request $request)
    {
        $cities = [];
        if($request->has('search')) {
            $adr = new Address;
            $cities = $adr->getCities($request->search);
        }
        return back()->with('cities', $cities['result'] ?: []);
    }

    public function warehouses(Request $request)
    {
        $warehouses = [];
        $adr = new Address;
        if($request->has('city_ref') && $request->has('search')) {
            $warehouses = $adr->getWarehouses($request->city_ref, false, $request->search);
        } else if($request->has('city_ref')) {
            $warehouses = $adr->getWarehouses($request->city_ref, false);
        }
        Log::info(['$warehouses' => $warehouses]);
        return back()->with('warehouses', $warehouses['result'] ?: []);
    }
}