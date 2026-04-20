<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Karat;
use App\Models\Language;
use App\Models\Material;
use App\Models\Product;
use App\Models\ProductDetails;
use App\Models\ProductImage;
use App\Models\SubCategory;
use App\Traits\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    use Upload;
    public function index()
    {
        $languages = Language::where('status',1)->get();
        $totalProducts = Product::where('status', 'Available')->count();
        return view('admin.product.index', compact('totalProducts','languages'));
    }

    public function productList(Request $request)
    {
        $search = @$request->search['value'] ?? null;
        $filterProductName = $request->filterProductName;
        $filterStatus = $request->filterStatus;
        $filterDate = explode('-', $request->filterDate);
        $startDate = $filterDate[0];
        $endDate = isset($filterDate[1]) ? trim($filterDate[1]) : null;
        $languages = Language::where('status',1)->get();
        $defaultLanguage = Language::where('default_status',1)->first();
        $allProductsId = Product::query()->select('id')->get()->pluck('id')->toArray();

        $productDetails = DB::table('product_details')
            ->whereIn('product_id',  $allProductsId)
            ->get()
            ->groupBy('product_id')
            ->map(function ($details) {
                return $details->pluck('language_id')->flip()->all();
            })
            ->toArray();

        $products = Product::query()->with(['category:id,name','subcategory:id,name','details:id,title,product_id,language_id'])
            ->whereHas('details', function ($query) use ($defaultLanguage) {
                $query->where('language_id', $defaultLanguage->id);
            })
            ->orderBy('id', 'DESC')
            ->when(!empty($search), function ($query) use ($search) {
                return $query->whereHas('details', function ($query) use ($search) {
                    return $query->where('title', 'LIKE', "%{$search}%");
                });
            })
            ->when(!empty($filterProductName), function ($query) use ($filterProductName) {
                return $query->whereHas('details', function ($query) use ($filterProductName) {
                    return $query->where('title', 'LIKE', "%{$filterProductName}%");
                });
            })
            ->when($filterStatus !== null && $filterStatus !== "all", function ($query) use ($filterStatus) {
                return $query->where('status', $filterStatus);
            })
            ->when($startDate && !$endDate, function ($query) use ($startDate) {
                $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate);
                return $query->whereDate('created_at', $startDate);
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate);
                $endDate = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate);
                return $query->whereBetween('created_at', [$startDate, $endDate]);
            });

        return DataTables::of($products)
            ->addColumn('checkbox', function ($item) {
                return ' <input type="checkbox" id="chk-' . $item->id . '"
                                       class="form-check-input row-tic tic-check" name="check" value="' . $item->id . '"
                                       data-id="' . $item->id . '">';
            })
            ->addColumn('product', function ($item) {
                $img = getFile($item->driver,$item->thumbnail_image);
                return '<div class="d-flex align-items-center" href="javascript:void(0)">
                        <div class="flex-shrink-0">
                          <img class="avatar avatar-lg" src="'.$img.'" alt="Image">
                        </div>
                        <div class="flex-grow-1 ms-3">
                          <h5 class="text-inherit mb-0">'.Str::words($item->details?->title, 6).'</h5>
                        </div>
                  </div>';
            })
            ->addColumn('category', function ($item) {
                return $item->category->name ?? 'no-category';
            })
            ->addColumn('subcategory', function ($item) {
                return $item->subcategory->name ?? 'no-subcategory';
            })
            ->addColumn('price', function ($item) {
                return currencyPosition($item->price);
            })
            ->addColumn('availability', function ($item) {
                $class = $item->status == 'Available' ? 'success' : 'warning';
                return "<span class='badge bg-soft-$class text-$class'><span class='legend-indicator bg-$class'></span> " . $item->status . "</span>";
            })
            ->addColumn('status', function ($item) {
                $class = $item->is_published == 1 ? 'info' : 'danger';
                return "<span class='badge bg-soft-$class text-$class'><span class='legend-indicator bg-$class'></span> " . ($item->is_published == 1?'Published':'Unpublished'). "</span>";
            })
            ->addColumn('language', function ($item)use ($languages,$productDetails) {
                $lang = '';
                foreach($languages as $language){
                    $lang .= ' <a href="'. route('admin.product.edit', [$item->id, $language->id]) .'"
                                          class="btn btn-white btn-icon btn-sm flag-btn"
                                          >
                                           <i class="bi '.(isset($productDetails[$item->id][$language->id]) ? 'bi-check2' : 'bi-pencil').'"></i>
                                       </a>';
                }

                return $lang;
            })
            ->addColumn('action', function ($item) {
                $editUrl = route('admin.product.edit', [$item->id,optional($item->details)->language_id]);
                $seoUrl = route('admin.product.seo', $item->id);
                $deleteUrl = route('admin.product.destroy', $item->id);
                return '<div class="btn-group" role="group">
                        <a class="btn btn-white btn-sm" id="updatedVariantsComObjValues" href="'.$editUrl.'"
                                       data-product-id="'.$item->id.'">
                            <i class="bi-pencil-fill me-1"></i>' . trans("Edit") . '
                        </a>
                        <div class="btn-group">
                            <button type="button" class="btn btn-white btn-icon btn-sm dropdown-toggle dropdown-toggle-empty" id="productsEditDropdown1" data-bs-toggle="dropdown" aria-expanded="false"></button>

                            <div class="dropdown-menu dropdown-menu-end mt-1" aria-labelledby="productsEditDropdown1">
                                <a class="dropdown-item"
                                   href="'.$seoUrl.'">
                                    <i class="fa-light fa-magnifying-glass dropdown-item-icon"></i>' . trans("SEO") . '
                                </a>
                                <a class="dropdown-item deleteBtn text-danger"
                                   href="javascript:void(0)"
                                   data-route="' . $deleteUrl . '"
                                   data-item-name="' . $item->title . '"
                                   data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="bi-trash dropdown-item-icon text-danger"></i> ' . trans("Delete") . '
                                </a>
                            </div>
                        </div>
                    </div>';
            })
            ->rawColumns(['checkbox', 'product', 'category', 'subcategory', 'price','availability', 'status','language', 'action'])
            ->make(true);

    }

    public function create()
    {
        $data['language'] = Language::where('default_status',1)->firstOrFail();
        $data['categories'] = Category::select('id', 'name','status')->get();
        $data['karats'] = Karat::select('id', 'karat')->where('status',true)->get();
        $data['materials'] = Material::select('id', 'material')->where('status',true)->get();
        return view('admin.product.create', $data);
    }

    public function store(Request $request)
    {

        $request->validate([
            'title' => 'required|string|max:250',
            'price' => 'required|numeric|min:1',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:sub_categories,id',
            'quantity' => 'required|numeric',
            'karat' => 'nullable|exists:karats,id',
            'material' => 'required|exists:materials,id',
            'description' => 'required|string|max:8000',
            'status' => 'required|in:Available,Stock Out',
            'is_published' => 'required|in:1,0',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:3072',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:3072',
            'breadcrumb_image' => 'nullable|image|mimes:jpeg,png,jpg|max:3072',
            'breadcrumb_status' => 'required|in:0,1'
        ]);

        DB::beginTransaction();

        try {
            $slug = $this->generateUniqueSlug($request->title);

            if ($request->hasFile('thumbnail')) {
                $thumbnail = $this->fileUpload($request->thumbnail,config('filelocation.product.path'),null,null,'webp',90);
            }

            if ($request->hasFile('breadcrumb_image')) {
                $breadcrumb = $this->fileUpload($request->breadcrumb_image,config('filelocation.product.path'),null,null,'webp',90);
            }


            $karat = Karat::where('id',$request->karat)->first();
            $material  = Material::where('id',$request->material)->first();

            $product = new Product();
            $product->price = $request->price;
            $product->slug = $slug;
            $product->category_id = $request->category_id;
            $product->subcategory_id = $request->subcategory_id;
            $product->quantity = $request->quantity;
            $product->karat = $karat?$karat->karat:null;
            $product->karat_id = $karat?$karat->id:null;
            $product->material = $material?$material->material:null;
            $product->material_id = $material?$material->id:null;
            $product->status = $request->status;
            $product->is_published = $request->is_published;
            $product->thumbnail_image = $thumbnail['path']??null;
            $product->driver = $thumbnail['driver']??null;
            $product->breadcrumb_image = $breadcrumb['path']??null;
            $product->breadcrumb_image_driver = $breadcrumb['driver']??null;
            $product->breadcrumb_image_status = $request->breadcrumb_status;
            $product->save();

            $details = new ProductDetails();
            $details->title = $request->title;
            $details->short_description = $request->short_description;
            $details->description = $request->description;
            $details->language_id = $request->language_id;
            $details->product_id = $product->id;
            $details->save();

            if (request()->hasFile('images')) {
                foreach ($request->images as  $file) {
                    $uploadImage = $this->fileUpload($file,config('filelocation.product.path'),null,null,'webp',90);
                    $image  = new ProductImage();
                    $image->product_id = $product->id;
                    $image->image = $uploadImage['path']??null;
                    $image->driver = $uploadImage['driver']??null;
                    $image->save();
                }
            }
            DB::commit();
            return redirect()->route('admin.products')->with('success', 'Product created successfully.');
        }catch (\Exception $exception){
            DB::rollBack();
            return back()->with('error', $exception->getMessage());
        }


    }


    public function edit($id,$language_id)
    {
        $product = Product::with(['details' => function ($query) use ($language_id) {
            $query->where('language_id',$language_id);
        },'ProductKarat','ProductMaterial'])->findOrFail($id);

        $data['categories'] = Category::select('id', 'name','status')->get();
        $data['karats'] = Karat::select('id', 'karat')->where('status',true)->get();
        $data['materials'] = Material::select('id', 'material')->where('status',true)->get();
        $data['productImages'] = $product->images->map(function ($image) {
            return [
                "id" => $image->id,
                "imageName" => $image->image,
                "image" => getFile($image->driver, $image->image)
            ];
        });

        return view('admin.product.edit', compact('product', 'language_id'),$data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:250',
            'price' => 'required|numeric|min:1',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:sub_categories,id',
            'quantity' => 'required|numeric',
            'karat' => 'nullable|exists:karats,id',
            'material' => 'required|exists:materials,id',
            'description' => 'required|string|max:5000',
            'status' => 'required|in:Available,Stock Out',
            'is_published' => 'required|in:1,0',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:3072',
            'breadcrumb_image' => 'nullable|image|mimes:jpeg,png,jpg|max:3072',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:3072',
            'breadcrumb_status' => 'required|in:0,1'
        ]);

        DB::beginTransaction();

        $language = $request->language_id;
        $product = Product::with(['details' => function ($query) use ($language) {
            $query->where('language_id',$language);
        }])->findOrFail($id);
        try {
            $slug = $this->generateUniqueSlug($request->title);

            if ($request->hasFile('thumbnail')) {
                $thumbnail = $this->fileUpload($request->thumbnail,config('filelocation.product.path'),null,null,'webp',90,$product->thumbnail_image,$product->driver);
            }

            if ($request->hasFile('breadcrumb_image')) {
                $breadcrumb = $this->fileUpload($request->breadcrumb_image,config('filelocation.product.path'),null,null,'webp',90,$product->breadcrumb_image,$product->breadcrumb_image_driver);
            }

            $karat = Karat::where('id',$request->karat)->first();
            $material  = Material::where('id',$request->material)->first();

            $product->price = $request->price;
            $product->slug = $slug;
            $product->category_id = $request->category_id;
            $product->subcategory_id = $request->subcategory_id;
            $product->karat = $karat?$karat->karat:null;
            $product->karat_id = $karat?$karat->id:null;
            $product->material = $material?$material->material:null;
            $product->material_id = $material?$material->id:null;
            $product->quantity = $request->quantity;
            $product->status = $request->status;
            $product->is_published = $request->is_published;
            $product->thumbnail_image = $thumbnail['path']??$product->thumbnail_image;
            $product->driver = $thumbnail['driver']??$product->driver;
            $product->breadcrumb_image = $breadcrumb['path']??$product->breadcrumb_image;
            $product->breadcrumb_image_driver = $breadcrumb['driver']??$product->breadcrumb_image_driver;
            $product->breadcrumb_image_status = $request->breadcrumb_status;
            $product->save();

            $product->details()->updateOrCreate(
                ['language_id' => $request->language_id], // Matching condition
                [
                    'title' => $request->title,
                    'description' => $request->description,
                    'short_description' => $request->short_description,
                    'product_id' => $product->id
                ]
            );

            $old_menu_images = $request->old ?? [];
            $dbImages = ProductImage::where('product_id', $id)->whereNotIn('id', $old_menu_images)->get();

            foreach ($dbImages as $dbImage) {
                $this->fileDelete($dbImage->driver, $dbImage->image);
                $dbImage->delete();
            }

            if ($request->has('images')) {

                foreach ($request->images as $key => $image) {
                    $images = $this->fileUpload($image, config('filelocation.product.path'), null, null, 'webp', 80);
                    try {
                        if ($images) {
                            $product->images()->create([
                                'image' => $images['path'],
                                'driver' => $images['driver']
                            ]);
                        }
                    } catch (\Exception $exp) {
                        return back()->with('error', 'Image could not be uploaded.');
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.products')->with('success', 'Product created successfully.');
        }catch (\Exception $exception){
            DB::rollBack();
            return back()->with('error', $exception->getMessage());
        }
    }


    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $product = Product::with(['images'])->where('id', $id)->firstOr(function () {
                throw new \Exception('No Product data found.');
            });

            $this->fileDelete($product->driver, $product->thumbnail_image);
            foreach ($product->images as $image) {
                $this->fileDelete($image->driver, $image->image);
            }
            $product->images()->delete();
            $product->details()->delete();
            $product->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }



    public function getSubcategory(Request $request)
    {
        $subcategories = SubCategory::where('category_id', $request->category_id)->get();
        return response()->json([
            'status' => true,
            'data' => $subcategories
        ]);
    }

    private function generateUniqueSlug($title, $id = null)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        // Determine the correct model to check the slug against
        $query = Product::where('slug', $slug);


        // Check if the slug exists and append a number if it does
        while ($query->exists()) {
            $slug = $originalSlug . '-' . $count++;
            $query = Product::where('slug', $slug);

            if ($id !== null) {
                $query->where('id', '!=', $id);
            }
        }

        return $slug;
    }

    public function productSeo($id)
    {
        try {
            $product = Product::where('id', $id)->firstOr(function () {
                throw new \Exception('No Product data found.');
            });
            return view('admin.product.seo', compact('product'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function productSeoUpdate(Request $request, $id)
    {
        $request->validate([
            'page_title' => 'required|string|min:3|max:100',
            'meta_title' => 'required|string|min:3|max:100',
            'meta_keywords' => 'required|array',
            'meta_keywords.*' => 'required|string|min:1|max:300',
            'meta_description' => 'required|string|min:1|max:300',
            'meta_image' => 'sometimes|required|mimes:jpeg,png,jpeg|max:2048',
            'og_description' => 'nullable|string|min:1|max:500',
            'meta_robots' => 'nullable|array',
            'meta_robots.*' => 'nullable|string|min:1|max:255',
        ]);

        try {
            $product = Product::where('id', $id)->firstOr(function () {
                throw new \Exception('No Product data found.');
            });
            if ($request->hasFile('meta_image')) {
                try {
                    $image = $this->fileUpload($request->meta_image, config('filelocation.pageSeo.path'), null, null, 'webp', 99, $product->meta_image_Driver, $product->meta_image);
                    if ($image) {
                        $pageSEOImage = $image['path'];
                        $pageSEODriver = $image['driver'] ?? 'local';
                    }
                } catch (\Exception $exp) {
                    return back()->with('error', 'Meta image could not be uploaded.');
                }
            }

            if ($request->meta_robots) {
                $meta_robots = implode(",", $request->meta_robots);
            }

            $product->page_title = $request->page_title;
            $product->meta_title = $request->meta_title;
            $product->meta_keywords = $request->meta_keywords;
            $product->meta_description = $request->meta_description;
            $product->og_description = $request->og_description;
            $product->meta_robots = $meta_robots ?? null;
            $product->meta_image = $pageSEOImage ?? $product->meta_image;
            $product->meta_image_Driver = $pageSEODriver ?? $product->meta_image_driver;
            $product->save();
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Seo has been updated.');
    }


    public function productDestroyMultiple(Request $request)
    {
        if ($request->strIds == null) {
            session()->flash('error', 'You do not select any Product.');
            return response()->json(['error' => 1]);
        } else {
            DB::transaction(function () use ($request) {
                Product::with(['details','images'])->whereIn('id', $request->strIds)->get()->map(function ($product){
                    $this->fileDelete($product->driver, $product->thumbnail_image);
                    foreach ($product->images as $image) {
                        $this->fileDelete($image->driver, $image->image);
                    }
                    $product->images()->delete();
                    $product->details()->delete();
                    $product->delete();
                });
            });
            session()->flash('success', 'Product has been deleted successfully');
            return response()->json(['success' => 1]);
        }
    }

    public function filterProduct(Request $request)
    {
        $category_id = $request->category_id;
        $subCategoryId = $request->subcategory_id;
        $products = Product::with(['details','category','subcategory'])
            ->when($category_id, function ($query) use ($category_id) {
                return $query->where('category_id', $category_id);
            })
            ->when($subCategoryId, function ($query) use ($subCategoryId) {
                return $query->where('subcategory_id', $subCategoryId);
            })
            ->get();
        return response()->json(['products' => $products]);
    }

    public function materials()
    {
        $materials = Material::get();
        return view('admin.product.material.index',compact('materials'));
    }

    public function materialStore(Request $request)
    {
        $request->validate([
            'material_name' => 'required|string|max:100',
        ]);
         $material = new Material();
         $material->material = $request->material_name;
         $material->status = $request->status?true:false;
         $material->save();

         return back()->with('success', 'Material created successfully.');
    }

    public function materialUpdate(Request $request,$id)
    {
        $request->validate([
            'material_name' => 'required|string|max:100',
        ]);
        $material = Material::findOrFail($id);
        $material->material = $request->material_name;
        $material->status = $request->status?true:false;
        $material->save();
        return back()->with('success', 'Material updated successfully.');
    }

    public function materialDestroy($id)
    {
        Material::findOrFail($id)->delete();
        return back()->with('success', 'Material deleted successfully.');
    }


    public function karats()
    {
        $karats = Karat::get();
        return view('admin.product.karat.index',compact('karats'));
    }

    public function karatStore(Request $request)
    {
        $request->validate([
            'karat' => 'numeric|min:1|max:24',
            'price' => 'numeric'
        ]);
        $karat = new Karat();
        $karat->karat = $request->karat;
        $karat->price = $request->price;
        $karat->status = $request->status?true:false;
        $karat->save();

        return back()->with('success', 'Karat created successfully.');
    }

    public function karatUpdate(Request $request,$id)
    {
        $request->validate([
            'karat' => 'numeric|min:1|max:24',
            'price' => 'numeric'
        ]);

        $karat = Karat::findOrFail($id);
        $karat->karat = $request->karat;
        $karat->price = $request->price;
        $karat->status = $request->status?true:false;
        $karat->save();
        return back()->with('success', 'Karat updated successfully.');
    }

    public function karatDestroy($id)
    {
        Karat::findOrFail($id)->delete();
        return back()->with('success', 'Karat deleted successfully.');
    }
}
