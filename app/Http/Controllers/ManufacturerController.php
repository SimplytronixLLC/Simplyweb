<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManufacturerController extends Controller
{
    public function index()
    {
        $allManufacturers = DB::table('cached_products')
            ->select('manufacturer', DB::raw('COUNT(*) as total_products'))
            ->whereNotNull('manufacturer')
            ->where('manufacturer', '!=', '')
            ->groupBy('manufacturer')
            ->orderBy('manufacturer')
            ->get();

        $topManufacturers = $allManufacturers
            ->sortByDesc('total_products')
            ->take(24)
            ->values();

        return view('manufacturers.index', compact('topManufacturers', 'allManufacturers'));
    }

    public function show($name)
    {
        $manufacturer = urldecode($name);

        $products = DB::table('cached_products')
            ->whereRaw('LOWER(manufacturer) = ?', [strtolower($manufacturer)])
            ->paginate(20);

        $total = DB::table('cached_products')
            ->whereRaw('LOWER(manufacturer) = ?', [strtolower($manufacturer)])
            ->count();

        return view('manufacturers.show', compact('products', 'manufacturer', 'total'));
    }
}