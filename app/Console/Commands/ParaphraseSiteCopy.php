<?php

namespace App\Console\Commands;

use App\Models\Blog;
use App\Models\BlogDetails;
use App\Models\ContentDetails;
use App\Models\Page;
use App\Models\PageDetail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ParaphraseSiteCopy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'production:paraphrase-site-copy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Paraphrase public website wording across pages, sections, and blog copy';

    public function handle(): int
    {
        DB::transaction(function () {
            $this->paraphrasePages();
            $this->paraphrasePageDetails();
            $this->paraphraseContentDetails();
            $this->paraphraseBlogs();
        });

        $this->info('Website copy paraphrase completed.');
        return self::SUCCESS;
    }

    private function paraphrasePages(): void
    {
        Page::query()->chunkById(100, function ($pages) {
            foreach ($pages as $page) {
                $page->name = $this->paraphraseText((string) $page->name);
                $page->page_title = $this->paraphraseText((string) $page->page_title);
                $page->meta_title = $this->paraphraseText((string) $page->meta_title);
                $page->meta_description = $this->paraphraseText((string) $page->meta_description);
                $page->og_description = $this->paraphraseText((string) $page->og_description);
                $page->save();
            }
        });
    }

    private function paraphrasePageDetails(): void
    {
        PageDetail::query()->chunkById(100, function ($details) {
            foreach ($details as $detail) {
                $detail->name = $this->paraphraseText((string) $detail->name);
                $detail->content = $this->paraphraseText((string) $detail->content);
                $detail->sections = $this->paraphraseMixed($detail->sections);
                $detail->save();
            }
        });
    }

    private function paraphraseContentDetails(): void
    {
        ContentDetails::query()->chunkById(100, function ($details) {
            foreach ($details as $detail) {
                $detail->description = $this->paraphraseMixed($detail->description);
                $detail->save();
            }
        });
    }

    private function paraphraseBlogs(): void
    {
        Blog::query()->chunkById(100, function ($blogs) {
            foreach ($blogs as $blog) {
                $blog->page_title = $this->paraphraseText((string) $blog->page_title);
                $blog->meta_title = $this->paraphraseText((string) $blog->meta_title);
                $blog->meta_description = $this->paraphraseText((string) $blog->meta_description);
                $blog->save();
            }
        });

        BlogDetails::query()->chunkById(100, function ($details) {
            foreach ($details as $detail) {
                $detail->title = $this->paraphraseText((string) $detail->title);
                $detail->description = $this->paraphraseText((string) $detail->description);
                $detail->save();
            }
        });
    }

    private function paraphraseMixed(mixed $value): mixed
    {
        if (is_string($value)) {
            // Keep technical identifiers (e.g. faq_section, blog_section) untouched.
            if (preg_match('/^[a-z0-9_]+$/', $value) === 1 || str_ends_with($value, '_section')) {
                return $value;
            }
            return $this->paraphraseText($value);
        }

        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = $this->paraphraseMixed($item);
            }

            return $value;
        }

        if (is_object($value)) {
            $array = (array) $value;
            foreach ($array as $key => $item) {
                $array[$key] = $this->paraphraseMixed($item);
            }

            return $array;
        }

        return $value;
    }

    private function paraphraseText(string $text): string
    {
        if ($text === '') {
            return $text;
        }

        $replacements = [
            'A modern investment platform for disciplined exposure to gold and BTC.' => 'A practical wealth platform built for structured exposure to gold and Bitcoin.',
            'CaprexInvest combines transparent execution, clear reporting, and strong operational controls so investors can access high-conviction assets with confidence.' => 'CaprexInvest brings together dependable execution, straightforward reporting, and robust safeguards so clients can invest in conviction assets with clarity.',
            'Smart & Simple Investing' => 'Straightforward, Guided Investing',
            'Enjoy a streamlined investment process that makes owning gold easy, transparent, and rewarding—without the usual complexities.' => 'Use an efficient investment flow that makes gold ownership easier to manage, easier to track, and easier to trust.',
            'Premium Gold Solutions' => 'Advanced Gold Investment Options',
            'From purchasing to vaulting, we offer tailored gold investment services designed to help you build and protect long-term wealth.' => 'From acquisition to secure storage, we provide customized gold services that support long-horizon wealth building and protection.',
            'Backed by Real Assets' => 'Supported by Tangible Holdings',
            'Every investment is supported by tangible, securely stored gold, ensuring unmatched trust and lasting value.' => 'Each allocation is linked to real, securely held assets, giving investors stronger confidence and durable value.',
            'Fast & Flexible Payouts' => 'Responsive and Flexible Withdrawals',
            'Access your profits quickly with our hassle-free withdrawal process—your returns, on your terms.' => 'Withdraw earnings through a smooth request workflow designed for speed, control, and convenience.',
            'Click Here' => 'Open Guide',
            'Learn More' => 'Explore Details',
            'Get Started' => 'Start Now',
            'Contact Us' => 'Reach Our Team',
            'Read More' => 'Continue Reading',
            'View All' => 'Browse All',
            'Our Services' => 'What We Offer',
            'About Us' => 'Who We Are',
            'Terms and Conditions' => 'Terms of Use',
            'Cookie Policy' => 'Cookie Notice',
            'Privacy Policy' => 'Privacy Notice',
            'Frequently Asked Questions' => 'Common Questions',
            'Support Ticket' => 'Support Request',
            'Dashboard' => 'Control Center',
            'No data to show' => 'No records are available yet',
            'Please wait' => 'One moment please',
            'Please Pay' => 'Kindly complete payment of',
            'Pay Now' => 'Complete Payment',
            'Submit' => 'Send Request',
            'Success' => 'Completed',
            'Failed' => 'Not Completed',
        ];

        $updated = str_ireplace(array_keys($replacements), array_values($replacements), $text);
        return preg_replace('/\s+/', ' ', $updated) ?? $updated;
    }
}

