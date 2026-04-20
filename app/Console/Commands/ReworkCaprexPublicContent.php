<?php

namespace App\Console\Commands;

use App\Models\Blog;
use App\Models\BlogDetails;
use App\Models\Content;
use App\Models\Language;
use App\Models\Page;
use App\Models\PageDetail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReworkCaprexPublicContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'production:rework-public-content';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rework public pages and legal content for CaprexInvest production launch';

    public function handle(): int
    {
        $supportEmail = 'support@caprexinvest.com';
        $companyAddress = '100 Peachtree St NW, Suite 1450, Atlanta, GA 30303, United States';
        $languageId = (int) (Language::query()->value('id') ?? 1);

        DB::transaction(function () use ($supportEmail, $companyAddress, $languageId) {
            $this->updatePageContent();
            $this->updateSectionContent($supportEmail, $companyAddress);
            $this->updateBlogs($languageId, $supportEmail);
        });

        $this->info('Public content rework completed successfully.');
        return self::SUCCESS;
    }

    private function updatePageContent(): void
    {
        $pageMeta = [
            'about' => [
                'name' => 'About',
                'page_title' => 'About CaprexInvest',
                'meta_title' => 'About CaprexInvest | Trusted Gold and BTC Investing Platform',
                'meta_description' => 'Learn about CaprexInvest, our mission, risk controls, and how we deliver secure access to gold and BTC investment opportunities.',
            ],
            'blogs' => [
                'name' => 'Blog',
                'page_title' => 'CaprexInvest Blog',
                'meta_title' => 'CaprexInvest Blog | Market Insights and Investor Education',
                'meta_description' => 'Read clear and practical articles on digital assets, portfolio strategy, risk management, and investment fundamentals.',
            ],
            'cookie-policy' => [
                'name' => 'Cookie Policy',
                'page_title' => 'Cookie Policy',
                'meta_title' => 'CaprexInvest Cookie Policy',
                'meta_description' => 'Understand how CaprexInvest uses cookies and similar technologies to improve security, performance, and user experience.',
            ],
            'terms-condition' => [
                'name' => 'Terms and Conditions',
                'page_title' => 'Terms and Conditions',
                'meta_title' => 'CaprexInvest Terms and Conditions',
                'meta_description' => 'Review the terms governing account usage, platform access, prohibited conduct, and applicable legal obligations.',
            ],
            'privacy-policy' => [
                'name' => 'Privacy Policy',
                'page_title' => 'Privacy Policy',
                'meta_title' => 'CaprexInvest Privacy Policy',
                'meta_description' => 'See what personal data CaprexInvest collects, why it is processed, and how users can exercise privacy rights.',
            ],
        ];

        foreach ($pageMeta as $slug => $meta) {
            $page = Page::query()->where('slug', $slug)->first();
            if (!$page) {
                continue;
            }

            $page->name = $meta['name'];
            $page->page_title = $meta['page_title'];
            $page->meta_title = $meta['meta_title'];
            $page->meta_description = $meta['meta_description'];
            $page->og_description = $meta['meta_description'];
            $page->save();

            PageDetail::query()->where('page_id', $page->id)->update([
                'name' => $meta['name'],
            ]);
        }
    }

    private function updateSectionContent(string $supportEmail, string $companyAddress): void
    {
        $this->updateSingleSection('footer_section', [
            'short_description' => 'CaprexInvest is a global digital investment platform focused on transparent access to gold and BTC opportunities.',
            'phone' => '+1 (212) 970-3100',
            'address' => $companyAddress,
            'email' => $supportEmail,
            'newsletter_title' => 'Get Market Updates',
            'newsletter_description' => 'Receive practical updates on market trends, platform improvements, and investor education.',
        ]);

        $this->updateSingleSection('contact_section', [
            'title' => 'Contact CaprexInvest',
            'sub_title' => 'Our support and operations teams are available to help with account, compliance, and platform questions.',
            'email' => $supportEmail,
            'phone' => '+1 (212) 970-3100',
            'location' => $companyAddress,
            'support' => 'Response time: typically within one business day',
            'location_url' => 'https://maps.google.com/?q=100+Peachtree+St+NW+Atlanta+GA+30303',
        ]);

        $this->updateSingleSection('about_section', [
            'title' => 'About CaprexInvest',
            'sub_title' => 'A modern investment platform for disciplined exposure to gold and BTC.',
            'description' => 'CaprexInvest combines transparent execution, clear reporting, and strong operational controls so investors can access high-conviction assets with confidence.',
        ]);

        $this->replaceMultipleSection('about_section', [
            [
                'icon' => 'fa-light fa-shield-check',
                'title' => 'Security and Compliance',
                'short_description' => 'We use account verification, transaction monitoring, and operational controls designed for responsible platform growth.',
            ],
            [
                'icon' => 'fa-light fa-chart-line-up',
                'title' => 'Clear Investment Visibility',
                'short_description' => 'Users can track allocation, order history, and market context in one place with practical dashboard reporting.',
            ],
        ]);

        $this->updateSingleSection('blog_section', [
            'title' => 'Market Insights',
            'sub_title' => 'Practical education for serious investors.',
            'button_name' => 'View All Articles',
            'button_url' => '/blogs',
        ]);

        $this->updateSingleSection('faq_section', [
            'title' => 'FAQs',
            'sub_title' => 'Common questions about CaprexInvest accounts, deposits, withdrawals, and compliance.',
        ]);

        $this->replaceMultipleSection('faq_section', [
            [
                'question' => 'How are deposits and withdrawals handled on CaprexInvest?',
                'answer' => '<p>Deposits and withdrawals are currently processed through manual BTC workflows for verification and operational security. Deposit requests require a payment receipt upload, while withdrawal requests are reviewed before release.</p>',
            ],
            [
                'question' => 'Do I need to complete identity verification before trading?',
                'answer' => '<p>Yes. KYC checks are required before enabling full trading functionality. This supports fraud prevention, regulatory obligations, and platform safety standards.</p>',
            ],
            [
                'question' => 'Can I invest in both gold and BTC plans?',
                'answer' => '<p>Yes. CaprexInvest offers both gold and BTC investment plans so users can choose based on portfolio goals and risk tolerance.</p>',
            ],
            [
                'question' => 'Where can I see my order and transaction history?',
                'answer' => '<p>Users can review transaction records, plan activity, and trading data from the dashboard area after login.</p>',
            ],
            [
                'question' => 'How do I contact support?',
                'answer' => '<p>You can contact support at <a href="mailto:support@caprexinvest.com">support@caprexinvest.com</a>. Our team responds as quickly as possible during business hours.</p>',
            ],
        ]);

        $cookies = [
            [
                'heading' => 'What Cookies Are',
                'description' => '<p>Cookies are small text files stored on your device to help websites operate correctly and remember key preferences.</p>',
            ],
            [
                'heading' => 'How We Use Cookies',
                'description' => '<p>CaprexInvest uses cookies to keep sessions secure, improve site performance, and understand how users interact with our services.</p>',
            ],
            [
                'heading' => 'Essential Cookies',
                'description' => '<p>Essential cookies are required for account login, navigation, and security controls. Disabling them may break core features.</p>',
            ],
            [
                'heading' => 'Analytics Cookies',
                'description' => '<p>Analytics cookies help us measure traffic and improve content and usability without exposing sensitive account actions.</p>',
            ],
            [
                'heading' => 'Preference Cookies',
                'description' => '<p>Preference cookies remember language and interface settings to provide a more consistent experience across sessions.</p>',
            ],
            [
                'heading' => 'Third-Party Technologies',
                'description' => '<p>Some trusted service providers may set cookies when delivering infrastructure, analytics, or security functionality.</p>',
            ],
            [
                'heading' => 'Managing Cookie Choices',
                'description' => '<p>You can control cookies through your browser settings. Blocking some cookie categories may affect feature availability.</p>',
            ],
            [
                'heading' => 'Contact for Cookie Questions',
                'description' => '<p>For cookie-related questions, contact <a href="mailto:support@caprexinvest.com">support@caprexinvest.com</a>.</p>',
            ],
        ];

        $terms = [
            [
                'heading' => 'Agreement to Terms',
                'description' => '<p>By accessing or using CaprexInvest, you agree to these Terms and our applicable policies. If you disagree, do not use the platform.</p>',
            ],
            [
                'heading' => 'Eligibility',
                'description' => '<p>Users must be legally eligible to use investment services in their jurisdiction and must provide accurate account information.</p>',
            ],
            [
                'heading' => 'Account Responsibilities',
                'description' => '<p>You are responsible for account credentials, activity originating from your account, and maintaining current profile details.</p>',
            ],
            [
                'heading' => 'Risk Disclosure',
                'description' => '<p>Investment activity involves market risk. Asset prices can move rapidly and past performance does not guarantee future results.</p>',
            ],
            [
                'heading' => 'Prohibited Conduct',
                'description' => '<p>Users may not attempt fraud, abuse platform controls, manipulate transactions, or use the service for unlawful activity.</p>',
            ],
            [
                'heading' => 'Service Availability',
                'description' => '<p>We may update, suspend, or restrict features for maintenance, compliance, risk management, or operational integrity.</p>',
            ],
            [
                'heading' => 'Limitation of Liability',
                'description' => '<p>To the maximum extent permitted by law, CaprexInvest is not liable for indirect or consequential damages arising from platform use.</p>',
            ],
            [
                'heading' => 'Contact Information',
                'description' => '<p>Questions regarding these terms can be sent to <a href="mailto:support@caprexinvest.com">support@caprexinvest.com</a> or mailed to ' . e($companyAddress) . '.</p>',
            ],
        ];

        $privacy = [
            [
                'heading' => 'Information We Collect',
                'description' => '<p>We collect account, identity, and transaction-related information required for platform operations, security, and legal obligations.</p>',
            ],
            [
                'heading' => 'How We Use Information',
                'description' => '<p>Data is used to provide services, maintain account security, process requests, and improve product reliability.</p>',
            ],
            [
                'heading' => 'Legal and Compliance Processing',
                'description' => '<p>Information may be processed to satisfy anti-fraud, anti-money-laundering, sanctions screening, and recordkeeping requirements.</p>',
            ],
            [
                'heading' => 'Information Sharing',
                'description' => '<p>We may share limited data with trusted providers and legal authorities where required by law or operational necessity.</p>',
            ],
            [
                'heading' => 'Data Retention',
                'description' => '<p>We retain records for as long as needed to provide services, resolve disputes, enforce agreements, and meet legal obligations.</p>',
            ],
            [
                'heading' => 'Security Measures',
                'description' => '<p>CaprexInvest applies administrative and technical controls to protect information from unauthorized access and misuse.</p>',
            ],
            [
                'heading' => 'Your Privacy Rights',
                'description' => '<p>Depending on your location, you may have rights to request access, correction, or deletion of certain personal information.</p>',
            ],
            [
                'heading' => 'Contact for Privacy Requests',
                'description' => '<p>For privacy requests or questions, email <a href="mailto:support@caprexinvest.com">support@caprexinvest.com</a> or write to ' . e($companyAddress) . '.</p>',
            ],
        ];

        $this->replaceMultipleSection('cookie_policy', $cookies);
        $this->replaceMultipleSection('terms_condition', $terms);
        $this->replaceMultipleSection('privacy_policy', $privacy);
    }

    private function updateBlogs(int $languageId, string $supportEmail): void
    {
        $blogData = [
            [
                'title' => 'Gold and Bitcoin in One Portfolio: A Practical Allocation Framework',
                'description' => '<p>Gold and Bitcoin often attract different investor profiles, yet both can play a role in a modern portfolio. Gold is typically used as a long-term store of value, while Bitcoin offers higher-growth potential with higher volatility.</p><p>A practical approach is to set a risk budget first, then allocate across defensive and growth assets based on your time horizon. Keep position sizing disciplined and rebalance periodically instead of reacting emotionally to short-term price swings.</p><p>CaprexInvest users can monitor both gold and BTC investment options in one account and apply a consistent process to risk management, entry timing, and performance review.</p>',
            ],
            [
                'title' => 'How to Evaluate Risk Before Opening Any Trade',
                'description' => '<p>Risk management should come before return targets. Start by defining your maximum loss per day and per position, then apply stop-loss logic and exposure limits that align with your account size.</p><p>Avoid concentrating capital in one symbol or one strategy. Track win rate, average gain, and average loss over time to measure whether your execution discipline is improving.</p><p>CaprexInvest includes tools that help users review order history and build more structured decision-making habits.</p>',
            ],
            [
                'title' => 'Manual Settlement Workflows: Why Receipt Validation Matters',
                'description' => '<p>Manual settlement workflows can improve operational control when designed correctly. Requiring proof of payment helps reduce fraud risk and gives operations teams a clear audit trail before funds are credited.</p><p>For withdrawals, amount-based requests with verification checks help ensure that balances, account status, and destination details are validated before release.</p><p>If you need assistance with a pending request, contact <a href="mailto:' . e($supportEmail) . '">' . e($supportEmail) . '</a>.</p>',
            ],
        ];

        $blogs = Blog::query()->orderBy('id')->get();
        foreach ($blogs as $index => $blog) {
            $payload = $blogData[$index % count($blogData)];
            $slug = Str::slug($payload['title']);

            $blog->slug = $slug;
            $blog->page_title = $payload['title'];
            $blog->meta_title = $payload['title'];
            $blog->meta_description = Str::limit(strip_tags($payload['description']), 155);
            $blog->status = 1;
            $blog->save();

            BlogDetails::query()->updateOrCreate(
                ['blog_id' => $blog->id, 'language_id' => $languageId],
                [
                    'title' => $payload['title'],
                    'description' => $payload['description'],
                ]
            );
        }
    }

    private function updateSingleSection(string $name, array $data): void
    {
        $content = Content::query()->where('name', $name)->where('type', 'single')->first();
        if (!$content) {
            return;
        }

        foreach ($content->contentDetails as $detail) {
            $description = json_decode(json_encode($detail->description), true);
            if (!is_array($description)) {
                $description = [];
            }

            $detail->description = array_merge($description, $data);
            $detail->save();
        }
    }

    private function replaceMultipleSection(string $name, array $items): void
    {
        $contents = Content::query()->where('name', $name)->where('type', 'multiple')->orderBy('id')->get();
        if ($contents->isEmpty()) {
            return;
        }

        $maxIndex = count($items) - 1;
        if ($maxIndex < 0) {
            return;
        }

        foreach ($contents as $index => $content) {
            $payload = $items[min($index, $maxIndex)];
            foreach ($content->contentDetails as $detail) {
                $detail->description = $payload;
                $detail->save();
            }
        }
    }
}

