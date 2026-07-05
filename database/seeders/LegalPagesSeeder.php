<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class LegalPagesSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy',
                'is_published' => true,
                'content' => '<h2>1. Information We Collect</h2>
<p>We collect information you provide directly, such as when you create an account, subscribe to our newsletter, or contact us. This may include your name, email address, and communication preferences.</p>
<p>We also automatically collect certain information when you visit our site, including your IP address, browser type, operating system, referring URLs, pages viewed, and the dates/times of your visits.</p>

<h2>2. How We Use Your Information</h2>
<ul>
<li>To provide, maintain, and improve our services</li>
<li>To send newsletters and editorial content you\'ve subscribed to</li>
<li>To communicate service updates and respond to inquiries</li>
<li>To analyze usage patterns and optimize user experience</li>
<li>To detect and prevent fraud or security threats</li>
</ul>

<h2>3. Cookies and Tracking</h2>
<p>We use cookies and similar tracking technologies to enhance your browsing experience. Essential cookies are required for site functionality. Analytics cookies help us understand how visitors interact with our content. You can manage your cookie preferences through your browser settings or our <a href="/cookies">Cookie Policy</a>.</p>

<h2>4. Data Sharing</h2>
<p>We do not sell your personal data. We may share information with trusted third-party service providers who assist us in operating our platform (e.g., email delivery services, analytics providers), subject to strict data processing agreements.</p>

<h2>5. Your Rights</h2>
<p>Depending on your jurisdiction, you may have rights to access, correct, delete, or port your personal data. To exercise these rights, please contact us at <strong>privacy@atomni.in</strong>.</p>

<h2>6. Data Security</h2>
<p>We implement industry-standard security measures including encryption in transit (TLS 1.3), encrypted storage, regular security audits, and strict access controls to protect your information.</p>

<h2>7. Contact</h2>
<p>For privacy-related questions, please contact our Data Protection Officer at <strong>privacy@atomni.in</strong> or visit our <a href="/contact">Contact page</a>.</p>',
            ],
            [
                'title' => 'Terms of Service',
                'slug' => 'terms',
                'is_published' => true,
                'content' => '<h2>1. Acceptance of Terms</h2>
<p>By accessing and using Atomni ("the Service"), you agree to be bound by these Terms of Service. If you do not agree with any part of these terms, you must discontinue use of the Service immediately.</p>

<h2>2. User Accounts</h2>
<p>When creating an account, you must provide accurate and complete information. You are responsible for maintaining the security of your account credentials and for all activities that occur under your account.</p>

<h2>3. Intellectual Property</h2>
<p>All content published on Atomni — including articles, images, graphics, logos, and software — is the property of Atomni Media or its content providers and is protected by copyright and intellectual property laws. Unauthorized reproduction, distribution, or modification is prohibited.</p>

<h2>4. User-Generated Content</h2>
<p>By submitting comments or other content, you grant Atomni a non-exclusive, worldwide, royalty-free license to use, display, and distribute such content. You retain ownership of your original content but agree not to post content that is defamatory, obscene, or violates the rights of others.</p>

<h2>5. Prohibited Conduct</h2>
<ul>
<li>Using automated tools to scrape or extract content without authorization</li>
<li>Impersonating other users, journalists, or Atomni staff</li>
<li>Posting spam, phishing links, or malicious software</li>
<li>Circumventing paywalls, rate limits, or access controls</li>
<li>Harassing, threatening, or intimidating other users</li>
</ul>

<h2>6. Limitation of Liability</h2>
<p>Atomni provides content for informational purposes only. We are not liable for any damages arising from your reliance on information published on our platform. The Service is provided "as is" without warranties of any kind.</p>

<h2>7. Changes to Terms</h2>
<p>We reserve the right to modify these terms at any time. Material changes will be communicated via email or a prominent notice on our site. Continued use of the Service after changes constitutes acceptance of the updated terms.</p>

<h2>8. Contact</h2>
<p>Questions about these terms? Reach us at <strong>legal@atomni.in</strong> or visit our <a href="/contact">Contact page</a>.</p>',
            ],
            [
                'title' => 'Cookie Policy',
                'slug' => 'cookies',
                'is_published' => true,
                'content' => '<h2>1. What Are Cookies?</h2>
<p>Cookies are small text files placed on your device when you visit our website. They help us provide you with a better experience by remembering your preferences and understanding how you use our site.</p>

<h2>2. Types of Cookies We Use</h2>
<h3>Essential Cookies</h3>
<p>These cookies are necessary for the website to function and cannot be switched off. They are usually set in response to actions like setting your privacy preferences, logging in, or filling in forms.</p>

<h3>Analytics Cookies</h3>
<p>These help us count visits and traffic sources so we can measure and improve site performance. They help us know which pages are the most and least popular and see how visitors move around the site.</p>

<h3>Marketing Cookies</h3>
<p>These cookies may be set through our site by our advertising partners. They may be used to build a profile of your interests and show you relevant ads on other sites.</p>

<h2>3. Managing Your Cookie Preferences</h2>
<p>You can manage your cookie preferences at any time through the cookie settings banner shown on your first visit, or through your browser settings. Note that blocking certain cookies may impact your experience on our site.</p>

<h2>4. Contact</h2>
<p>For questions about our cookie practices, please contact us at <strong>info@atomni.in</strong>.</p>',
            ],
            [
                'title' => 'DMCA',
                'slug' => 'dmca',
                'is_published' => true,
                'content' => '<h2>Digital Millennium Copyright Act Notice</h2>
<p>Atomni respects the intellectual property rights of others and expects our users to do the same. In accordance with the Digital Millennium Copyright Act (DMCA), we will respond expeditiously to claims of copyright infringement committed using our service.</p>

<h2>Filing a DMCA Notice</h2>
<p>If you believe that content on Atomni infringes your copyright, please submit a written notification containing:</p>
<ul>
<li>A physical or electronic signature of the copyright owner or authorized agent</li>
<li>Identification of the copyrighted work claimed to have been infringed</li>
<li>Identification of the material that is claimed to be infringing, with sufficient information for us to locate it</li>
<li>Your contact information (address, telephone number, and email)</li>
<li>A statement that you have a good faith belief that the use is not authorized</li>
<li>A statement, under penalty of perjury, that the information in the notification is accurate</li>
</ul>

<h2>Contact for DMCA Notices</h2>
<p>Send DMCA notices to: <strong>dmca@atomni.in</strong></p>

<h2>Counter-Notification</h2>
<p>If you believe your content was wrongly removed, you may file a counter-notification with the same information, plus a statement under penalty of perjury that you have a good faith belief that the material was removed as a result of mistake or misidentification.</p>',
            ],
            [
                'title' => 'Accessibility',
                'slug' => 'accessibility',
                'is_published' => true,
                'content' => '<h2>Our Commitment</h2>
<p>Atomni is committed to ensuring digital accessibility for people with disabilities. We are continually improving the user experience for everyone and applying the relevant accessibility standards.</p>

<h2>Conformance Status</h2>
<p>We aim to conform to the Web Content Accessibility Guidelines (WCAG) 2.1, Level AA. These guidelines explain how to make web content more accessible for people with disabilities.</p>

<h2>Measures We Take</h2>
<ul>
<li>Semantic HTML for screen reader compatibility</li>
<li>Sufficient color contrast ratios across all text and UI elements</li>
<li>Full keyboard navigation support</li>
<li>Alt text for all informative images</li>
<li>Responsive design for all device sizes</li>
<li>Focus indicators for interactive elements</li>
<li>ARIA landmarks and labels where appropriate</li>
</ul>

<h2>Known Limitations</h2>
<p>While we strive for full accessibility, some older content may not yet meet all standards. We are actively working to remediate any issues.</p>

<h2>Feedback</h2>
<p>We welcome your feedback on our accessibility. If you encounter barriers, please contact us at <strong>accessibility@atomni.in</strong> or visit our <a href="/contact">Contact page</a>.</p>',
            ],
        ];

        foreach ($pages as $pageData) {
            Page::updateOrCreate(
                ['slug' => $pageData['slug']],
                $pageData
            );
        }
    }
}
