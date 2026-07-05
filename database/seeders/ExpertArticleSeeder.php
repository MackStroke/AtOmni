<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;

class ExpertArticleSeeder extends Seeder
{
    public function run()
    {
        $category = Category::first();
        $categoryId = $category ? $category->id : null;
        
        $user = User::first();
        $authorId = $user ? $user->id : null;

        $title = 'How to Build a Safe Human-in-the-Loop AI Workflow';
        
        // Prevent duplicate seeding
        if (Post::where('slug', Str::slug($title))->exists()) {
            $this->command->info('Expert article already exists.');
            return;
        }

        $content = "
<p>AI workflow automation is transforming service businesses, but fully autonomous AI is not the goal. When you are processing legal documents, medical intake forms, or financial data, AI cannot be allowed to fail silently or hallucinate without oversight.</p>

<p>The solution is the <strong>Human-in-the-Loop (HITL)</strong> workflow.</p>

<h2>What is a Human-in-the-Loop Workflow?</h2>
<p>A HITL workflow is an automated process where an AI handles the heavy lifting (reading, extracting, categorizing), but routes uncertain or high-risk decisions to a human for final approval.</p>
<p>For example, if an AI is 99% confident in extracting a client's name from an email, it pushes the data to the CRM automatically. If it's only 70% confident about an unstructured contract clause, it flags it in a review queue for a human operator.</p>

<h2>Why AI Fails Without Human Review</h2>
<p>Generic automation tools (like Zapier connected to ChatGPT) lack native confidence scoring. They take the AI's output as absolute truth and pass it down the line. If the AI hallucinates a date or misunderstands a clause, that error goes straight into your database.</p>

<h2>How to Implement HITL Safely</h2>
<ol>
<li><strong>Define Confidence Thresholds:</strong> Determine what level of accuracy is acceptable for each specific data point.</li>
<li><strong>Build a Review UI:</strong> Human reviewers need context. If they are asked to verify an extracted invoice amount, they must see the original invoice side-by-side with the AI's extraction.</li>
<li><strong>Create Fallback Paths:</strong> If the AI completely fails to process a document, the workflow should gracefully degrade to a manual task assignment, not crash the system.</li>
<li><strong>Audit Trails:</strong> Log every action. You must know whether the AI or a human made a specific data entry.</li>
</ol>

<p>At Atomni, we build these safeguards natively into every automation we deploy for our clients. Ready to automate safely? <a href=\"/contact\">Request a custom demo today.</a></p>
";

        Post::create([
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => 'Why fully autonomous AI is dangerous for service businesses, and how to implement safe human-in-the-loop workflows.',
            'content' => $content,
            'category_id' => $categoryId,
            'author_id' => $authorId,
            'status' => 'published',
            'published_at' => now(),
            'reading_time' => 3,
        ]);
        
        $this->command->info('Expert article seeded successfully.');
    }
}
