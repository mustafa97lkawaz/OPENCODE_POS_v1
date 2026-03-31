<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Products;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    public function index()
    {
        $products = Products::where('Status', 'مفعل')->get();

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }
}