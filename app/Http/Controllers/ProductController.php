<?php

namespace App\Http\Controllers;

use Exception;
use DataTables;
use App\Models\Product;
use App\Models\Category;
use App\Traits\AjaxResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ProductRequest;

class ProductController extends Controller
{
    use AjaxResponse;
    
    public function index(Request $request)
    {
        $categories = Category::select('id', 'name')->get();
        if ($request->ajax()) {
            $products = Product::with('category:id,name')->select('id', 'name', 'category_id', 'price')->orderBy('id', 'desc');

            return Datatables::of($products)
                ->addIndexColumn()  
                ->addColumn('category_name', function ($product) {
                    return $product->category ? $product->category->name : '-';
                })    
                ->addColumn('action', function ($product) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $product->id . '" data-original-title="Edit" class="product-edit-btn btn btn-primary btn-sm">Edit</a>';
                    $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $product->id . '" data-original-title="Delete" class="product-delete-btn btn btn-danger btn-sm ">Delete</a>';
                    return $btn;
                })

                ->rawColumns(['category_name', 'action'])
                ->make(true);
        }
        return view('product.index', compact('categories'));
    }   

    public function store(ProductRequest $request)
    {
        DB::beginTransaction();
        $update = $request->has('product_edit_id') && $request->product_edit_id != "";

        try {
            if ($update) {
                $message = 'Product Data update sucessfully';
                $product  = Product::find($request->product_edit_id);
            } else {
                $message = 'Product Data saved sucessfully';
                $product = new Product();
            }
            $product->fill($request->validated());
            $product->save();
            DB::commit();
            return $this->ajaxResponse(true, $message);
        } catch (Exception $e) {
            DB::rollback();
            return $this->ajaxResponse(false, 'Internal Server Error');
        }
    }

    public function delete(Request $request)
    {
        DB::beginTransaction();
        try {
            $product  = Product::find($request->productId);

            if ($product) {
                $product->delete();
            } else {
                return $this->ajaxResponse(false, 'Internal Server Error');
            }
            DB::commit();

            return $this->ajaxResponse(true, 'Product Data delete successfully');
        } catch (Exception $e) {
            DB::rollback();
            return $this->ajaxResponse(false, 'Internal Server Error');
        }
    }


    public function edit(Request $request)
    {
        $product  = Product::with('category:id,name')->select('id','category_id', 'name', 'price')->where('id', $request->productId)->first();
         
        if($product)
        {
            $data = [
                 'product' => $product,   
            ];
            return $this->ajaxResponse(true, 'product Data', $data);
        }
        else
        {
            return $this->ajaxResponse(false, 'Internal Server Error');
        } 
    }
}
