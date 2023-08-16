<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        if ($products->count() > 0) {
            $products->transform(function ($product) {
                $product->properties = json_decode($product->properties);
                return $product;
            });

            $data = [
                'status' => 200,
                'products' => $products
            ];
            return response()->json($data, 200);
        } else {
            $data = [
                'status' => 404,
                'response' => "No products found"
            ];
            return response()->json($data, 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:20',
            'description' => 'required|string|max:100',
            'price' => 'required|max:10',
            'image' => 'required|url|max:255',
            'properties' => 'required|json|max:200',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        } else {
            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'image' => $request->image,
                'properties' => $request->properties,
            ]);

            if($product) {
                return response()->json([
                'status' => 200,
                'message' => "Product added with success!"
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => "The product was not added, something went wrong"
                    ], 500);
            }
        }
    }

    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['status' => 404, 'message' => 'Product not found'], 404);
        }

        $product->properties = json_decode($product->properties);

        return response()->json(['status' => 200, 'product' => $product], 200);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['status' => 404, 'message' => 'Product not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:20',
            'description' => 'required|string|max:100',
            'price' => 'required|max:10',
            'image' => 'required|url|max:255',
            'properties' => 'required|json|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $request->image,
            'properties' => $request->properties,
        ]);

        return response()->json(['status' => 200, 'message' => 'Product updated successfully'], 200);
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['status' => 404, 'message' => 'Product not found'], 404);
        }

        $product->delete();

        return response()->json(['status' => 200, 'message' => 'Product deleted successfully'], 200);
    }
}
