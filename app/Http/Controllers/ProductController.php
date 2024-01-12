<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    //index
    public function index(Request $request)
    {
        // $products = \App\Models\Product::paginate(5);
        $products = DB::table('products')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->when($request->input('name'), function ($query, $name) {
                return $query->where('products.name', 'like', '%' . $name . '%');
            })
            ->select('products.*', 'categories.name as category_name') // Add other columns if needed
            ->paginate(5);
        return view('pages.product.index', compact('products'));
    }

    //create
    public function create()
    {
        $categories = \App\Models\Category::all();
        return view('pages.product.create', compact('categories'));
    }

    //store
    public function store(Request $request)
    {
        $filename = time() . '.' . $request->image->extension();
        $request->image->storeAs('public/products', $filename);
        // $data = $request->all();

        $product = new \App\Models\Product;
        $product->name = $request->name;
        $product->price = (int) $request->price;
        $product->stock = (int) $request->stock;
        $product->category_id = $request->category_id;
        $product->image = $filename;
        $product->save();

        return redirect()->route('product.index');
    }
}
