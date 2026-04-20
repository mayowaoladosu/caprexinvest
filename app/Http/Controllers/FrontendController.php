<?php

namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\InvestmentPlan;
use App\Models\Karat;
use App\Models\Language;
use App\Models\Material;
use App\Models\Order;
use App\Models\Page;
use App\Models\PageDetail;
use App\Models\Product;
use App\Models\ProductDetails;
use App\Models\Rating;
use App\Models\Subscriber;
use App\Traits\Frontend;
use App\Traits\Notify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FrontendController extends Controller
{
    use Frontend , Notify;

    public function page($slug = '/')
    {
        try {

            $connection = DB::connection()->getPdo();
            $selectedTheme = basicControl()->theme ?? 'light';
            $existingSlugs = collect([]);
            DB::table('pages')->select('slug')->get()->map(function ($item) use ($existingSlugs) {
                $existingSlugs->push($item->slug);
            });

            if (!in_array($slug, $existingSlugs->toArray())) {
                throw new \Exception('Not Found',404);
            }
            $pageDetails = PageDetail::with('page')
                ->whereHas('page', function ($query) use ($slug, $selectedTheme) {
                    $query->where(['slug' => $slug, 'template_name' => $selectedTheme]);
                })
                ->first();


            $pageSeo = [
                'page_title' => optional(optional($pageDetails)->page)->page_title ?? '',
                'meta_title' => optional(optional($pageDetails)->page)->meta_title,
                'meta_keywords' => implode(',', optional(optional($pageDetails)->page)->meta_keywords ?? []),
                'meta_description' => optional(optional($pageDetails)->page)->meta_description,
                'og_description' => optional(optional($pageDetails)->page)->og_description,
                'meta_robots' => optional(optional($pageDetails)->page)->meta_robots,
                'meta_image' => $pageDetails?->page
                    ? getFile($pageDetails->page->meta_image_driver, $pageDetails->page->meta_image)
                    : null,
                'breadcrumb_image' => $pageDetails?->page?->breadcrumb_status
                    ? getFile($pageDetails->page->breadcrumb_image_driver, $pageDetails->page->breadcrumb_image)
                    : null,
            ];


            $sectionsData = $this->getSectionsData($pageDetails->sections, $pageDetails->content, $selectedTheme);
            return view("themes.{$selectedTheme}.page", compact('sectionsData', 'pageSeo', 'slug'));

        } catch (\Exception $exception) {

            \Cache::forget('ConfigureSetting');
            if ($exception->getCode() == 404) {
                abort(404);
            }
            if ($exception->getCode() == 403) {
                abort(403);
            }
            if ($exception->getCode() == 401) {
                abort(401);
            }
            if ($exception->getCode() == 503) {
                return redirect()->route('maintenance');
            }
            if ($exception->getCode() == "42S02") {
                die($exception->getMessage());
            }
            if ($exception->getCode() == 1045) {
                die("Access denied. Please check your username and password.");
            }
            if ($exception->getCode() == 1044) {
                die("Access denied to the database. Ensure your user has the necessary permissions.");
            }
            if ($exception->getCode() == 1049) {
                die("Unknown database. Please verify the database name exists and is spelled correctly.");
            }
            if ($exception->getCode() == 2002) {
                die("Unable to connect to the MySQL server. Check the database host and ensure the server is running.");
            }

            return redirect()->route('instructionPage');
        }
    }


    public function sentContactInfo(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required | numeric',
            'email' => 'required | email',
            'message' => 'required',
        ]);
        $params = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'message' => $request->message
        ];
        $actionAdmin = [
            "name" => $request->name,
            "image" => Auth::user() ? getFile(Auth::user()->image_driver, Auth::user()->image) : getFile('driver', 'image'),
            "link" => '#',
            "icon" => "fas fa-ticket-alt text-white"
        ];
        $firebaseAction = '#';
        $this->adminMail('CONTACT_INFO', $params);
        $this->adminPushNotification('CONTACT_INFO', $params, $actionAdmin);
        $this->adminFirebasePushNotification('CONTACT_INFO', $params, $firebaseAction);

        return redirect()->back()->with('success', 'Your information has been sent.');
    }

    public function shop()
    {
        if (!hasEcommerce())
            abort(403);
        $page= Page::where('slug', 'shop')->first();
        $products = Product::selectRaw('max(price) as max_price')
                                    ->selectRaw('min(price) as min_price')
                                    ->selectRaw('COUNT(id) as total_products')
                                    ->get();

       $data['total_products'] = $products[0]->total_products;
        $data['max_price'] = $products[0]->max_price;
        $data['min_price'] = $products[0]->min_price;
        $data['pageSeo'] = [
            'page_title' => $page->page_title,
            'meta_title' => $page->meta_title,
            'meta_keywords' => isset($page->meta_keywords) && $page->meta_keywords ?implode(',', $page->meta_keywords ?? []):'',
            'meta_description' => $page->meta_description,
            'og_description' => $page->og_description,
            'meta_robots' => $page->meta_robots,
            'meta_image' => getFile($page->meta_image_driver, $page->meta_image),
            'breadcrumb_image' => $page->breadcrumb_status ?
                getFile($page->breadcrumb_image_driver, $page->breadcrumb_image) : null,
        ];
        $data['categories'] = Category::with(['products'])->where('status', 1)->orderByDesc('created_at')->get();
        $data['materials'] = Material::with(['products'])->where('status', 1)->orderByDesc('created_at')->get();
        $data['karats'] = Karat::with(['products'])->where('status', 1)->orderByDesc('created_at')->get();
        return view(template().'pages.shop',$data);
    }

    public function products(Request $request)
    {
        if (!hasEcommerce())
            return [];
        $products = Product::with(['details', 'wishlist', 'reviews','category'])
            ->where('is_published', 1)
            ->when(request()->has('material') && request()->material, function ($query) {
                $query->whereIn('material_id', request()->material);
            })
            ->when(request()->has('category') && request()->category, function ($query) {
                $query->whereIn('category_id', request()->category);
            })
            ->when(request()->has('karat') && request()->karat, function ($query) {
                $query->whereIn('karat_id', request()->karat);
            })
            ->when(request()->has('min_price') && request()->min_price, function ($query) {
                $query->where('price', '>=', request()->min_price);
            })
            ->when(request()->has('max_price') && request()->max_price, function ($query) {
                $query->where('price', '<=', request()->max_price);
            })

            ->when(request()->has('sorting') && request()->sorting == 'asc' || request()->sorting == 'desc', function ($query) {
                $query->orderBy(
                    ProductDetails::select('title')
                        ->whereColumn('product_details.product_id', 'products.id')
                        ->limit(1),
                    request()->sorting
                );
            })
            ->when(request()->has('sorting') && request()->sorting == 'low_to_high', function ($query) {
                $query->orderBy('price', 'asc');
            })
            ->paginate(12);

        return response()->json($products);
    }

    public function productDetails($slug)
    {
        if (!hasEcommerce())
            abort(403);
        $product = Product::with(['details', 'reviews.comment','images'])->where('slug',$slug)->firstOrFail();

        $data['pageSeo'] = [
            'page_title' => $product->page_title,
            'meta_title' => $product->meta_title,
            'meta_keywords' => isset($product->meta_keywords) && $product->meta_keywords ?implode(',', $product->meta_keywords ?? []):'',
            'meta_description' => $product->meta_description,
            'og_description' => $product->og_description,
            'meta_robots' => $product->meta_robots,
            'meta_image' => getFile($product->meta_image_driver, $product->meta_image),
            'breadcrumb_image' => $product->breadcrumb_image_status ?
                getFile($product->breadcrumb_image_driver, $product->breadcrumb_image) : null,
        ];




        $data['reviews'] = Rating::where('product_id', $product->id)->get();
        foreach ($data['reviews'] as $review) {
            $rating = $review->rating;
            $fullStars = floor($rating);
            $hasHalfStar = ($rating - $fullStars) >= 0.5;
            $stars = '';
            for ($i = 0; $i < $fullStars; $i++) {
                $stars .= '<li> <i class="fas fa-star"></i></li>';
            }
            if ($hasHalfStar) {
                $stars .= '<li><i class="fas fa-star-half-alt"></i></li>';
            }
            for ($i = 0; $i < 5 - $fullStars - ($hasHalfStar ? 1 : 0); $i++) {
                $stars .= '<li><i class="far fa-star"></i></li>';
            }

            $review->stars = $stars;
        }
        $rating = $product->reviews->avg('rating');
        $fullStars = floor($rating);
        $hasHalfStar = ($rating - $fullStars) >= 0.5;
        $stars = '';
        for ($i = 0; $i < $fullStars; $i++) {
            $stars .= ' <li><i class="fas fa-star"></i></li>';
        }
        if ($hasHalfStar) {
            $stars .= '<li><i class="fas fa-star-half-alt"></i></li>';
        }
        for ($i = 0; $i < 5 - $fullStars - ($hasHalfStar ? 1 : 0); $i++) {
            $stars .= '<li><i class="far fa-star"></i></li>';
        }
        $data['avg_rating'] = $stars;

        $relatedProducts = Product::with('details')->where('category_id', $product->category_id)->where('id', '!=', $product->id)->limit(4)->get();
        return view(template() . 'pages.product_details', compact('product',  'relatedProducts'), $data);

    }

    public function applyCoupon(Request $request)
    {
        $auth = auth()->user();

        if (!$auth) {
            return response(['errors' => ['coupon' => ['You have to authorize for applying a coupon.']]], 400);
        }

        $rules = [
            'coupon' => 'required|exists:coupons,coupon_code',
        ];

        $req = $request->all();
        $validator = Validator::make($req, $rules);


        if ($validator->fails()) {
            return response(['errors' => $validator->messages()], 400);
        }
        $couponCode = $req['coupon'];
        $coupon = Coupon::where('coupon_code', $couponCode)->first();

        $price = cartTotal(session('cart') ?? []);

        if (count(session('cart') ?? []) <= 0) {
            return response()->json(['errors' => ['coupon' => ['Please add to cart and try again.']]], 400);
        }


        if ($coupon->applicableProducts->first()) {
            $cartItems = collect(session('cart') ?? [])->keys()->toArray();
            $applicableProducts = $coupon->applicableProducts->pluck('product_id')->toArray();
            $matchedItems = array_intersect($cartItems, $applicableProducts);
            if (empty($matchedItems)) {
                return response()->json(['errors' => ['coupon' => ["You cannot apply this coupon for this order items"]]], 400);
            }
        }



        $orderCount = Order::where('coupon_code', $couponCode)
            ->where('user_id', $auth->id)
            ->count();

        if ($orderCount >= $coupon->maximum_order) {
            return response(['errors' => ['coupon' => ['Coupon usage limit reached.']]], 400);
        }

        if ($coupon->start_date > now()) {
            return response(['errors' => ['coupon' => ['Coupon has not started yet.']]], 400);
        }

        if ($coupon->end_date < now()) {
            return response(['errors' => ['coupon' => ['Coupon has expired.']]], 400);
        }

        if ($coupon->minimum_order_price > $price) {
            $minimumPrice = $coupon->minimum_order_price;
            return response(['errors' => ['coupon' => ["You have to purchase more then $minimumPrice for apply this coupon."]]], 400);
        }

        $discount = discountPrice($price, $req['coupon']);

        return response()->json($discount);

    }

    public function plans()
    {
        $assetType = request()->asset_type;
        $plans = InvestmentPlan::where(['status' => 1, 'soft_delete' => 0])
            ->when(in_array($assetType, ['gold', 'btc'], true), function ($query) use ($assetType) {
                $query->where('asset_type', $assetType);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view(template() . 'user.plan.index', compact('plans', 'assetType'));
    }

    public function subscribe(Request $request)
    {
        $rules = [
            'email' => 'required|email|max:255|unique:subscribers'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->with('error',$validator->errors()->first());
        }
        $data = new Subscriber();
        $data->email = $request->email;
        $data->save();

        $msg = [
            'email' => $data->email
        ];

        $action = [
            "link" => route('admin.subscriber.index'),
            "icon" => "fas fa-user text-white",
            'image' => auth()->user()? getFile(auth()->user()->image_driver,auth()->user()->image):getFile('local','image')
        ];

        $this->adminPushNotification('SUBSCRIBE_NEWSLETTER', $msg, $action);
        $this->adminMail('SUBSCRIBE_NEWSLETTER', [
            'email' => $data->email,
        ]);

        return redirect(url()->previous())->with('success', 'Subscribed Successfully');
    }

    public function language()
    {
        $locale = request()->language;

        app()->setLocale($locale);
        $lang = Language::where('short_name', $locale)->first();
        if ($lang){
            session()->put('lang', $locale);
        }
        return redirect()->back();
    }
}
