<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Page;

class BlogController extends Controller
{
    public function blog($category_id = null)
    {
        $page= Page::where('slug', 'blogs')->first();
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

        $data['blogs'] = Blog::with(['details'])
            ->where('status',1)
            ->when($category_id, function ($query, $category_id) {
                return $query->where('category_id', $category_id);
            })
            ->when(request()->has('title') && request()->title, function ($query) {
                return $query->whereHas('details', function ($query) {
                    $query->where('title', 'like', '%' . request()->title . '%');
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(9);
        return view(template().'pages.blogs', $data);
    }

    public function blogDetails($slug)
    {
        $blog = Blog::with(['details'])->where('slug', $slug)->firstOrFail();

        $data['pageSeo'] = [
            'page_title' => $blog->page_title,
            'meta_title' => $blog->meta_title,
            'meta_keywords' => isset($blog->meta_keywords) && $blog->meta_keywords ?implode(',', $blog->meta_keywords ?? []):'',
            'meta_description' => $blog->meta_description,
            'meta_image' => getFile($blog->meta_image_driver, $blog->meta_image),
            'breadcrumb_image' => $blog->breadcrumb_status ?
                getFile($blog->breadcrumb_image_driver, $blog->breadcrumb_image) : null,
        ];
        $data['blogs'] = Blog::with(['details'])->where('status',1)->where('id', '!=', $blog->id)->orderBy('id', 'desc')->get()->take(4);
        $data['categories'] = BlogCategory::orderBy('id', 'desc')->get();
        $data['blog'] = $blog;
        return view(template().'pages.blog_details', $data);
    }
}
