<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Image;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // SELECT * FROM images INNER JOIN products ON (images.product_id =products.id)
         return Image::join("products","images.product_id","=","products.id")
            ->get();
        // return Product::orderBy('id', 'DESC')->get();
        // return Product::find()->get();
       
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $path="http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        if($request->hasFile('image')){
            $file = $request->file('image');
            $name = time().'.jpg';
            $file->move(public_path('//images//'), $name);
            $product = new Product;
            $product->name = $request->name;
            $product->description = $request->description;
            $product->save();
            $id= $product->id;
            $product->where('id', $id)->get();
            $image = new Image;
            $image->path = '/images/'.$name;
            $image->product_id = $product->id;
            $image->save();
            return response()->json(['message'=>'Producto añadido Satisfactoriamente', 'product'=>$product, 'image'=>$image, $path ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Product::where('id', $id)->get();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {     
        $product = Product::find($id);
        $product->name = $request->name;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->save();
        if($request->hasFile('image')){
            $file = $request->file('image');
            $name = time().'.jpg';
            $file->move(public_path('//images//'), $name);
            $image = Image::find($id);
            $image->path = '/images/'.$name;
            $image->save();
            return response()->json(['message'=>'Producto Modificado Satisfactoriamente', $product ,'image'=>$image,]);
        }
        return response()->json(['message'=>'Producto Modificado Satisfactoriamente', $product]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        $product->delete();
        return response()->json(['message'=>'Producto Eliminado Satisfactoriamente']);
    }
}
