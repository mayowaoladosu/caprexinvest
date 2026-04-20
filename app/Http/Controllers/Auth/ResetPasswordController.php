<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    public function showResetForm(Request $request, $token = null)
    {
        $page= Page::where('slug', 'reset_password')->first();
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

        return view(template().'auth.passwords.reset',$data)->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

}
