<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ContentDetails;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {

        $rules = [
            'product_id' => ['required', Rule::exists('products', 'id')],
            'quantity' => ['required', 'integer', 'min:1'],
        ];

       $validator = Validator::make($request->all(),$rules);

       if ($validator->fails()) {
           return response()->json(collect($validator->errors())->collapse());
       }


        $product = Product::where('id',$request->product_id)->first();
        if (!$product){
            return response()->json(['message' => 'Product not found'], 404);
        }
        $id = $request->product_id;
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $request->quantity ?? 1;
        }else{
            $cart[$id] = [
                "id" => $id,
                "image" => getFile($product->driver, $product->thumbnail_image),
                "name" => optional($product->details)->title,
                "quantity" => $request->quantity+0 ?? 1,
                'product_quantity' => $product->quantity+0,
                "price" => $product->price+0,
            ];
        }

        session()->put('cart', $cart);


        if (session('coupon')){
            $discount = discountPrice(cartTotal($cart),session('coupon'));
        }

        return response()->json([
            'success' => 'Item added successfully to the cart',
            'data' => $cart,
            'discount' => isset($discount)?$discount:null,
        ]);
    }

    public function remove(Request $request)
    {
      $rule = [
          'product_id' => ['required', Rule::exists('products', 'id')],
      ];

      $validator = Validator::make($request->all(),$rule);
      if ($validator->fails()) {
          return response()->json(collect($validator->errors())->collapse());
      }

        $cart = session()->get('cart');
        if (isset($cart[$request->product_id])) {
            unset($cart[$request->product_id]);
            session()->put('cart', $cart);
            if (session('coupon')){
                $discount = discountPrice(cartTotal($cart),session('coupon'));
            }
        }

        return response()->json([
            'success' => 'Card Remove successfully',
            'data' => $cart,
            'discount' => isset($discount)?$discount:null,
        ]);
    }


    public function updateQuantity(Request $request)
    {
        $request->validate([
            'quantity' => ['required'],
            'product_id' => ['required', Rule::exists('products', 'id')],
        ]);
        $productId = $request->product_id;
        $quantity = $request->quantity;
        $cart = session()->get('cart', []);
        if (isset($cart[$productId])) {
            if ($quantity > 0) {
                $cart[$productId]['quantity'] = $quantity;
            } else {
                unset($cart[$productId]);
            }
        }
        session()->put('cart', $cart);

        if (session('coupon')){
            $discount = discountPrice(cartTotal($cart),session('coupon'));
        }
        return response()->json([
            'success' => 'Cart updated successfully',
            'data' => $cart,
            'discount' => isset($discount)?$discount:null,
        ]);
    }

    public function cart()
    {

        if (!basicControl()->ecommerce){
            abort(403);
        }

        if (count(session('cart')??[]) <= 0){
            return redirect()->route('page')->with('error','Cart is empty');
        }



        $page = Page::where('slug', 'view-cart')->first();
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
        return view(template().'pages.cart_view',$data);
    }
}
