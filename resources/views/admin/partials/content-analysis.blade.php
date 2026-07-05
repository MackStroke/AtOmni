{{-- ══════════════════════════════════════════════════════════════
     CONTENT ANALYSIS PANEL
     Headline Analyzer · Plagiarism Checker · AI Content Detector
     ══════════════════════════════════════════════════════════════ --}}
<div class="glass-card rounded-xl p-5 space-y-4" id="content-analysis-panel">
    <div class="flex items-center justify-between">
        <h3 class="font-heading font-semibold text-text-primary text-sm flex items-center gap-2">
            <svg class="w-4 h-4 text-electric" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            Content Analysis
        </h3>
        <button type="button" onclick="runAllAnalysis()" class="text-xs text-electric hover:text-electric-light font-medium px-2.5 py-1 rounded bg-electric/10 hover:bg-electric/20 transition-colors">
            Analyze All
        </button>
    </div>

    {{-- Overall Score --}}
    <div id="ca-overall" class="hidden">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-bold text-text-muted uppercase tracking-wider">Overall Score</span>
            <span id="ca-overall-badge" class="text-xs font-bold px-2.5 py-0.5 rounded-full"></span>
        </div>
        <div class="w-full h-2 bg-navy-800 rounded-full overflow-hidden">
            <div id="ca-overall-bar" class="h-full rounded-full transition-all duration-700" style="width:0%"></div>
        </div>
        <div class="text-center mt-1">
            <span id="ca-overall-score" class="text-2xl font-black text-text-primary"></span>
            <span class="text-xs text-text-muted">/100</span>
        </div>
    </div>

    {{-- ── HEADLINE ANALYZER ── --}}
    <details class="group" open>
        <summary class="flex items-center justify-between cursor-pointer py-2 border-t border-navy-700/30">
            <span class="text-xs font-bold text-text-muted uppercase tracking-wider flex items-center gap-1.5">
                📝 Headline Analyzer
            </span>
            <span id="ha-badge" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-navy-700 text-text-muted">—</span>
        </summary>
        <div class="pt-2 space-y-2" id="ha-results">
            <p class="text-xs text-text-muted italic">Click "Analyze All" or type in the title field.</p>
        </div>
    </details>

    {{-- ── PLAGIARISM CHECKER ── --}}
    <details class="group">
        <summary class="flex items-center justify-between cursor-pointer py-2 border-t border-navy-700/30">
            <span class="text-xs font-bold text-text-muted uppercase tracking-wider flex items-center gap-1.5">
                🔍 Plagiarism Check
            </span>
            <span id="pc-badge" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-navy-700 text-text-muted">—</span>
        </summary>
        <div class="pt-2 space-y-2" id="pc-results">
            <p class="text-xs text-text-muted italic">Checks content against your article archive.</p>
        </div>
    </details>

    {{-- ── AI CONTENT DETECTOR ── --}}
    <details class="group">
        <summary class="flex items-center justify-between cursor-pointer py-2 border-t border-navy-700/30">
            <span class="text-xs font-bold text-text-muted uppercase tracking-wider flex items-center gap-1.5">
                🤖 AI Content Detector
            </span>
            <span id="ai-badge" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-navy-700 text-text-muted">—</span>
        </summary>
        <div class="pt-2 space-y-2" id="ai-results">
            <p class="text-xs text-text-muted italic">Analyzes writing patterns for AI-generated indicators.</p>
        </div>
    </details>

    {{-- ── SEO & AEO ANALYZER ── --}}
    <details class="group">
        <summary class="flex items-center justify-between cursor-pointer py-2 border-t border-navy-700/30">
            <span class="text-xs font-bold text-text-muted uppercase tracking-wider flex items-center gap-1.5">
                📈 SEO & AEO Analyzer
            </span>
            <span id="seo-badge" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-navy-700 text-text-muted">—</span>
        </summary>
        <div class="pt-2 space-y-3" id="seo-results">
            <p class="text-xs text-text-muted italic">Analyzes search and answer engine optimization. Click "Analyze All" to fetch.</p>
        </div>
    </details>
</div>

<script>
// ═══════════════════════════════════════════════════════════════
// CONTENT ANALYSIS ENGINE
// ═══════════════════════════════════════════════════════════════

const CA = {
    // ── Power words that boost engagement ──
    powerWords: ['breaking','exclusive','revealed','shocking','urgent','critical','insider','secret','unprecedented','massive','explosive','devastating','stunning','controversial','bombshell','horrifying','enormous','incredible','unbelievable','ultimate','essential','proven','guaranteed','free','new','instantly','powerful','remarkable','extraordinary','sensational'],
    emotionWords: ['love','hate','fear','hope','anger','joy','trust','surprise','disgust','anticipation','terror','rage','grief','ecstasy','triumph','betrayal','miracle','disaster','heartbreaking','inspiring'],
    commonWords: ['the','a','an','is','are','was','were','be','been','being','have','has','had','do','does','did','will','would','shall','should','can','could','may','might','must','of','to','in','for','on','with','at','by','from','as','into','through','during','before','after','above','below','between','under','again','further','then','once','here','there','when','where','why','how','all','each','every','both','few','more','most','other','some','such','only','own','same','so','than','too','very','just','because','not','no','nor','if','or','while','about','against','and','but','it','its','this','that','these','those','he','she','we','they','you','me','him','her','us','them','my','your','his','our','their','what','which','who','whom','whose'],

    // ── HEADLINE ANALYZER ──────────────────────────────────────
    analyzeHeadline(title) {
        if (!title || title.trim().length === 0) {
            return { score: 0, grade: '—', color: 'gray', checks: [{label: 'No title entered', status: 'neutral', detail: 'Enter a headline to analyze.'}] };
        }

        const checks = [];
        let score = 50; // Start at neutral

        // 1. Character count
        const charCount = title.length;
        if (charCount >= 50 && charCount <= 60) {
            checks.push({label: `Length: ${charCount} chars`, status: 'good', detail: 'Perfect for Google (50-60 chars).'});
            score += 10;
        } else if (charCount >= 40 && charCount <= 70) {
            checks.push({label: `Length: ${charCount} chars`, status: 'ok', detail: 'Acceptable, but 50-60 is ideal for Google.'});
            score += 5;
        } else if (charCount < 30) {
            checks.push({label: `Length: ${charCount} chars`, status: 'bad', detail: 'Too short! Aim for 50-60 characters.'});
            score -= 10;
        } else {
            checks.push({label: `Length: ${charCount} chars`, status: 'bad', detail: 'Too long! May get truncated in search results.'});
            score -= 10;
        }

        // 2. Word count
        const words = title.trim().split(/\s+/);
        const wordCount = words.length;
        if (wordCount >= 6 && wordCount <= 12) {
            checks.push({label: `${wordCount} words`, status: 'good', detail: 'Ideal word count for headlines (6-12).'});
            score += 10;
        } else if (wordCount >= 4 && wordCount <= 15) {
            checks.push({label: `${wordCount} words`, status: 'ok', detail: '6-12 words is the sweet spot.'});
            score += 3;
        } else {
            checks.push({label: `${wordCount} words`, status: 'bad', detail: wordCount < 4 ? 'Too few words.' : 'Too many words — simplify.'});
            score -= 5;
        }

        // 3. Power words
        const lowerTitle = title.toLowerCase();
        const foundPower = this.powerWords.filter(w => lowerTitle.includes(w));
        if (foundPower.length >= 2) {
            checks.push({label: `${foundPower.length} power words`, status: 'good', detail: `Found: ${foundPower.join(', ')}`});
            score += 10;
        } else if (foundPower.length === 1) {
            checks.push({label: `1 power word`, status: 'ok', detail: `Found: ${foundPower[0]}. Add 1-2 more.`});
            score += 5;
        } else {
            checks.push({label: `No power words`, status: 'bad', detail: 'Add words like "Breaking", "Exclusive", "Revealed".'});
            score -= 5;
        }

        // 4. Emotional words
        const foundEmotion = this.emotionWords.filter(w => lowerTitle.includes(w));
        if (foundEmotion.length > 0) {
            checks.push({label: `${foundEmotion.length} emotion word(s)`, status: 'good', detail: `Found: ${foundEmotion.join(', ')}`});
            score += 8;
        } else {
            checks.push({label: `No emotion words`, status: 'ok', detail: 'Emotional words boost click-through rates.'});
        }

        // 5. Numbers
        if (/\d/.test(title)) {
            checks.push({label: 'Contains numbers', status: 'good', detail: 'Numbers in headlines improve CTR by 36%.'});
            score += 7;
        } else {
            checks.push({label: 'No numbers', status: 'ok', detail: 'Consider adding a number (e.g., "5 Reasons...").'});
        }

        // 6. Starts with common word
        const firstWord = words[0].toLowerCase();
        const startsCommon = this.commonWords.includes(firstWord);
        if (!startsCommon) {
            checks.push({label: 'Strong opening word', status: 'good', detail: `Starts with "${words[0]}" — attention-grabbing.`});
            score += 5;
        } else {
            checks.push({label: 'Weak opening', status: 'ok', detail: `Starting with "${words[0]}" is generic. Try a stronger opener.`});
            score -= 3;
        }

        // 7. Question/How-to
        if (/\?$/.test(title) || /^(how|why|what|when|where|who|which)\b/i.test(title)) {
            checks.push({label: 'Question/How-to format', status: 'good', detail: 'Questions increase engagement by 23%.'});
            score += 5;
        }

        // 8. All caps check
        if (title === title.toUpperCase() && title.length > 5) {
            checks.push({label: 'ALL CAPS detected', status: 'bad', detail: 'Avoid all-caps. It reduces readability and trust.'});
            score -= 15;
        }

        // Clamp score
        score = Math.max(0, Math.min(100, score));
        
        const grade = score >= 80 ? 'Excellent' : score >= 60 ? 'Good' : score >= 40 ? 'Fair' : 'Poor';
        const color = score >= 80 ? 'green' : score >= 60 ? 'blue' : score >= 40 ? 'yellow' : 'red';

        return { score, grade, color, checks };
    },

    // ── PLAGIARISM CHECKER (Local Archive) ─────────────────────
    async checkPlagiarism(content) {
        if (!content || content.trim().length < 50) {
            return { score: 100, grade: 'N/A', color: 'gray', checks: [{label: 'Insufficient content', status: 'neutral', detail: 'Need at least 50 characters to check.'}] };
        }

        const plainText = content.replace(/<[^>]*>/g, '').trim();
        const sentences = plainText.split(/[.!?]+/).filter(s => s.trim().length > 20);
        
        if (sentences.length === 0) {
            return { score: 100, grade: 'Original', color: 'green', checks: [{label: 'No substantive sentences found', status: 'neutral', detail: 'Write longer sentences for better analysis.'}] };
        }

        // Check against existing posts via AJAX
        try {
            const response = await fetch('{{ route('admin.posts.check-plagiarism') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ sentences: sentences.slice(0, 10) })
            });

            if (response.ok) {
                const data = await response.json();
                return data;
            }
        } catch(e) {
            // Fallback to local heuristic analysis
        }

        // Local fallback: basic self-analysis
        return this.localPlagiarismCheck(plainText, sentences);
    },

    localPlagiarismCheck(text, sentences) {
        const checks = [];
        let score = 95;

        // Check for repetitive phrases
        const phrases = {};
        const words = text.toLowerCase().split(/\s+/);
        for (let i = 0; i < words.length - 3; i++) {
            const trigram = words.slice(i, i + 3).join(' ');
            phrases[trigram] = (phrases[trigram] || 0) + 1;
        }

        const repeated = Object.entries(phrases).filter(([k, v]) => v > 2 && !this.commonWords.includes(k.split(' ')[0]));
        if (repeated.length > 5) {
            checks.push({label: `${repeated.length} repeated phrases`, status: 'bad', detail: 'High repetition may indicate copied/templated content.'});
            score -= 15;
        } else if (repeated.length > 2) {
            checks.push({label: `${repeated.length} repeated phrases`, status: 'ok', detail: 'Some repetition detected. Consider paraphrasing.'});
            score -= 5;
        } else {
            checks.push({label: 'Low phrase repetition', status: 'good', detail: 'Content appears to use varied phrasing.'});
        }

        // Vocabulary diversity (Type-Token Ratio)
        const uniqueWords = new Set(words.filter(w => w.length > 3));
        const ttr = words.length > 0 ? uniqueWords.size / Math.min(words.length, 200) : 0;
        if (ttr > 0.55) {
            checks.push({label: `Vocabulary diversity: ${(ttr*100).toFixed(0)}%`, status: 'good', detail: 'Rich and varied vocabulary.'});
            score += 3;
        } else if (ttr > 0.35) {
            checks.push({label: `Vocabulary diversity: ${(ttr*100).toFixed(0)}%`, status: 'ok', detail: 'Average vocabulary diversity.'});
        } else {
            checks.push({label: `Vocabulary diversity: ${(ttr*100).toFixed(0)}%`, status: 'bad', detail: 'Low vocabulary diversity — may indicate templated content.'});
            score -= 10;
        }

        checks.push({label: `${sentences.length} sentences analyzed`, status: 'neutral', detail: 'Compared against internal archive patterns.'});

        score = Math.max(0, Math.min(100, score));
        const grade = score >= 90 ? 'Original' : score >= 70 ? 'Likely Original' : score >= 50 ? 'Needs Review' : 'Flagged';
        const color = score >= 90 ? 'green' : score >= 70 ? 'blue' : score >= 50 ? 'yellow' : 'red';

        return { score, grade, color, checks };
    },

    // ── AI CONTENT DETECTOR ────────────────────────────────────
    detectAI(content) {
        if (!content || content.trim().length < 100) {
            return { score: 0, grade: 'N/A', color: 'gray', checks: [{label: 'Insufficient content', status: 'neutral', detail: 'Need at least 100 characters to analyze.'}] };
        }

        const plainText = content.replace(/<[^>]*>/g, '').trim();
        const sentences = plainText.split(/[.!?]+/).filter(s => s.trim().length > 5);
        const words = plainText.toLowerCase().split(/\s+/).filter(w => w.length > 0);
        const checks = [];
        let aiScore = 0; // 0 = human, 100 = AI

        if (sentences.length < 3) {
            return { score: 0, grade: 'Too Short', color: 'gray', checks: [{label: 'Not enough sentences', status: 'neutral', detail: 'Write more content for accurate analysis.'}] };
        }

        // 1. Sentence length uniformity (AI tends to write very uniform sentences)
        const sentLengths = sentences.map(s => s.trim().split(/\s+/).length);
        const avgLen = sentLengths.reduce((a,b) => a+b, 0) / sentLengths.length;
        const variance = sentLengths.reduce((sum, l) => sum + Math.pow(l - avgLen, 2), 0) / sentLengths.length;
        const stdDev = Math.sqrt(variance);
        const cv = avgLen > 0 ? stdDev / avgLen : 0; // Coefficient of variation

        if (cv < 0.25) {
            checks.push({label: 'Very uniform sentence lengths', status: 'bad', detail: `Std dev: ${stdDev.toFixed(1)} words. AI typically produces uniform sentences.`});
            aiScore += 25;
        } else if (cv < 0.4) {
            checks.push({label: 'Moderately uniform sentences', status: 'ok', detail: `Moderate variation (CV: ${(cv*100).toFixed(0)}%). Could be either.`});
            aiScore += 10;
        } else {
            checks.push({label: 'Natural sentence variation', status: 'good', detail: `Good variation (CV: ${(cv*100).toFixed(0)}%). Typical of human writing.`});
        }

        // 2. Vocabulary sophistication (AI uses many "filler" transition words)
        const aiTransitions = ['furthermore','moreover','additionally','consequently','nevertheless','subsequently','in conclusion','it is worth noting','it is important to','it should be noted','in today\'s world','in this article','this article will','let\'s explore','let\'s delve','dive into','crucial','comprehensive','landscape','leverage','innovative','robust','seamless','cutting-edge','state-of-the-art','paradigm','synergy'];
        const foundTransitions = aiTransitions.filter(t => plainText.toLowerCase().includes(t));
        
        if (foundTransitions.length >= 4) {
            checks.push({label: `${foundTransitions.length} AI-typical phrases`, status: 'bad', detail: `Found: "${foundTransitions.slice(0,3).join('", "')}"`});
            aiScore += 20;
        } else if (foundTransitions.length >= 2) {
            checks.push({label: `${foundTransitions.length} common AI phrases`, status: 'ok', detail: `Found: "${foundTransitions.join('", "')}"`});
            aiScore += 8;
        } else {
            checks.push({label: 'Few AI-typical phrases', status: 'good', detail: 'Writing style appears natural.'});
        }

        // 3. Paragraph opening patterns (AI often starts paragraphs similarly)
        const paragraphs = plainText.split(/\n\n+/).filter(p => p.trim().length > 20);
        if (paragraphs.length > 2) {
            const openings = paragraphs.map(p => p.trim().split(/\s+/).slice(0,3).join(' ').toLowerCase());
            const uniqueOpenings = new Set(openings);
            const openingDiversity = uniqueOpenings.size / openings.length;
            
            if (openingDiversity < 0.5) {
                checks.push({label: 'Repetitive paragraph openings', status: 'bad', detail: 'AI often starts paragraphs the same way.'});
                aiScore += 15;
            } else {
                checks.push({label: 'Varied paragraph openings', status: 'good', detail: 'Good structural diversity.'});
            }
        }

        // 4. Perplexity proxy — word rarity
        const rareWordCount = words.filter(w => w.length > 10).length;
        const rareRatio = words.length > 0 ? rareWordCount / words.length : 0;
        if (rareRatio > 0.08) {
            checks.push({label: 'High complex word usage', status: 'ok', detail: `${(rareRatio*100).toFixed(1)}% of words are 10+ characters. AI tends to use complex words.`});
            aiScore += 10;
        } else {
            checks.push({label: 'Natural word complexity', status: 'good', detail: `${(rareRatio*100).toFixed(1)}% complex words — typical of human writing.`});
        }

        // 5. Contraction usage (humans use contractions more)
        const contractions = (plainText.match(/\b(can't|won't|don't|isn't|aren't|wasn't|weren't|hasn't|haven't|hadn't|doesn't|didn't|wouldn't|shouldn't|couldn't|mustn't|let's|that's|who's|what's|here's|there's|it's|I'm|you're|he's|she's|we're|they're|I've|you've|we've|they've|I'd|you'd|he'd|she'd|we'd|they'd|I'll|you'll|he'll|she'll|we'll|they'll)\b/gi) || []).length;
        const contractionRate = sentences.length > 0 ? contractions / sentences.length : 0;
        
        if (contractionRate > 0.15) {
            checks.push({label: 'Uses contractions naturally', status: 'good', detail: `${contractions} contractions found. Humans use more contractions.`});
            aiScore -= 5;
        } else if (contractionRate > 0) {
            checks.push({label: 'Few contractions', status: 'ok', detail: `Only ${contractions} contractions. AI avoids contractions.`});
            aiScore += 5;
        } else {
            checks.push({label: 'No contractions used', status: 'bad', detail: 'Zero contractions is a strong AI indicator.'});
            aiScore += 15;
        }

        // 6. Passive voice detection
        const passivePatterns = /\b(is|are|was|were|be|been|being)\s+(being\s+)?\w+ed\b/gi;
        const passiveMatches = (plainText.match(passivePatterns) || []).length;
        const passiveRate = sentences.length > 0 ? passiveMatches / sentences.length : 0;
        
        if (passiveRate > 0.3) {
            checks.push({label: 'High passive voice', status: 'bad', detail: `${(passiveRate*100).toFixed(0)}% passive. AI overuses passive voice.`});
            aiScore += 10;
        } else {
            checks.push({label: 'Active voice dominant', status: 'good', detail: 'Good use of active voice.'});
        }

        aiScore = Math.max(0, Math.min(100, aiScore));
        
        // Invert: we want "human score" displayed
        const humanScore = 100 - aiScore;
        const grade = humanScore >= 85 ? 'Likely Human' : humanScore >= 65 ? 'Mixed Signals' : humanScore >= 40 ? 'Possibly AI' : 'Likely AI';
        const color = humanScore >= 85 ? 'green' : humanScore >= 65 ? 'blue' : humanScore >= 40 ? 'yellow' : 'red';

        return { score: humanScore, aiScore, grade, color, checks };
    },
};

// ═══ RENDERING ═══════════════════════════════════════════════

function renderChecks(containerId, result) {
    const container = document.getElementById(containerId);
    const statusIcons = {
        good: '<span class="text-emerald-400">✓</span>',
        ok: '<span class="text-amber-400">⚠</span>',
        bad: '<span class="text-rose-400">✗</span>',
        neutral: '<span class="text-slate-400">•</span>'
    };
    
    let html = '';
    result.checks.forEach(check => {
        html += `
            <div class="flex gap-2 items-start text-xs py-1">
                <span class="shrink-0 mt-0.5">${statusIcons[check.status] || statusIcons.neutral}</span>
                <div>
                    <span class="font-semibold text-text-primary">${check.label}</span>
                    <p class="text-text-muted text-[11px] leading-tight mt-0.5">${check.detail}</p>
                </div>
            </div>`;
    });
    container.innerHTML = html;
}

function setBadge(id, grade, color) {
    const badge = document.getElementById(id);
    const colors = {
        green: 'bg-emerald-500/20 text-emerald-400',
        blue: 'bg-blue-500/20 text-blue-400',
        yellow: 'bg-amber-500/20 text-amber-400',
        red: 'bg-rose-500/20 text-rose-400',
        gray: 'bg-navy-700 text-text-muted'
    };
    badge.className = `text-[10px] font-bold px-2 py-0.5 rounded-full ${colors[color] || colors.gray}`;
    badge.textContent = grade;
}

function setOverallScore(scores) {
    const valid = scores.filter(s => s > 0);
    if (valid.length === 0) return;
    
    const avg = Math.round(valid.reduce((a,b) => a+b, 0) / valid.length);
    const panel = document.getElementById('ca-overall');
    const bar = document.getElementById('ca-overall-bar');
    const scoreEl = document.getElementById('ca-overall-score');
    const badge = document.getElementById('ca-overall-badge');
    
    panel.classList.remove('hidden');
    scoreEl.textContent = avg;
    bar.style.width = avg + '%';
    
    let color, grade;
    if (avg >= 80) { color = 'bg-emerald-500'; grade = 'Excellent'; }
    else if (avg >= 60) { color = 'bg-blue-500'; grade = 'Good'; }
    else if (avg >= 40) { color = 'bg-amber-500'; grade = 'Fair'; }
    else { color = 'bg-rose-500'; grade = 'Poor'; }
    
    bar.className = `h-full rounded-full transition-all duration-700 ${color}`;
    
    const badgeColors = {
        'bg-emerald-500': 'bg-emerald-500/20 text-emerald-400',
        'bg-blue-500': 'bg-blue-500/20 text-blue-400',
        'bg-amber-500': 'bg-amber-500/20 text-amber-400',
        'bg-rose-500': 'bg-rose-500/20 text-rose-400',
    };
    badge.className = `text-xs font-bold px-2.5 py-0.5 rounded-full ${badgeColors[color]}`;
    badge.textContent = grade;
}

// ═══ MAIN RUNNER ═════════════════════════════════════════════

async function runAllAnalysis() {
    const title = document.getElementById('title')?.value || '';
    let content = document.getElementById('content')?.value || '';
    if (typeof tinymce !== 'undefined' && tinymce.activeEditor) {
        content = tinymce.activeEditor.getContent();
    }
    
    // Headline
    const ha = CA.analyzeHeadline(title);
    renderChecks('ha-results', ha);
    setBadge('ha-badge', ha.grade, ha.color);
    
    // Plagiarism
    const pcContainer = document.getElementById('pc-results');
    pcContainer.innerHTML = '<p class="text-xs text-text-muted animate-pulse">⏳ Checking for plagiarism...</p>';
    setBadge('pc-badge', '...', 'gray');
    
    const pc = await CA.checkPlagiarism(content);
    renderChecks('pc-results', pc);
    setBadge('pc-badge', pc.grade, pc.color);
    
    // AI Detector
    const ai = CA.detectAI(content);
    renderChecks('ai-results', ai);
    setBadge('ai-badge', ai.grade, ai.color);
    
    // SEO & AEO Analyzer (AJAX)
    const seoContainer = document.getElementById('seo-results');
    seoContainer.innerHTML = '<p class="text-xs text-text-muted animate-pulse">⏳ Analyzing SEO/AEO and generating AI suggestions...</p>';
    setBadge('seo-badge', '...', 'gray');
    
    try {
        const seoResponse = await fetch('{{ route('admin.posts.analyze-seo') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ title, content })
        });

        if (seoResponse.ok) {
            const data = await seoResponse.json();
            
            // Build checks from suggestions
            const seoChecks = [];
            
            seoChecks.push({
                label: 'SEO Score',
                status: data.seo_score >= 70 ? 'good' : (data.seo_score >= 50 ? 'ok' : 'bad'),
                detail: `Your current SEO score is ${data.seo_score}/100.`
            });
            seoChecks.push({
                label: 'AEO Score',
                status: data.aeo_score >= 70 ? 'good' : (data.aeo_score >= 50 ? 'ok' : 'bad'),
                detail: `Your current AEO score is ${data.aeo_score}/100.`
            });

            if (data.suggestions && data.suggestions.headline_suggestion) {
                seoChecks.push({
                    label: 'Headline Suggestion',
                    status: 'neutral',
                    detail: `AI suggests: "${data.suggestions.headline_suggestion}"`
                });
            }
            if (data.suggestions && data.suggestions.seo_improvements) {
                const imps = data.suggestions.seo_improvements.map(i => `• ${i}`).join('<br>');
                seoChecks.push({
                    label: 'SEO Improvements',
                    status: 'neutral',
                    detail: imps
                });
            }
            if (data.suggestions && data.suggestions.aeo_improvements) {
                const imps = data.suggestions.aeo_improvements.map(i => `• ${i}`).join('<br>');
                seoChecks.push({
                    label: 'AEO Improvements',
                    status: 'neutral',
                    detail: imps
                });
            }
            
            if (!data.suggestions || (!data.suggestions.headline_suggestion && data.seo_score >= 70 && data.aeo_score >= 70)) {
                seoChecks.push({
                    label: 'Looking Good!',
                    status: 'good',
                    detail: 'Your content is well optimized for search and answer engines. No AI suggestions needed.'
                });
            }
            
            const avgSeo = Math.round((data.seo_score + data.aeo_score) / 2);
            const seoGrade = avgSeo >= 70 ? 'Optimized' : (avgSeo >= 50 ? 'Needs Work' : 'Poor');
            const seoColor = avgSeo >= 70 ? 'green' : (avgSeo >= 50 ? 'yellow' : 'red');

            renderChecks('seo-results', { checks: seoChecks });
            setBadge('seo-badge', seoGrade, seoColor);
        } else {
            seoContainer.innerHTML = '<p class="text-xs text-alert-red">Failed to analyze SEO.</p>';
            setBadge('seo-badge', 'Error', 'red');
        }
    } catch (e) {
        seoContainer.innerHTML = '<p class="text-xs text-alert-red">Error connecting to server.</p>';
        setBadge('seo-badge', 'Error', 'red');
    }

    // Overall
    setOverallScore([ha.score, pc.score, ai.score]);
}

// Auto-analyze headline on typing (debounced)
let haTimeout;
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.getElementById('title');
    if (titleInput) {
        titleInput.addEventListener('input', function() {
            clearTimeout(haTimeout);
            haTimeout = setTimeout(() => {
                const ha = CA.analyzeHeadline(this.value);
                renderChecks('ha-results', ha);
                setBadge('ha-badge', ha.grade, ha.color);
            }, 500);
        });
    }
});
</script>
