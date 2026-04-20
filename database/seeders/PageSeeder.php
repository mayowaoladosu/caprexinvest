<?php

namespace Database\Seeders;

use App\Models\Page;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            ['name' => 'SMS Verification','slug' => 'sms_verification', 'template_name' => 'light', 'type' => 1],
            ['name' => 'Email Verification','slug' => 'email_verification', 'template_name' => 'light', 'type' => 1],
            ['name' => '2FA','slug' => '2fa', 'template_name' => 'light', 'type' => 1],
        ];
        foreach ($pages as $page) {
            Page::updateOrCreate(
                ['name' => $page['name']],
                [
                    'slug' => $page['slug'],
                    'template_name' => $page['template_name'],
                    'type' => $page['type'],
                ],
                [
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );
        }
    }
}
