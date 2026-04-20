<?php

namespace App\Traits;

use App\Models\Blog;
use App\Models\ContentDetails;
use App\Models\InvestmentPlan;
use App\Models\Karat;
use Illuminate\Support\Facades\DB;

trait Frontend
{
    protected function getSectionsData($sections, $content, $selectedTheme)
    {
        if ($sections == null) {
            $data = ['support' => $content,];
            return view("themes.$selectedTheme.support", $data)->toHtml();
        }

        $contentData = ContentDetails::with('content')
            ->whereHas('content', function ($query) use ($sections) {
                $query->whereIn('name', $sections);
            })
            ->get();

        foreach ($sections as $section) {
            $singleContent = $contentData->where('content.name', $section)->where('content.type', 'single')->first() ?? [];
            if ($section == 'pricing_section' || $section == 'pricing_section_two') {
                $data[$section] = [
                    'single' => $singleContent ? collect($singleContent->description ?? [])->merge($singleContent->content->only('media')) : [],
                    'investmentPlans' => InvestmentPlan::where(['status'=>1,'soft_delete' => 0])->get()
                ];
            }elseif ($section == 'calculation_section'){
                $karats = Karat::where('status',1)->get();
                $multipleContents = $contentData->where('content.name', $section)->where('content.type', 'multiple')->values()->map(function ($multipleContentData) {
                    return collect($multipleContentData->description)->merge($multipleContentData->content->only('media'));
                });
                $data[$section] = [
                    'single' => $singleContent ? collect($singleContent->description ?? [])->merge($singleContent->content->only('media')) : [],
                    'multiple' => $multipleContents,
                    'karats' => $karats,
                ];
            }elseif($section == 'blog_section' || $section == 'blog_section_two'){
                $data[$section] = [
                    'single' => $singleContent ? collect($singleContent->description ?? [])->merge($singleContent->content->only('media')) : [],
                    'blogs' =>   Blog::where('status',1)->get()
                ];
            }elseif($section == 'product_section'){
                $bestSellingProducts = DB::table('order_items')
                    ->select('products.id','products.thumbnail_image','products.slug','products.driver','products.price','categories.name as category_name', 'product_details.title', DB::raw('SUM(order_items.quantity) as total_sold'))
                    ->join('products', 'order_items.product_id', '=', 'products.id')
                    ->join('categories', 'products.category_id', '=', 'categories.id')
                    ->join('product_details', 'products.id', '=', 'product_details.product_id')
                    ->groupBy('products.id', 'product_details.title')
                    ->orderByDesc('total_sold')
                    ->take(2) 
                    ->get();

                $data[$section] = [
                    'single' => $singleContent ? collect($singleContent->description ?? [])->merge($singleContent->content->only('media')) : [],
                    'bestSellingProducts' =>   $bestSellingProducts
                ];
            }else {
                $multipleContents = $contentData->where('content.name', $section)->where('content.type', 'multiple')->values()->map(function ($multipleContentData) {
                    return collect($multipleContentData->description)->merge($multipleContentData->content->only('media'));
                });

                $data[$section] = [
                    'single' => $singleContent ? collect($singleContent->description ?? [])->merge($singleContent->content->only('media')) : [],
                    'multiple' => $multipleContents
                ];
            }

            $replacement = view("themes.light.sections.{$section}", $data)->toHtml();

            $content = str_replace('<div class="custom-block" contenteditable="false"><div class="custom-block-content">[[' . $section . ']]</div>', $replacement, $content);
            $content = str_replace('<span class="delete-block">×</span>', '', $content);
            $content = str_replace('<span class="up-block">↑</span>', '', $content);
            $content = str_replace('<span class="down-block">↓</span></div>', '', $content);
            $content = str_replace('<p><br></p>', '', $content);
        }

        return $content;
    }
}
