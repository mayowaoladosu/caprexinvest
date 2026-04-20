<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BasicControl;
use App\Models\Page;
use App\Models\PageDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class HomeStyleController extends Controller
{
    public function index()
    {
        return view('admin.home.index');
    }

    public function selectHomeStyle($val)
    {
        if (!in_array($val, ['home_one', 'home_two'])) {
            return response()->json('failed');
        }

        $home_sections = config('home.'.$val);

        $page = Page::where('slug','/')->first();
        if(!$page){
            $page = Page::first();
        }

        $pageDetails = PageDetail::where('page_id',$page->id)->get();

        foreach ($pageDetails as $pageDetail) {
           $pageDetail->content = $home_sections['content'];
           $pageDetail->sections = $home_sections['sections'];
           $pageDetail->save();
        }

        $basicControl = BasicControl::firstOrFail();
        $basicControl->home_style = $val;
        $basicControl->update();
        Artisan::call('cache:clear');
        return response()->json('success');

    }
}
