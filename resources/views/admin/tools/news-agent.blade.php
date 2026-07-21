@extends('admin.layouts.app')
@section('title', 'AI News Agent Manager')
@section('page-title', 'AI News Agent Manager')

@section('content')
<div class="mb-6 lg:mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="page-title text-2xl sm:text-3xl font-bold tracking-tight text-text-primary">AI News Agent</h1>
        <p class="text-sm sm:text-base text-text-muted mt-1 sm:mt-2">Deploy, configure, and monitor your autonomous AI-powered news research & publishing agent.</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start max-w-7xl">
    
    <!-- Left Column: Settings and Controls -->
    <div class="lg:col-span-5 space-y-6">
        
        <!-- Action Trigger Card -->
        <div class="glass-card rounded-2xl p-6 border border-navy-700/50 bg-gradient-to-br from-navy-900/80 to-navy-950/80">
            <h3 class="font-heading font-semibold text-text-primary text-lg mb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-accent-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                Trigger News Agent
            </h3>
            <p class="text-xs text-text-muted mb-4">
                Run the agent manually on-demand. It will research current breaking news via Google Search, write the article, resolve/generate images, decide featured status, and publish.
            </p>

            <div class="space-y-4">
                <div>
                    <label for="run_category" class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Target Category</label>
                    <div class="relative">
                        <select id="run_category" class="block w-full px-4 py-2.5 rounded-xl bg-navy-950/40 border border-navy-700/50 text-text-primary focus:ring-2 focus:ring-accent-blue focus:border-transparent transition-all font-medium appearance-none">
                            <option value="">All Configured Categories (Sequential)</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-text-muted">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3 rounded-xl bg-navy-950/30 border border-navy-800/60">
                    <div class="flex flex-col">
                        <span class="text-xs font-semibold text-text-secondary">Dry Run Mode</span>
                        <span class="text-[10px] text-text-muted">Perform research and draft content without saving to the DB.</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="dry_run" class="sr-only peer">
                        <div class="w-9 h-5 bg-navy-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-text-secondary after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-accent-blue"></div>
                    </label>
                </div>

                <button id="run-agent-btn" onclick="runAgent()" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-bold text-white bg-gradient-to-r from-accent-blue to-purple-600 rounded-xl hover:from-accent-blue-hover hover:to-purple-700 transition-all shadow-lg shadow-accent-blue/20 focus:outline-none focus:ring-2 focus:ring-accent-blue">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>Run News Agent Now</span>
                </button>
            </div>
        </div>

        <!-- Configuration Settings Card -->
        <div class="glass-card rounded-2xl p-6 border border-navy-700/50 bg-gradient-to-br from-navy-900/80 to-navy-950/80">
            <h3 class="font-heading font-semibold text-text-primary text-lg mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                </svg>
                Agent Scheduler &amp; Scope
            </h3>

            <form method="POST" action="{{ route('admin.tools.news-agent.settings') }}" class="space-y-5">
                @csrf
                
                <!-- Toggle Automated Runs -->
                <div class="flex items-center justify-between pb-4 border-b border-navy-800/40">
                    <div class="flex flex-col">
                        <span class="text-sm font-semibold text-text-primary">Automated Daily Agent</span>
                        <span class="text-xs text-text-muted">Research and publish articles on a daily schedule.</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="news_agent_enabled" value="false">
                        <input type="checkbox" name="news_agent_enabled" value="true"
                               {{ $settings['news_agent_enabled'] === 'true' ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-navy-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-text-secondary after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                    </label>
                </div>

                <!-- Last Run Indicator -->
                <div class="flex justify-between items-center text-xs">
                    <span class="text-text-muted">Last Execution:</span>
                    <span class="font-mono text-text-secondary bg-navy-950/40 px-2.5 py-1 rounded border border-navy-800/50">
                        {{ $settings['news_agent_last_run_at'] }}
                    </span>
                </div>

                <!-- Categories Checklist -->
                <div>
                    <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-2">Daily Active Categories</label>
                    <p class="text-[11px] text-text-muted mb-3">Select the categories the automated agent should write for daily.</p>
                    
                    @php
                        $activeSlugs = array_filter(array_map('trim', explode(',', $settings['news_agent_categories'])));
                        if (empty($activeSlugs)) {
                            // default fallback categories
                            $activeSlugs = ['world', 'politics', 'technology', 'business', 'science', 'sports', 'health', 'entertainment'];
                        }
                    @endphp

                    <div class="grid grid-cols-2 gap-2 max-h-56 overflow-y-auto pr-1 bg-navy-950/30 border border-navy-850/50 rounded-xl p-3 scrollbar-thin">
                        @foreach($categories as $cat)
                            <label class="flex items-center gap-2 p-1.5 hover:bg-navy-900/40 rounded transition-colors cursor-pointer text-xs">
                                <input type="checkbox" name="news_agent_categories[]" value="{{ $cat->slug }}"
                                       {{ in_array($cat->slug, $activeSlugs) ? 'checked' : '' }}
                                       class="w-4 h-4 rounded border-navy-700 bg-navy-900 text-emerald-500 focus:ring-emerald-500 focus:ring-offset-navy-950">
                                <span class="text-text-secondary truncate">{{ $cat->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="w-full btn-primary py-2.5 text-xs font-semibold">
                    Save Agent Configuration
                </button>
            </form>
        </div>

    </div>

    <!-- Right Column: Live Logs Terminal and History -->
    <div class="lg:col-span-7 space-y-6">
        
        <!-- Live Terminal logs -->
        <div class="glass-card rounded-2xl overflow-hidden border border-navy-700/50 flex flex-col h-[400px]">
            <div class="bg-navy-950/90 px-4 py-3 flex items-center justify-between border-b border-navy-850">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                    <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                    <span class="w-3 h-3 rounded-full bg-green-500"></span>
                    <span class="text-xs font-mono font-bold text-text-secondary ml-2">agent-output.log</span>
                </div>
                <div id="terminal-badge" class="hidden text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded bg-accent-blue/10 text-accent-blue animate-pulse">
                    Running...
                </div>
            </div>
            
            <div id="terminal" class="bg-black/95 text-green-400 p-4 font-mono text-xs overflow-y-auto flex-1 leading-relaxed whitespace-pre-wrap selection:bg-green-800/40 selection:text-white">=== AI News Agent Status Console ===
No active runs. Configured command runs daily at 04:00. Click "Run News Agent Now" to trigger manually.</div>
        </div>

        <!-- Recent articles history -->
        <div class="glass-card rounded-2xl p-6 border border-navy-700/50">
            <h3 class="font-heading font-semibold text-text-primary text-lg mb-4">Latest Published Articles</h3>
            
            <div class="divide-y divide-navy-850">
                @forelse($recentPosts as $post)
                    <div class="py-3.5 flex items-center justify-between gap-4">
                        <div class="min-w-0">
                            <h4 class="text-sm font-semibold text-text-primary truncate hover:text-accent-blue transition-colors">
                                <a href="{{ route('frontend.article', $post->slug) }}" target="_blank">
                                    {{ $post->title }}
                                </a>
                            </h4>
                            <div class="flex items-center gap-2 mt-1 text-[11px] text-text-muted">
                                <span class="px-2 py-0.5 rounded bg-navy-800 text-text-secondary font-medium">
                                    {{ $post->category->name ?? 'Uncategorized' }}
                                </span>
                                <span>&bull;</span>
                                <span>By {{ $post->author->name ?? 'System' }}</span>
                                <span>&bull;</span>
                                <span>{{ $post->published_at ? $post->published_at->diffForHumans() : 'Draft' }}</span>
                            </div>
                        </div>
                        <div class="shrink-0 flex items-center gap-2">
                            @if($post->is_featured)
                                <span class="text-[10px] bg-amber-500/10 border border-amber-500/30 text-amber-400 px-2 py-0.5 rounded-full font-bold uppercase tracking-wider"
                                      title="Featured until {{ $post->featured_until ? $post->featured_until->format('d M Y, H:i') : 'indefinitely' }}">
                                    Featured
                                </span>
                            @endif
                            <a href="{{ route('admin.posts.edit', $post) }}" class="p-1 text-text-muted hover:text-text-primary transition-colors" title="Edit Post">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-text-muted py-4 text-center">No articles published yet.</p>
                @endforelse
            </div>
        </div>

    </div>

</div>

@section('scripts')
<script>
    let logInterval = null;
    
    function startPollingLogs() {
        const terminal = document.getElementById('terminal');
        const badge = document.getElementById('terminal-badge');
        
        badge.classList.remove('hidden');
        terminal.textContent = 'Launching News Agent. Initializing search grounding...';
        
        if (logInterval) clearInterval(logInterval);
        
        // Poll logs every 2 seconds
        logInterval = setInterval(async () => {
            try {
                const res = await fetch('{{ route('admin.tools.news-agent.logs') }}');
                const data = await res.json();
                if (data.logs) {
                    terminal.textContent = data.logs;
                    // Auto scroll to bottom
                    terminal.scrollTop = terminal.scrollHeight;
                }
            } catch (err) {
                console.error('Error fetching logs:', err);
            }
        }, 2000);
    }
    
    function stopPollingLogs() {
        if (logInterval) {
            clearInterval(logInterval);
            logInterval = null;
        }
        document.getElementById('terminal-badge').classList.add('hidden');
    }

    async function runAgent() {
        const btn = document.getElementById('run-agent-btn');
        const categorySelect = document.getElementById('run-category');
        const dryRunCheck = document.getElementById('dry_run');
        
        const category = categorySelect.value;
        const dryRun = dryRunCheck.checked ? 1 : 0;
        
        btn.disabled = true;
        btn.classList.add('opacity-60', 'cursor-not-allowed');
        
        startPollingLogs();
        
        try {
            const response = await fetch('{{ route('admin.tools.news-agent.run') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    category: category,
                    dry_run: dryRun
                })
            });
            
            const data = await response.json();
            
            // Final logs fetch
            const logRes = await fetch('{{ route('admin.tools.news-agent.logs') }}');
            const logData = await logRes.json();
            if (logData.logs) {
                document.getElementById('terminal').textContent = logData.logs;
                document.getElementById('terminal').scrollTop = document.getElementById('terminal').scrollHeight;
            }
            
            if (data.success) {
                alert('News Agent completed the run successfully!');
            } else {
                alert('Agent error: ' + data.message);
            }
        } catch (err) {
            alert('Request failed: ' + err.message);
        } finally {
            stopPollingLogs();
            btn.disabled = false;
            btn.classList.remove('opacity-60', 'cursor-not-allowed');
        }
    }
</script>
@endsection

@endsection
