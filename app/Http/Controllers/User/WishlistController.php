<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\WishList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        if (!basicControl()->ecommerce){
            abort(403);
        }

        $wishlists = Wishlist::query()->with(['product:id,slug,thumbnail_image,driver,price','product.details:title,id,product_id'])->where('user_id', Auth::id())->paginate(15);
        return view(template().'user.wishlist.index',compact('wishlists'));
    }
    public function addToWishlist(Request $request)
    {
        if (!basicControl()->ecommerce){
            return false;
        }

        $request->validate([
            'id' => 'required|exists:products,id',
        ]);
        $id = $request->id;
        $wishlist = Wishlist::where('user_id',auth()->user()->id)->where('product_id',$id)->first();

        if($wishlist){
            $wishlist->delete();
            $userWishlist = WishList::where('user_id',\auth()->id())->count('id');
            return [
                'success' => 1,
                'message' => 'Product remove to wishlist',
                'count' => $userWishlist,
            ];
        }else{
            $wishlist = new Wishlist();
            $wishlist->user_id = auth()->user()->id;
            $wishlist->product_id = $id;
            $wishlist->save();
            $userWishlist = WishList::where('user_id',\auth()->id())->count('id');
            return [
                'success' => 2,
                'message' => 'Product added to wishlist',
                'count' => $userWishlist,
            ];
        }

    }

    public function removeFromWishlist(Request $request)
    {
        if (!basicControl()->ecommerce){
            return false;
        }

        $request->validate([
            'id' => 'required|exists:wish_lists,id',
        ]);

        $id = $request->id;
        $wishlist = Wishlist::where('id',$id)->first();

        if($wishlist){
            $wishlist->delete();
        }else{
            return false;
        }

        return  true;
    }
}
