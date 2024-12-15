<?php

namespace App\Http\Controllers;

use Exception;
use DataTables;
use App\Models\Category;
use App\Traits\AjaxResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CategoryRequest;

class CategoryController extends Controller
{
    use AjaxResponse;
    
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $categories =  Category::select('id', 'name')->withCount('products')->get();

            return Datatables::of($categories)
                ->addIndexColumn()   
                ->addColumn('action', function ($category) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $category->id . '" data-original-title="Edit" class="category-edit-btn btn btn-primary btn-sm">Edit</a>';
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $category->id . '" data-original-title="Delete" class="category-delete-btn btn btn-danger btn-sm ">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('category.index');
    }   

    public function store(CategoryRequest $request)
    {
        DB::beginTransaction();
        $update = $request->has('category_edit_id') && $request->category_edit_id != "";

        try {
            if ($update) {
                $message = 'Category Data update sucessfully';
                $category  = Category::find($request->category_edit_id);
            } else {
                $message = 'Category Data saved sucessfully';
                $category = new Category();
            }
            $category->fill($request->validated());
            $category->save();
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
            $category  = Category::find($request->categoryId);

            if ($category) {
                $category->delete();
            } else {
                return $this->ajaxResponse(false, 'Internal Server Error');
            }
            DB::commit();

            return $this->ajaxResponse(true, 'Category Data delete successfully');
        } catch (Exception $e) {
            DB::rollback();
            return $this->ajaxResponse(false, 'Internal Server Error');
        }
    }


    public function edit(Request $request)
    {
        $category  = Category::select('id', 'name', )->where('id', $request->categoryId)->first();
         
        if($category)
        {
            $data = [
                 'category' => $category,   
            ];
            return $this->ajaxResponse(true, 'category Data', $data);
        }
        else
        {
            return $this->ajaxResponse(false, 'Internal Server Error');
        } 
    }
}
