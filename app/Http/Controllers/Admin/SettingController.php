<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function rss()
    {
        $rssFeeds = [
            'rss_enabled' => Setting::get('rss_enabled', 'true'),
            'rss_title' => Setting::get('rss_title', 'Atomni News Feed'),
            'rss_description' => Setting::get('rss_description', 'Latest news and articles from Atomni'),
            'rss_max_items' => Setting::get('rss_max_items', '25'),
            'rss_custom_urls' => Setting::get('rss_custom_urls', ''),
        ];

        return view('admin.settings.rss', compact('rssFeeds'));
    }

    public function updateRss(Request $request)
    {
        $request->validate([
            'rss_title' => 'required|string|max:255',
            'rss_description' => 'required|string|max:500',
            'rss_max_items' => 'required|integer|min:5|max:100',
            'rss_custom_urls' => 'nullable|string',
        ]);

        Setting::set('rss_enabled', $request->boolean('rss_enabled') ? 'true' : 'false');
        Setting::set('rss_title', $request->input('rss_title'));
        Setting::set('rss_description', $request->input('rss_description'));
        Setting::set('rss_max_items', $request->input('rss_max_items'));
        Setting::set('rss_custom_urls', $request->input('rss_custom_urls'));

        return redirect()->route('admin.settings.rss')->with('success', 'RSS settings updated.');
    }

    // ── Global Settings (Logo, Theme) ──────────────────────────────────
    public function global()
    {
        $settings = [
            'site_name' => Setting::get('site_name', 'Atomni'),
            'website_tagline' => Setting::get('website_tagline', ''),
            'theme_type' => Setting::get('theme_type', 'preset'),
            'theme_color' => Setting::get('theme_color', 'blue'), // e.g. blue, purple, emerald
            'theme_manual_primary' => Setting::get('theme_manual_primary', '#2D7FF9'),
            'theme_manual_secondary' => Setting::get('theme_manual_secondary', '#1A5FD1'),
            'site_logo' => Setting::get('site_logo', ''),
            'site_logo_dark' => Setting::get('site_logo_dark', ''),
            'site_favicon' => Setting::get('site_favicon', ''),
            'font_family' => Setting::get('font_family', 'inter'),
            'ga_measurement_id' => Setting::get('ga_measurement_id', ''),
            'ticker_enabled' => Setting::get('ticker_enabled', '0'),
            'ticker_mode' => Setting::get('ticker_mode', 'latest_posts'),
            'ticker_text' => Setting::get('ticker_text', "⚡ Supreme Court delivers landmark ruling on digital privacy rights\n📈 Global markets rally as trade negotiations reach breakthrough\n🚀 SpaceX successfully completes first Mars cargo mission\n🏆 Historic upset at the World Championships — underdog team claims gold"),
            'ticker_speed' => Setting::get('ticker_speed', '25'),
            'media_keep_original' => Setting::get('media_keep_original', '1'),
            'contact_email' => Setting::get('contact_email', ''),
            'contact_phone' => Setting::get('contact_phone', ''),
            'contact_address' => Setting::get('contact_address', ''),
            'contact_map_embed' => Setting::get('contact_map_embed', ''),
            'donate_qr_link' => Setting::get('donate_qr_link', ''),
            'donate_note' => Setting::get('donate_note', ''),
            'donation_link' => Setting::get('donation_link', ''),
            'home_author_section_enabled' => Setting::get('home_author_section_enabled', '0'),
            'home_author_section_title' => Setting::get('home_author_section_title', 'Selected Author'),
            'home_author_section_author_id' => Setting::get('home_author_section_author_id', ''),
        ];
        
        $authors = \App\Models\User::whereIn('role', ['author', 'admin', 'super_admin'])->get();
        $curatedPresets = config('theme.presets', []);
        
        return view('admin.settings.global', compact('settings', 'authors', 'curatedPresets'));
    }

    public function updateGlobal(Request $request)
    {
        $section = $request->input('section');

        if ($section === 'identity') {
            $request->validate([
                'site_name' => 'required|string|max:255',
                'website_tagline' => 'nullable|string|max:255',
                'site_logo' => 'nullable|string',
                'site_logo_dark' => 'nullable|string',
                'site_favicon' => 'nullable|string',
                'ticker_mode' => 'nullable|string|in:latest_posts,custom_announcement',
                'ticker_text' => 'nullable|string',
                'ticker_speed' => 'nullable|integer|min:5|max:120',
                'media_keep_original' => 'nullable|boolean',
                'contact_email' => 'nullable|email|max:255',
                'contact_phone' => 'nullable|string|max:50',
                'contact_address' => 'nullable|string',
                'contact_map_embed' => 'nullable|string',
                'donation_link' => 'nullable|string|max:2048',
                'donate_qr_link' => 'nullable|string|max:2048',
                'donate_note' => 'nullable|string|max:1000',
                'home_author_section_title' => 'nullable|string|max:255',
                'home_author_section_author_id' => 'nullable|integer',
                'google_site_verification' => 'nullable|string|max:255',
            ]);

            Setting::set('ticker_enabled', $request->has('ticker_enabled') ? '1' : '0');
            Setting::set('ticker_mode', $request->input('ticker_mode', 'latest_posts'));
            Setting::set('ticker_text', $request->input('ticker_text', ''));
            Setting::set('ticker_speed', (string) $request->input('ticker_speed', '25'));
            Setting::set('media_keep_original', $request->has('media_keep_original') ? '1' : '0');
            Setting::set('contact_email', $request->input('contact_email', ''));
            Setting::set('contact_phone', $request->input('contact_phone', ''));
            Setting::set('contact_address', $request->input('contact_address', ''));
            Setting::set('contact_map_embed', $request->input('contact_map_embed', ''));
            Setting::set('donate_qr_link', $request->input('donate_qr_link', ''));
            Setting::set('donate_note', $request->input('donate_note', ''));
            Setting::set('donation_link', $request->input('donation_link', ''));
            Setting::set('google_site_verification', $request->input('google_site_verification', ''));
            
            Setting::set('home_author_section_enabled', $request->has('home_author_section_enabled') ? '1' : '0');
            Setting::set('home_author_section_title', $request->input('home_author_section_title', 'Selected Author'));
            Setting::set('home_author_section_author_id', $request->input('home_author_section_author_id', ''));

            Setting::set('site_name', $request->input('site_name'));
            Setting::set('website_tagline', $request->input('website_tagline', ''));

            Setting::set('site_logo', $request->input('site_logo', ''));
            Setting::set('site_logo_dark', $request->input('site_logo_dark', ''));
            Setting::set('site_favicon', $request->input('site_favicon', ''));
            $message = 'Site identity settings updated.';
        } elseif ($section === 'theme') {
            if ($request->input('action') === 'reset') {
                Setting::set('theme_type', 'preset');
                Setting::set('theme_color', 'blue');
                Setting::set('theme_manual_primary', '#2D7FF9');
                Setting::set('theme_manual_secondary', '#1A5FD1');
                Setting::set('font_family', 'Inter');
                return redirect()->route('admin.settings.global')->with('success', 'Theme settings have been reset to default.');
            }

            $request->validate([
                'theme_type' => 'required|string|in:preset,manual',
                'theme_color' => 'nullable|string|in:blue,purple,emerald,rose,amber,brand,sunset,cyberpunk,sakura,midnight,forest,lavender',
                'theme_manual_primary' => ['nullable', 'string', 'regex:/^#[a-fA-F0-9]{6}$/i'],
                'theme_manual_secondary' => ['nullable', 'string', 'regex:/^#[a-fA-F0-9]{6}$/i'],
                'font_family' => 'required|string',
            ]);
            Setting::set('theme_type', $request->input('theme_type', 'preset'));
            Setting::set('theme_color', $request->input('theme_color', 'blue'));
            Setting::set('theme_manual_primary', $request->input('theme_manual_primary', '#2D7FF9'));
            Setting::set('theme_manual_secondary', $request->input('theme_manual_secondary', '#1A5FD1'));
            Setting::set('font_family', $request->input('font_family', 'Inter'));
            $message = 'Theme customization updated.';
        } elseif ($section === 'integrations') {
            $request->validate([
                'ga_measurement_id' => 'nullable|string|max:50',
            ]);
            Setting::set('ga_measurement_id', $request->input('ga_measurement_id'));
            $message = 'External integrations updated.';
        } else {
            // Fallback for legacy form submission if needed
            $request->validate([
                'site_name' => 'required|string|max:255',
                'theme_color' => 'required|string|in:blue,purple,emerald,rose,amber,brand',
                'site_logo' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
                'ga_measurement_id' => 'nullable|string|max:50',
            ]);

            Setting::set('site_name', $request->input('site_name'));
            Setting::set('theme_color', $request->input('theme_color'));
            Setting::set('ga_measurement_id', $request->input('ga_measurement_id'));

            if ($request->hasFile('site_logo')) {
                $path = $request->file('site_logo')->store('logos', 'public');
                Setting::set('site_logo', $path);
            }
            $message = 'Global settings updated.';
        }

        return redirect()->route('admin.settings.global')->with('success', $message);
    }

    public function menus()
    {
        $navbarLinks = json_decode(Setting::get('navbar_links', '[]'), true) ?: [];
        $footerLinks = json_decode(Setting::get('footer_links', '[]'), true) ?: [];
        $megaMenuLinks = json_decode(Setting::get('mega_menu_links', '[]'), true) ?: [];
        $exploreLinks = json_decode(Setting::get('explore_links', '[]'), true) ?: [];

        $pages = \App\Models\Page::where('is_published', true)->orderBy('title')->get();
        // Also fetch categories to allow selecting them for Mega/Explore links
        $categories = \App\Models\Category::orderBy('name')->get();

        return view('admin.settings.menus', compact('navbarLinks', 'footerLinks', 'megaMenuLinks', 'exploreLinks', 'pages', 'categories'));
    }

    public function updateMenus(Request $request)
    {
        $request->validate([
            'navbar_links' => 'nullable|string', // Expecting JSON string from frontend
            'footer_links' => 'nullable|string', // Expecting JSON string from frontend
            'mega_menu_links' => 'nullable|string',
            'explore_links' => 'nullable|string',
        ]);

        Setting::set('navbar_links', $request->input('navbar_links', '[]'));
        Setting::set('footer_links', $request->input('footer_links', '[]'));
        Setting::set('mega_menu_links', $request->input('mega_menu_links', '[]'));
        Setting::set('explore_links', $request->input('explore_links', '[]'));

        return redirect()->route('admin.settings.menus')->with('success', 'Menu structures updated.');
    }

    public function integrations()
    {
        $settings = [
            'ga_measurement_id'        => Setting::get('ga_measurement_id', ''),
            'gtm_container_id'         => Setting::get('gtm_container_id', ''),
            'open_ai_key'              => Setting::get('open_ai_key', ''),
            'anthropic_key'            => Setting::get('anthropic_key', ''),
            'gemini_key'               => Setting::get('gemini_key', ''),
            'default_llm_model'        => Setting::get('default_llm_model', 'gpt-4o'),
            // GA4 Data API
            'ga4_property_id'          => Setting::get('ga4_property_id', ''),
            'ga4_service_account_json' => Setting::get('ga4_service_account_json', ''),
            
            // AdSense
            'adsense_pub_id'           => Setting::get('adsense_pub_id', ''),
            'adsense_ads_txt'          => Setting::get('adsense_ads_txt', ''),
        ];
        return view('admin.settings.integrations', compact('settings'));
    }

    public function updateIntegrations(Request $request)
    {
        $request->validate([
            'ga_measurement_id'        => 'nullable|string|max:50',
            'gtm_container_id'         => 'nullable|string|max:30',
            'open_ai_key'              => 'nullable|string',
            'anthropic_key'            => 'nullable|string',
            'gemini_key'               => 'nullable|string',
            'default_llm_model'        => 'nullable|string|max:50',
            'ga4_property_id'          => 'nullable|string|max:30',
            'ga4_service_account_json' => 'nullable|string',
            'adsense_pub_id'           => 'nullable|string|max:100',
            'adsense_ads_txt'          => 'nullable|string',
        ]);

        Setting::set('ga_measurement_id',        $request->input('ga_measurement_id'));
        Setting::set('gtm_container_id',         $request->input('gtm_container_id'));
        Setting::set('open_ai_key',              $request->input('open_ai_key'));
        Setting::set('anthropic_key',            $request->input('anthropic_key'));
        Setting::set('gemini_key',               $request->input('gemini_key'));
        Setting::set('default_llm_model',        $request->input('default_llm_model', 'gpt-4o'));
        Setting::set('ga4_property_id',          $request->input('ga4_property_id', ''));
        Setting::set('ga4_service_account_json', $request->input('ga4_service_account_json', ''));
        Setting::set('adsense_pub_id',           $request->input('adsense_pub_id', ''));
        Setting::set('adsense_ads_txt',          $request->input('adsense_ads_txt', ''));

        return redirect()->route('admin.settings.integrations')->with('success', 'External integrations updated.');
    }

    // ── Ad Controls ──────────────────────────────────────────────────
    public function ads()
    {
        $settings = [
            'ad_header_leaderboard' => Setting::get('ad_header_leaderboard', ''),
            'ad_header_leaderboard_image_source' => Setting::get('ad_header_leaderboard_image_source', ''),
            'ad_header_leaderboard_link' => Setting::get('ad_header_leaderboard_link', ''),
            
            'ad_in_article'         => Setting::get('ad_in_article', ''),
            'ad_in_article_image_source' => Setting::get('ad_in_article_image_source', ''),
            'ad_in_article_link' => Setting::get('ad_in_article_link', ''),
            
            'ad_sidebar'            => Setting::get('ad_sidebar', ''),
            'ad_sidebar_image_source' => Setting::get('ad_sidebar_image_source', ''),
            'ad_sidebar_link' => Setting::get('ad_sidebar_link', ''),
        ];
        return view('admin.settings.ads', compact('settings'));
    }

    public function updateAds(Request $request)
    {
        $request->validate([
            'ad_header_leaderboard' => 'nullable|string',
            'ad_header_leaderboard_image' => 'nullable|image|max:2048',
            'ad_header_leaderboard_link' => 'nullable|url',
            
            'ad_in_article'         => 'nullable|string',
            'ad_in_article_image' => 'nullable|image|max:2048',
            'ad_in_article_link' => 'nullable|url',
            
            'ad_sidebar'            => 'nullable|string',
            'ad_sidebar_image' => 'nullable|image|max:2048',
            'ad_sidebar_link' => 'nullable|url',
        ]);

        $placements = ['ad_header_leaderboard', 'ad_in_article', 'ad_sidebar'];

        foreach ($placements as $placement) {
            $rawHtml = $request->input($placement, '');
            $newLink = $request->input("{$placement}_link", '');

            if ($request->hasFile("{$placement}_image")) {
                $path = $request->file("{$placement}_image")->store('ads', 'public');
                $url = \Illuminate\Support\Facades\Storage::url($path);
                $link = $newLink ?: '#';
                $target = $link === '#' ? '' : ' target="_blank" rel="sponsored"';
                
                $rawHtml = '<a href="'.e($link).'"'.$target.' class="block hover:opacity-90 transition-opacity"><img src="'.e($url).'" alt="Advertisement" class="w-full h-auto rounded-lg mx-auto"></a>';
                
                Setting::set("{$placement}_image_source", $path);
                Setting::set("{$placement}_link", $link);
            } else {
                $existingImage = Setting::get("{$placement}_image_source", '');
                $existingLink = Setting::get("{$placement}_link", '');

                if ($existingImage) {
                    $imageFilename = basename($existingImage);
                    if (str_contains($rawHtml, $imageFilename)) {
                        // The user kept the image banner
                        if ($newLink !== $existingLink) {
                            $imageUrl = \Illuminate\Support\Facades\Storage::url($existingImage);
                            $link = $newLink ?: '#';
                            $target = $link === '#' ? '' : ' target="_blank" rel="sponsored"';
                            $rawHtml = '<a href="'.e($link).'"'.$target.' class="block hover:opacity-90 transition-opacity"><img src="'.e($imageUrl).'" alt="Advertisement" class="w-full h-auto rounded-lg mx-auto"></a>';
                            
                            Setting::set("{$placement}_link", $link);
                        }
                    } else {
                        // User modified the raw HTML manually or cleared it, so discard structured settings
                        Setting::set("{$placement}_image_source", '');
                        Setting::set("{$placement}_link", '');
                    }
                }
            }

            Setting::set($placement, $rawHtml);
        }

        return redirect()->route('admin.settings.ads')->with('success', 'Ad placements updated.');
    }

    // ── Social Links ──────────────────────────────────────────────────
    public function social()
    {
        $settings = [
            'social_twitter' => Setting::get('social_twitter', ''),
            'social_facebook' => Setting::get('social_facebook', ''),
            'social_instagram' => Setting::get('social_instagram', ''),
            'social_linkedin' => Setting::get('social_linkedin', ''),
            'social_youtube' => Setting::get('social_youtube', ''),
            'social_rss' => Setting::get('social_rss', ''),
        ];
        return view('admin.settings.social', compact('settings'));
    }

    public function updateSocial(Request $request)
    {
        $request->validate([
            'social_twitter' => 'nullable|url|max:255',
            'social_facebook' => 'nullable|url|max:255',
            'social_instagram' => 'nullable|url|max:255',
            'social_linkedin' => 'nullable|url|max:255',
            'social_youtube' => 'nullable|url|max:255',
            'social_rss' => 'nullable|url|max:255',
        ]);

        foreach (['social_twitter', 'social_facebook', 'social_instagram', 'social_linkedin', 'social_youtube', 'social_rss'] as $key) {
            Setting::set($key, $request->input($key, ''));
        }

        return redirect()->route('admin.settings.social')->with('success', 'Social media links updated.');
    }

    // ── Permalinks ────────────────────────────────────────────────────
    public function permalink()
    {
        $current = Setting::get('permalink_structure', 'post-name');
        return view('admin.settings.permalink', compact('current'));
    }

    public function updatePermalink(Request $request)
    {
        $request->validate([
            'permalink_structure' => 'required|string|in:plain,day-name,month-name,numeric,post-name',
        ]);

        Setting::set('permalink_structure', $request->input('permalink_structure'));

        return redirect()->route('admin.settings.permalink')->with('success', 'Permalink structure updated.');
    }
}

