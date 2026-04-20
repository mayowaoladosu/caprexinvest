<?php

namespace App\Providers;

use App\Models\ContentDetails;
use App\Models\Language;
use App\Models\WishList;
use App\Services\SidebarDataService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Bridge\Mailchimp\Transport\MandrillTransportFactory;
use Symfony\Component\Mailer\Bridge\Sendgrid\Transport\SendgridTransportFactory;
use Symfony\Component\Mailer\Bridge\Sendinblue\Transport\SendinblueTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        try {
            DB::connection()->getPdo();

            $data['basicControl'] = basicControl();
            $data['theme'] = template();
            $data['themeTrue'] = template(true);
            View::share($data);

            view()->composer([
                $data['theme'] . 'partials.footer',
                $data['theme'] . 'partials.header',
            ], function ($view) {
                $section = 'footer_section';
                $footer_section = ContentDetails::with('content')
                    ->whereHas('content', function ($query) use ($section) {
                        $query->where('name', $section);
                    })
                    ->get();
                $single_content = $footer_section->where('content.name', 'footer_section')->where('content.type', 'single')->first();
                $multipleContents = $footer_section->where('content.name', 'footer_section')->where('content.type', 'multiple')->values()->map(function ($multipleContentData) {
                    return collect($multipleContentData->description)->merge($multipleContentData->content->only('media'));
                });

                $datas = [
                    'single' => $single_content? collect($single_content->description ?? [])->merge($single_content->content->only('media')) : [],
                    'multiple' => $multipleContents
                ];
                $view->with("footer_section", $datas);
                $countUserWishlist = WishList::where('user_id', auth()->id())->count('id');
                $view->with("countUserWishlist", $countUserWishlist);

                $languages = Language::where('status',1)->get();
                $view->with("languages", $languages);
            });

            view()->composer([
                $data['theme'] . 'partials.pwa',
            ], function ($view) {
                $section = 'pwa_popup';
                $pwa = ContentDetails::with('content')
                    ->whereHas('content', function ($query) use ($section) {
                        $query->where('name', $section);
                    })
                    ->get();
                $singleContent = $pwa->where('content.name', $section)->where('content.type', 'single')->first() ?? [];
                $multipleContents = $pwa->where('content.name', $section)->where('content.type', 'multiple')->values()->map(function ($multipleContentData) {
                    return collect($multipleContentData->description)->merge($multipleContentData->content->only('media'));
                });
                $data = [
                    'single' => $singleContent ? collect($singleContent->description ?? [])->merge($singleContent->content->only('media')) : [],
                    'multiple' => $multipleContents,
                ];
                $view->with('pwa', $data);
            });



            view()->composer([
                'admin.layouts.sidebar',
            ], function ($view) {
                $sidebarCounts = Cache::remember('sidebar_counts', now()->addMinutes(10), function () {
                    return SidebarDataService::getSidebarCounts();
                });
                $view->with('sidebarCounts', $sidebarCounts);
            });

            if (basicControl()->force_ssl == 1) {
                if ($this->app->environment('production') || $this->app->environment('local')) {
                    \URL::forceScheme('https');
                }
            }

            Mail::extend('sendinblue', function () {
                return (new SendinblueTransportFactory)->create(
                    new Dsn(
                        'sendinblue+api',
                        'default',
                        config('services.sendinblue.key')
                    )
                );
            });

            Mail::extend('sendgrid', function () {
                return (new SendgridTransportFactory)->create(
                    new Dsn(
                        'sendgrid+api',
                        'default',
                        config('services.sendgrid.key')
                    )
                );
            });

            Mail::extend('mandrill', function () {
                return (new MandrillTransportFactory)->create(
                    new Dsn(
                        'mandrill+api',
                        'default',
                        config('services.mandrill.key')
                    )
                );
            });

        } catch (\Exception $e) {
        }

    }
}
