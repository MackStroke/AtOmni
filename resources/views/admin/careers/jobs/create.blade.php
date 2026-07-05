@extends('admin.layouts.app')
@section('title', 'Create Job Posting')
@section('page-title', 'Create Job Posting')
@section('content')

<div class="page-header mb-6">
    <div class="flex items-center gap-3 min-w-0">
        <a href="{{ route('admin.careers.jobs.index') }}" class="p-2 -ml-2 text-text-muted hover:text-text-primary hover:bg-navy-800 rounded-xl transition-all shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-text-primary page-title">Create Job Posting</h1>
    </div>
</div>

<form method="POST" action="{{ route('admin.careers.jobs.store') }}" class="max-w-5xl">
    @csrf

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Main content --}}
        <div class="lg:col-span-2 space-y-5">
            <div>
                <label for="title" class="block text-sm font-medium text-text-secondary mb-1.5">Job Title</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required
                       class="w-full px-4 py-2.5 rounded-lg bg-navy-800/50 border border-navy-700/50 text-text-primary placeholder-text-muted focus:border-electric focus:outline-none focus:ring-1 focus:ring-electric/30 transition-colors"
                       placeholder="e.g. Senior Backend Engineer">
                @error('title') <p class="mt-1 text-xs text-alert-red">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="department" class="block text-sm font-medium text-text-secondary mb-1.5">Department</label>
                    <input type="text" id="department" name="department" value="{{ old('department') }}"
                           class="w-full px-4 py-2.5 rounded-lg bg-navy-800/50 border border-navy-700/50 text-text-primary placeholder-text-muted focus:border-electric focus:outline-none focus:ring-1 focus:ring-electric/30 transition-colors"
                           placeholder="e.g. Engineering">
                </div>
                <div>
                    <label for="location" class="block text-sm font-medium text-text-secondary mb-1.5">Location</label>
                    <input type="text" id="location" name="location" value="{{ old('location') }}"
                           class="w-full px-4 py-2.5 rounded-lg bg-navy-800/50 border border-navy-700/50 text-text-primary placeholder-text-muted focus:border-electric focus:outline-none focus:ring-1 focus:ring-electric/30 transition-colors"
                           placeholder="e.g. Remote, San Francisco">
                </div>
            </div>

            {{-- Description --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="description" class="block text-sm font-medium text-text-secondary">Job Description</label>
                    <button type="button" onclick="aiGenerate('description')" class="ai-gen-btn text-[11px] font-medium px-2.5 py-1 rounded-lg bg-purple-500/15 text-purple-400 hover:bg-purple-500/25 hover:text-purple-300 transition-colors flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Write with AI
                    </button>
                </div>
                <textarea id="description" name="description" rows="5" required
                          class="w-full px-4 py-2.5 rounded-lg bg-navy-800/50 border border-navy-700/50 text-text-primary placeholder-text-muted focus:border-electric focus:outline-none focus:ring-1 focus:ring-electric/30 transition-colors text-sm summernote-editor"
                          placeholder="Describe the role...">{{ old('description') }}</textarea>
                @error('description') <p class="mt-1 text-xs text-alert-red">{{ $message }}</p> @enderror
            </div>

            {{-- Requirements --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="requirements" class="block text-sm font-medium text-text-secondary">Requirements</label>
                    <button type="button" onclick="aiGenerate('requirements')" class="ai-gen-btn text-[11px] font-medium px-2.5 py-1 rounded-lg bg-purple-500/15 text-purple-400 hover:bg-purple-500/25 hover:text-purple-300 transition-colors flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Write with AI
                    </button>
                </div>
                <textarea id="requirements" name="requirements" rows="4"
                          class="w-full px-4 py-2.5 rounded-lg bg-navy-800/50 border border-navy-700/50 text-text-primary placeholder-text-muted focus:border-electric focus:outline-none focus:ring-1 focus:ring-electric/30 transition-colors text-sm summernote-editor"
                          placeholder="List key requirements...">{{ old('requirements') }}</textarea>
            </div>

            {{-- Benefits --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="benefits" class="block text-sm font-medium text-text-secondary">Benefits</label>
                    <button type="button" onclick="aiGenerate('benefits')" class="ai-gen-btn text-[11px] font-medium px-2.5 py-1 rounded-lg bg-purple-500/15 text-purple-400 hover:bg-purple-500/25 hover:text-purple-300 transition-colors flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Write with AI
                    </button>
                </div>
                <textarea id="benefits" name="benefits" rows="4"
                          class="w-full px-4 py-2.5 rounded-lg bg-navy-800/50 border border-navy-700/50 text-text-primary placeholder-text-muted focus:border-electric focus:outline-none focus:ring-1 focus:ring-electric/30 transition-colors text-sm summernote-editor"
                          placeholder="List benefits...">{{ old('benefits') }}</textarea>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">
            <div class="glass-card rounded-xl p-5 space-y-4">
                <h3 class="font-heading font-semibold text-text-primary text-sm">Publish Setup</h3>
                <div>
                    <label for="status" class="block text-xs font-medium text-text-muted mb-1">Status</label>
                    <select id="status" name="status" class="w-full px-3 py-2 rounded-lg bg-navy-800/50 border border-navy-700/50 text-sm text-text-primary focus:border-electric focus:outline-none transition-colors">
                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published (Active)</option>
                        <option value="closed" {{ old('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                
                <div>
                    <label for="type" class="block text-xs font-medium text-text-muted mb-1">Employment Type</label>
                    <select id="type" name="type" class="w-full px-3 py-2 rounded-lg bg-navy-800/50 border border-navy-700/50 text-sm text-text-primary focus:border-electric focus:outline-none transition-colors">
                        <option value="full-time" {{ old('type') === 'full-time' ? 'selected' : '' }}>Full-Time</option>
                        <option value="part-time" {{ old('type') === 'part-time' ? 'selected' : '' }}>Part-Time</option>
                        <option value="contract" {{ old('type') === 'contract' ? 'selected' : '' }}>Contract</option>
                    </select>
                </div>

                <div>
                    <label for="closing_date" class="block text-xs font-medium text-text-muted mb-1">Closing Date (Optional)</label>
                    <input type="datetime-local" id="closing_date" name="closing_date" value="{{ old('closing_date') }}"
                           class="w-full px-3 py-2 rounded-lg bg-navy-800/50 border border-navy-700/50 text-sm text-text-primary placeholder-text-muted focus:border-electric focus:outline-none transition-colors">
                </div>
                
                <button type="submit" class="w-full px-4 py-2.5 mt-2 rounded-lg bg-electric hover:bg-electric-light text-white text-sm font-semibold transition-all shadow-lg shadow-electric/20">
                    Create Job Posting
                </button>
            </div>
        </div>
    </div>
</form>

@section('scripts')
@include('admin.partials.editor-scripts')
<script>
function aiGenerate(field) {
    const title = document.getElementById('title').value.trim();
    const department = document.getElementById('department').value.trim();
    const location = document.getElementById('location').value.trim();

    if (!title) {
        alert('Please enter a Job Title first so AI can generate relevant content.');
        return;
    }

    const btn = event.target.closest('.ai-gen-btn');
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg> Generating…';
    btn.disabled = true;

    const context = `${title}${department ? ' in the ' + department + ' department' : ''}${location ? ', located at ' + location : ''}`;
    
    let generated = '';
    
    if (field === 'description') {
        generated = `<h3>About the Role</h3>
<p>We are looking for a talented <strong>${title}</strong>${department ? ' to join our <strong>' + department + '</strong> team' : ''}${location ? ' based in <strong>' + location + '</strong>' : ''}. This role is critical to driving our mission forward, and you'll have the opportunity to work on high-impact projects that shape the future of our organization.</p>

<h3>What You'll Do</h3>
<ul>
<li>Lead and contribute to key initiatives within the ${department || 'team'}, delivering measurable results</li>
<li>Collaborate cross-functionally with stakeholders to define and execute strategic goals</li>
<li>Design, develop, and optimize workflows and systems for maximum efficiency</li>
<li>Mentor junior team members and foster a culture of continuous improvement</li>
<li>Stay ahead of industry trends and bring innovative ideas to the table</li>
</ul>

<h3>Why Join Us?</h3>
<p>We offer a dynamic work environment where creativity and initiative are valued. As a ${title}, you'll have direct influence on our products and culture.</p>`;
    } else if (field === 'requirements') {
        generated = `<h3>Required Qualifications</h3>
<ul>
<li>3–5+ years of relevant experience in a similar role</li>
<li>Proven track record of success in ${department || 'the field'}</li>
<li>Strong communication and collaboration skills</li>
<li>Ability to work independently and manage multiple priorities</li>
<li>Bachelor's degree in a related field, or equivalent practical experience</li>
</ul>

<h3>Nice to Have</h3>
<ul>
<li>Experience in a fast-paced startup or scale-up environment</li>
<li>Advanced degree (Master's, MBA, etc.)</li>
<li>Familiarity with modern tools and frameworks relevant to ${department || 'the role'}</li>
<li>Leadership or mentoring experience</li>
</ul>`;
    } else if (field === 'benefits') {
        generated = `<ul>
<li>💰 Competitive salary with performance-based bonuses</li>
<li>🏥 Comprehensive health, dental, and vision insurance</li>
<li>🏖️ Generous PTO (25+ days) plus public holidays</li>
<li>💻 Latest equipment and home office stipend</li>
<li>📚 Learning & development budget ($1,500/year)</li>
<li>🧘 Mental health and wellness programs</li>
<li>${location && location.toLowerCase().includes('remote') ? '🌍 Fully remote with flexible working hours' : '🚗 Commuter benefits and free parking'}</li>
<li>🎉 Regular team events, offsites, and social activities</li>
<li>📈 Equity/stock options for all full-time employees</li>
</ul>`;
    }

    setTimeout(() => {
        document.getElementById(field).value = generated;
        // Trigger TinyMCE update if available
        if (typeof tinymce !== 'undefined') {
            const editor = tinymce.get(field);
            if (editor) editor.setContent(generated);
        }
        btn.innerHTML = originalHTML;
        btn.disabled = false;
    }, 800);
}
</script>
@endsection

@endsection
