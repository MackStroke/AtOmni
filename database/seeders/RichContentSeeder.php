<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\JobPosting;
use App\Models\Post;
use App\Models\Setting;
use App\Models\Tag;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RichContentSeeder extends Seeder
{
    public function run(): void
    {
        // ── Additional Authors ────────────────────────────────
        $admin = User::where('email', 'admin@atomni.com')->first()
            ?? User::create(['name' => 'Admin', 'email' => 'admin@atomni.com', 'password' => bcrypt('password'), 'role' => 'super_admin']);

        $authors = collect([
            ['name' => 'Manoj Kumar', 'email' => 'manoj@atomni.com', 'role' => 'editor', 'bio' => 'Senior Political Correspondent with 15 years of experience covering Indian politics and governance.'],
            ['name' => 'Priya Sharma', 'email' => 'priya@atomni.com', 'role' => 'author', 'bio' => 'Technology editor specialising in AI, startups, and India\'s digital economy.'],
            ['name' => 'Rohan Desai', 'email' => 'rohan@atomni.com', 'role' => 'author', 'bio' => 'Sports journalist covering cricket, IPL, and Olympic sports across South Asia.'],
            ['name' => 'Ananya Iyer', 'email' => 'ananya@atomni.com', 'role' => 'author', 'bio' => 'Business and economy reporter tracking markets, startups, and infrastructure.'],
            ['name' => 'Vikram Singh', 'email' => 'vikram@atomni.com', 'role' => 'author', 'bio' => 'Science and environment correspondent. Passionate about space exploration and climate policy.'],
        ])->map(fn ($u) => User::updateOrCreate(['email' => $u['email']], array_merge($u, ['password' => bcrypt('password')])));

        $allAuthors = $authors->push($admin);

        // ── Additional Categories ─────────────────────────────
        $extraCategories = [
            ['name' => 'Entertainment', 'slug' => 'entertainment', 'color_code' => '#EC4899', 'sort_order' => 7],
            ['name' => 'Health', 'slug' => 'health', 'color_code' => '#14B8A6', 'sort_order' => 8],
            ['name' => 'World News', 'slug' => 'world-news', 'color_code' => '#F97316', 'sort_order' => 9],
            ['name' => 'Education', 'slug' => 'education', 'color_code' => '#6366F1', 'sort_order' => 10],
        ];
        foreach ($extraCategories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }

        // ── Additional Tags ──────────────────────────────────
        $newTags = ['IPL 2026', 'Agentic AI', 'West Asia', 'GDP Growth', 'Noida Airport', 'Semiconductor', 'PharmaMed', 'T20 World Cup', 'Ram Navami', 'Data Centers', 'Deepfake', 'EV Revolution', 'ISRO', 'UPI', 'Digital India', 'Bollywood', 'Women in STEM'];
        foreach ($newTags as $name) {
            Tag::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'slug' => Str::slug($name)]);
        }

        // ── Trending Posts (India Google Trends March 2026) ──
        $posts = [
            // POLITICS
            [
                'title' => 'PM Modi Inaugurates Noida International Airport: India\'s Largest Aviation Hub Takes Flight',
                'category' => 'politics',
                'excerpt' => 'The ₹11,200 crore Jewar airport opens its doors, marking a new chapter in India\'s aviation infrastructure with a capacity of 12 million passengers annually.',
                'content' => '<p>Prime Minister Narendra Modi on Friday inaugurated the first phase of the Noida International Airport at Jewar, Uttar Pradesh, calling it a "gateway to a new India." The airport, built at an estimated cost of ₹11,200 crore, is expected to handle 12 million passengers annually in its initial phase.</p><p>The inauguration drew massive attention as the airport is set to serve as a second aviation hub for the Delhi-NCR region, easing the congestion at Indira Gandhi International Airport. "This airport will transform the economic landscape of western UP," the Prime Minister said during his address.</p><p>Aviation Minister Jyotiraditya Scindia highlighted that the Noida airport would offer direct connectivity to over 50 domestic and 20 international destinations by year-end. The Yamuna Expressway Industrial Development Authority has already sanctioned land for an upcoming cargo hub that could make Jewar one of Asia\'s largest logistic centres.</p><p>Industry experts project the airport will generate over 1 lakh direct and indirect jobs in the region, with real estate development around the airport corridor already witnessing a surge of 40% in property prices over the last two years.</p>',
                'tags' => ['Noida Airport', 'Digital India'],
                'featured' => true,
                'views' => 48500,
                'trending' => 9.8,
                'days_ago' => 1,
                'author_email' => 'manoj@atomni.com',
            ],
            [
                'title' => 'West Asia Crisis: India Reviews Energy Security as Global Supply Chains Face Disruption',
                'category' => 'politics',
                'excerpt' => 'PM chairs emergency meeting with Chief Ministers amid rising crude oil prices and trade route disruptions in the Persian Gulf.',
                'content' => '<p>As the conflict in West Asia enters its fourth week, Prime Minister Modi chaired an emergency meeting with Chief Ministers and senior cabinet colleagues to assess India\'s energy security preparedness. The government categorically denied rumours of a nationwide lockdown.</p><p>"India has diversified its energy imports significantly over the past decade," said Petroleum Minister Hardeep Singh Puri. "We have strategic petroleum reserves sufficient for 14 days, and we are in active discussions with alternate suppliers."</p><p>The disruption of key shipping routes through the Strait of Hormuz has pushed Brent crude above $95 per barrel, a 30% increase since the conflict began. Indian refiners, who source nearly 40% of crude from the Gulf region, have started accelerating imports from Russia and the Americas.</p><p>The Ministry of External Affairs confirmed that over 8 lakh Indian nationals in the Gulf region are safe, with evacuation plans in place for worst-case scenarios. Operation Kalam, a contingency evacuation framework, has been activated on standby mode.</p>',
                'tags' => ['West Asia', 'Economy'],
                'featured' => false,
                'views' => 35200,
                'trending' => 9.5,
                'days_ago' => 2,
                'author_email' => 'manoj@atomni.com',
            ],
            [
                'title' => 'Supreme Court Upholds Scheduled Caste Reservation Criteria in Landmark Verdict',
                'category' => 'politics',
                'excerpt' => 'The five-judge constitution bench confirms only Hindus, Sikhs, and Buddhists can claim SC status, rejecting petitions for expansion.',
                'content' => '<p>In a landmark constitutional ruling, the Supreme Court of India upheld the existing framework that restricts Scheduled Caste (SC) status to members of the Hindu, Sikh, and Buddhist faiths. The five-judge constitution bench delivered a 4-1 majority verdict, dismissing petitions that sought to extend SC status to Dalit converts to Christianity and Islam.</p><p>Chief Justice DY Chandrachud, writing for the majority, stated that the Presidential Order under Article 341 "reflects a considered policy decision by the executive and legislature" and that the court cannot substitute its own judgment in matters of social classification that require empirical data and political consensus.</p><p>The dissenting opinion, delivered by Justice BV Nagarathna, argued that the blanket exclusion based on religion alone violated Articles 14 and 25 of the Constitution and called for Parliament to re-examine the criteria through a dedicated commission.</p><p>Legal experts say the ruling could have significant implications for upcoming state elections, particularly in southern India where Dalit Christian communities form a substantial voter base.</p>',
                'tags' => ['Elections'],
                'featured' => false,
                'views' => 28900,
                'trending' => 8.2,
                'days_ago' => 5,
                'author_email' => 'manoj@atomni.com',
            ],

            // TECHNOLOGY
            [
                'title' => 'Agentic AI Is Here: How India\'s Startups Are Building Autonomous AI Systems That Work Independently',
                'category' => 'technology',
                'excerpt' => 'From chatbots to autonomous agents — Indian AI startups are pioneering "Agentic AI" that can plan, execute, and coordinate complex tasks without human intervention.',
                'content' => '<p>The next wave of artificial intelligence is not about smarter chatbots — it\'s about AI that can act. Welcome to the era of Agentic AI, autonomous systems capable of planning multi-step workflows, coordinating across tools, and executing end-to-end tasks independently.</p><p>At the India-AI Impact Summit 2026, held in February, over 30 Indian startups showcased their agentic AI platforms. Companies like Sarvam AI, Krutrim, and Ola\'s Krutrim Labs demonstrated agents that can handle everything from customer service escalation to supply chain optimization without human supervision.</p><p>"We\'re moving from AI that answers questions to AI that solves problems," said Bhavish Aggarwal, founder of Krutrim Labs. "An agentic system doesn\'t just tell you what to do — it does it, verifies the result, and adapts if something goes wrong."</p><p>Investment in Indian AI startups hit $4.2 billion in FY26, with agentic AI companies receiving the largest share. The sector has seen a 300% increase in venture capital funding compared to FY24, driven by demand from enterprise clients in banking, manufacturing, and healthcare.</p><p>However, experts caution that governance frameworks need to keep pace. "Autonomous AI systems operating without guardrails create liability questions we haven\'t fully answered," said Professor Raj Reddy of IIIT Hyderabad.</p>',
                'tags' => ['Agentic AI', 'AI', 'Startups'],
                'featured' => true,
                'views' => 42300,
                'trending' => 9.6,
                'days_ago' => 3,
                'author_email' => 'priya@atomni.com',
            ],
            [
                'title' => 'India\'s $250 Billion AI Infrastructure Push: Data Centres, Chips, and Sovereign Models',
                'category' => 'technology',
                'excerpt' => 'The India-AI Impact Summit saw pledges exceeding $250 billion across AI infrastructure, semiconductor R&D, and homegrown language models.',
                'content' => '<p>India\'s ambition to become a global AI powerhouse received a massive boost at the India-AI Impact Summit 2026, where government and private sector commitments crossed the $250 billion mark spread over the next five years.</p><p>The pledges encompass data centre infrastructure ($80 billion), semiconductor R&D and manufacturing ($45 billion), foundation model development ($15 billion), and associated connectivity and cloud computing investments.</p><p>IT Minister Ashwini Vaishnaw announced that sovereign AI models — trained on Indian languages and cultural contexts — will be a national priority. "India cannot depend on Western or Chinese AI models for its digital future. We need models that understand Hindi, Tamil, Bengali and 20 other languages natively," he said.</p><p>Major investments were announced by Reliance Jio (₹75,000 crore data centre network), Tata Communications (edge computing in 100 cities), and Adani Group (semiconductor fabrication unit in Gujarat). Global partners including NVIDIA, AMD, and Google committed to establishing AI research labs in Bengaluru, Hyderabad, and Chennai.</p>',
                'tags' => ['Semiconductor', 'Data Centers', 'Digital India', 'AI'],
                'featured' => false,
                'views' => 31500,
                'trending' => 8.9,
                'days_ago' => 8,
                'author_email' => 'priya@atomni.com',
            ],
            [
                'title' => 'India\'s New IT Rules: Deepfake Labels Mandatory, Platforms Face 2-Hour Takedown Windows',
                'category' => 'technology',
                'excerpt' => 'New regulations require AI-generated content to be clearly labelled and give platforms just 2-3 hours to remove flagged synthetic media.',
                'content' => '<p>The Ministry of Electronics and Information Technology (MeitY) has notified sweeping new IT rules that mandate clear labelling of all AI-generated synthetic content, including deepfakes, AI-assisted articles, and voice clones.</p><p>Under the new framework, platforms must implement automated detection systems capable of identifying synthetic media and tagging it with a visible "AI Generated" watermark. Content reported to platforms or flagged by government agencies must be taken down within a "lightning-fast" 2-3 hour window.</p><p>The rules come amid growing concerns about electoral misinformation, with multiple instances of deepfake videos featuring politicians going viral ahead of state assembly elections in Tamil Nadu, Kerala, and Puducherry.</p><p>"This is not about censorship — it\'s about transparency," said MeitY Secretary S. Krishnan. "Citizens have a right to know when the content they\'re consuming is generated by a machine rather than a human."</p><p>Tech companies have raised concerns about the operational feasibility of the 2-hour takedown window, calling it "technically impractical at scale." Industry body NASSCOM has requested a phased implementation timeline.</p>',
                'tags' => ['Deepfake', 'AI', 'Digital India'],
                'featured' => false,
                'views' => 24800,
                'trending' => 7.8,
                'days_ago' => 6,
                'author_email' => 'priya@atomni.com',
            ],

            // SPORTS
            [
                'title' => 'IPL 2026 Kicks Off: RCB vs SRH Sets the Tone for Cricket\'s Biggest Festival',
                'category' => 'sports',
                'excerpt' => 'The Indian Premier League 2026 begins at M. Chinnaswamy Stadium as Bengaluru and Hyderabad clash in front of a roaring 40,000 crowd.',
                'content' => '<p>The 19th edition of the Indian Premier League roared to life at the M. Chinnaswamy Stadium in Bengaluru on Friday, with Royal Challengers Bengaluru hosting Sunrisers Hyderabad in a blockbuster opener.</p><p>The BCCI, mindful of assembly elections in multiple states, announced the schedule in two phases — the first 20 matches were revealed in early March, with the league stage set to conclude on May 24.</p><p>This year\'s auction had already generated massive buzz, with 12-year-old prodigy Vaibhav Suryavanshi becoming the youngest-ever IPL signing for Rajasthan Royals at ₹1.1 crore. Overseas stars including Travis Head, Liam Livingstone, and Rashid Khan headline the international contingent.</p><p>Economists estimate the IPL\'s direct and indirect economic impact at approximately ₹60,000 crore annually, making it one of India\'s largest single sporting events. Advertising spend across television and digital platforms is projected to touch ₹8,000 crore this season.</p><p>The BCCI has also introduced a new Umpire Review System powered by AI this season, promising faster and more accurate decision-making.</p>',
                'tags' => ['IPL 2026'],
                'featured' => true,
                'views' => 67800,
                'trending' => 9.9,
                'days_ago' => 1,
                'author_email' => 'rohan@atomni.com',
            ],
            [
                'title' => 'India\'s T20 World Cup Triumph: How Rohit Sharma\'s Team Conquered New Zealand in the Final',
                'category' => 'sports',
                'excerpt' => 'A historic victory at the Narendra Modi Stadium as India lifts its third ICC T20 World Cup title with a dominant 8-wicket win.',
                'content' => '<p>India sealed a magnificent 8-wicket victory over New Zealand in the ICC Men\'s T20 World Cup 2026 final at the Narendra Modi Stadium in Ahmedabad, clinching their third T20 World Cup title in front of a record 1,32,000 spectators.</p><p>Captain Rohit Sharma, playing his final T20 International, anchored the chase with a sublime 78 not out off 49 balls, guiding India home with 12 balls to spare. "This is the perfect farewell," an emotional Sharma told Star Sports after the match.</p><p>Jasprit Bumrah was named Player of the Tournament for his extraordinary economy rate of 4.27 and 15 wickets across 8 matches, including a devastating 4-18 in the final that restricted New Zealand to 144/8.</p><p>The victory triggered celebrations across India, with millions pouring onto streets in Mumbai, Delhi, and Kolkata. The BCCI announced a ₹125 crore bonus for the team, while several state governments declared public holidays.</p>',
                'tags' => ['T20 World Cup'],
                'featured' => false,
                'views' => 89200,
                'trending' => 9.4,
                'days_ago' => 12,
                'author_email' => 'rohan@atomni.com',
            ],

            // BUSINESS
            [
                'title' => 'India GDP Growth Surges to 7.6% in FY26: Manufacturing Boom Drives the Rebound',
                'category' => 'business',
                'excerpt' => 'Strong private consumption and a 13.3% manufacturing expansion in Q3 FY26 push India\'s growth well above global average.',
                'content' => '<p>India\'s GDP growth for FY26 is estimated at 7.6%, driven by a robust manufacturing sector that expanded by 13.3% in the third quarter, according to the new GDP series with base year 2022-23 released by the Ministry of Statistics.</p><p>The strong numbers came as a pleasant surprise to economists who had projected growth of 6.8-7.0%. Private consumption, which accounts for nearly 58% of GDP, grew at 8.1% — the highest in three years — buoyed by festive season spending and a resilient rural economy.</p><p>Fitch Ratings has revised India\'s growth projection upward to 7.5% for the fiscal year, noting that "India\'s domestic demand story remains intact despite global headwinds." The agency highlighted India\'s infrastructure spending pipeline, including the Noida airport, Bullet Train corridor, and the PM Gati Shakti logistics network, as key growth multipliers.</p><p>The Reserve Bank of India maintained the repo rate at 6.25%, signalling confidence in the growth trajectory while keeping a watchful eye on food inflation, which remained elevated at 5.2% in February.</p>',
                'tags' => ['GDP Growth', 'Economy'],
                'featured' => false,
                'views' => 25600,
                'trending' => 8.5,
                'days_ago' => 4,
                'author_email' => 'ananya@atomni.com',
            ],
            [
                'title' => 'UPI Crosses 20 Billion Monthly Transactions: India\'s Digital Payment Revolution Reaches New Heights',
                'category' => 'business',
                'excerpt' => 'India\'s Unified Payments Interface processes over 20 billion transactions worth ₹18 lakh crore in a single month, cementing its position as the world\'s largest real-time payment system.',
                'content' => '<p>The Unified Payments Interface (UPI) set a new global record in March 2026, processing over 20 billion transactions worth approximately ₹18.4 lakh crore in a single month. The milestone underscores India\'s position as the undisputed leader in real-time digital payments.</p><p>NPCI data shows UPI now accounts for over 72% of all retail digital payments in India, up from 56% two years ago. The platform has expanded beyond person-to-person transfers to become the backbone of e-commerce, bill payments, and even cross-border remittances.</p><p>"UPI is not just a payment system — it\'s a public digital infrastructure," said Dilip Asbe, CEO of NPCI. "Its success has inspired over 15 countries to develop similar systems."</p><p>Singapore, UAE, France, and Sri Lanka now accept UPI payments, with Japan and Australia expected to join by the end of 2026. The RBI is working on UPI integration with the European Instant Payment system (SEPA Instant) for seamless India-Europe transactions.</p>',
                'tags' => ['UPI', 'Digital India', 'Startups'],
                'featured' => false,
                'views' => 18900,
                'trending' => 7.6,
                'days_ago' => 7,
                'author_email' => 'ananya@atomni.com',
            ],
            [
                'title' => 'EV Revolution: India\'s Electric Vehicle Sales Cross 3 Million Units in FY26',
                'category' => 'business',
                'excerpt' => 'Two-wheelers lead the charge as India becomes the world\'s third-largest EV market, with Ola, Ather, and TVS dominating sales.',
                'content' => '<p>India\'s electric vehicle market has crossed a significant milestone, with total EV sales surpassing 3 million units in FY26 — a 65% year-on-year increase. Electric two-wheelers accounted for 78% of sales, followed by three-wheelers (15%) and four-wheelers (7%).</p><p>Ola Electric led the two-wheeler segment with a 32% market share, followed by Ather Energy (22%) and TVS (18%). In the four-wheeler space, Tata Motors maintained its dominance with the Nexon EV and Curvv EV, while BYD and MG Motor gained significant ground in the premium segment.</p><p>The government\'s PM E-Drive scheme, which replaced FAME-III, has been instrumental in driving adoption with subsidies of up to ₹10,000 per kWh for two-wheelers and ₹50,000 for four-wheelers.</p><p>Charging infrastructure has expanded rapidly, with over 125,000 public charging stations now operational across India — a 4x increase from FY24. Highway charging corridors on all major national highways now have fast chargers every 50 km.</p>',
                'tags' => ['EV Revolution', 'Economy'],
                'featured' => false,
                'views' => 21300,
                'trending' => 7.2,
                'days_ago' => 9,
                'author_email' => 'ananya@atomni.com',
            ],

            // SCIENCE
            [
                'title' => 'ISRO\'s Gaganyaan Test Flight Succeeds: India One Step Closer to Human Spaceflight',
                'category' => 'science',
                'excerpt' => 'The uncrewed G2 mission completes all objectives including orbital manoeuvres and safe re-entry, clearing the path for India\'s first crewed mission in 2027.',
                'content' => '<p>The Indian Space Research Organisation achieved a crucial milestone as the Gaganyaan G2 uncrewed test flight completed all mission objectives, including orbital insertion, in-orbit manoeuvres, and a controlled re-entry into the Bay of Bengal.</p><p>The crew module, carrying Vyommitra (an AI-powered humanoid astronaut), spent 72 hours in Low Earth Orbit at an altitude of 400 km before executing a precision splashdown within 2 km of the designated recovery zone.</p><p>"This is the most complex mission ISRO has ever executed," said Chairman Dr S. Somanath. "Every system — life support, thermal protection, parachute deployment — performed exactly as designed."</p><p>The success clears the way for the G3 crewed mission, tentatively scheduled for Q3 2027, which will make India the fourth nation to independently send humans to space. Four Indian Air Force test pilots — Group Captains Prasanth Balakrishnan Nair, Ajit Krishnan, Angad Pratap, and Shubhanshu Shukla — are undergoing final training at the Astronaut Training Facility in Bengaluru.</p>',
                'tags' => ['ISRO', 'Space'],
                'featured' => false,
                'views' => 38700,
                'trending' => 8.8,
                'days_ago' => 10,
                'author_email' => 'vikram@atomni.com',
            ],
            [
                'title' => 'India Achieves Breakthrough in Quantum Computing: IIT-Bombay Team Creates 50-Qubit Processor',
                'category' => 'science',
                'excerpt' => 'Researchers at IIT-Bombay develop India\'s first 50-qubit superconducting quantum processor, putting India in the elite club of quantum nations.',
                'content' => '<p>A team of physicists at IIT-Bombay, in collaboration with the Tata Institute of Fundamental Research (TIFR), has successfully fabricated and tested a 50-qubit superconducting quantum processor — making India only the fifth country in the world to achieve this capability.</p><p>The processor, named "Indra-Q50," operates at a temperature of 15 millikelvins (colder than outer space) and demonstrated quantum advantage on specific optimisation problems. The team published their results in Nature Physics.</p><p>"Indra-Q50 is not just a laboratory achievement — it\'s a stepping stone to practical quantum computing applications in drug discovery, cryptography, and weather prediction," said lead researcher Professor Arindam Ghosh.</p><p>The Department of Science and Technology has allocated ₹6,000 crore under the National Quantum Mission for the next phase, which aims to build a 200-qubit processor by 2028 and establish quantum internet testbeds across five cities.</p>',
                'tags' => ['AI', 'Space'],
                'featured' => false,
                'views' => 27400,
                'trending' => 8.1,
                'days_ago' => 14,
                'author_email' => 'vikram@atomni.com',
            ],

            // OPINION
            [
                'title' => 'Opinion: Will AI Replace Journalists? The Answer Is More Nuanced Than You Think',
                'category' => 'opinion',
                'excerpt' => 'AI can write news reports, but it cannot investigate, empathise, or hold power to account. Here\'s why human journalism is irreplaceable.',
                'content' => '<p>Every week brings a new AI tool that can generate a news article in 15 seconds. Every week, a journalist somewhere wonders if their job is next. The anxiety is real, but the answer requires nuance.</p><p>Let me be clear: AI will absolutely replace certain kinds of journalism — the formulaic, data-driven, template-following kind. Earnings reports, sports scores, weather updates — these can and should be automated. They already are at many publications.</p><p>But here\'s what AI cannot do: knock on a politician\'s door at midnight, cultivate sources over years of trust, feel the weight of a farmer\'s tears, or understand the difference between what a government official says and what they mean. These human capabilities are the bedrock of investigative, narrative, and explanatory journalism.</p><p>The real question isn\'t "Will AI replace journalists?" but "What kind of journalism will survive?" The answer: journalism that is deeply human, courageously investigative, and genuinely serves the public interest. Everything else is content — and content is cheap.</p>',
                'tags' => ['AI'],
                'featured' => false,
                'views' => 15600,
                'trending' => 6.2,
                'days_ago' => 11,
                'author_email' => 'manoj@atomni.com',
            ],

            // ENTERTAINMENT
            [
                'title' => 'Dhurandhar 2 Breaks Box Office Records: ₹500 Crore in Just 10 Days',
                'category' => 'entertainment',
                'excerpt' => 'The Akshay Kumar and Pankaj Tripathi sequel becomes the fastest Indian film to cross ₹500 crore, despite battling piracy and online leaks.',
                'content' => '<p>Rohit Shetty\'s Dhurandhar 2 has shattered box office records, crossing the ₹500 crore worldwide gross mark in just 10 days of release. The action-comedy sequel, starring Akshay Kumar and Pankaj Tripathi, opened to the highest single-day collection ever for a Hindi film at ₹78 crore.</p><p>The film\'s success comes despite a significant piracy challenge — high-quality prints were leaked online within 48 hours of release, prompting the Bombay High Court to issue a John Doe order demanding takedowns across 150+ piracy domains.</p><p>Trade analyst Taran Adarsh called it "a phenomenon, not just a film" and projected a lifetime collection of ₹800-900 crore worldwide. The film\'s success has revitalised the theatrical release model at a time when OTT platforms were dominating consumption.</p><p>Producer Jio Studios has already greenlit Dhurandhar 3, with production expected to begin in late 2026.</p>',
                'tags' => ['Bollywood'],
                'featured' => false,
                'views' => 45200,
                'trending' => 8.7,
                'days_ago' => 5,
                'author_email' => 'priya@atomni.com',
            ],

            // HEALTH
            [
                'title' => 'PharmaMed 2026: India\'s Pharma Industry Eyes $130 Billion Revenue by 2030',
                'category' => 'health',
                'excerpt' => 'At the annual PharmaMed conference, industry leaders outline a roadmap for India to become the world\'s largest generic drug producer.',
                'content' => '<p>India\'s pharmaceutical industry is on track to achieve $130 billion in annual revenue by 2030, up from $78 billion in FY26, according to projections presented at the PharmaMed 2026 conference in Hyderabad.</p><p>Health Minister JP Nadda announced a new ₹10,000 crore Production-Linked Incentive (PLI) scheme specifically for biosimilars and complex generics, areas where India currently trails behind China and the EU.</p><p>"India already supplies 60% of the world\'s vaccines and 20% of global generic drugs. The next frontier is biosimilars, where we can save global healthcare systems trillions of dollars," said the minister.</p><p>Key trends at the conference included AI-driven drug discovery (with Bengaluru startup Innoplexus demonstrating a platform that reduced drug discovery timelines from 5 years to 18 months), the rise of contract research organisations (CROs), and India\'s growing role as a global clinical trial hub.</p>',
                'tags' => ['PharmaMed', 'Health'],
                'featured' => false,
                'views' => 16800,
                'trending' => 6.8,
                'days_ago' => 13,
                'author_email' => 'vikram@atomni.com',
            ],

            // WORLD NEWS
            [
                'title' => 'West Asia Conflict: India Evacuates 15,000 Citizens from the Gulf as Tensions Escalate',
                'category' => 'world-news',
                'excerpt' => 'Operation Kalam enters its active phase as Air India and Navy vessels bring back Indian nationals from conflict-affected zones.',
                'content' => '<p>India\'s emergency evacuation operation from the Gulf region — codenamed Operation Kalam — has entered its active phase, with Air India deploying 45 special flights and the Indian Navy dispatching INS Vikrant and two landing platform docks to bring back citizens.</p><p>Over 15,000 Indian nationals have been evacuated so far from Oman, Bahrain, and Kuwait, with the External Affairs Ministry setting up 24/7 helplines and dedicated contact centres in all affected countries.</p><p>"No Indian will be left behind," said External Affairs Minister S. Jaishankar. "We have the diplomatic channels, the logistical capability, and the political will to protect our citizens."</p><p>India\'s diaspora in the Gulf — estimated at 8.7 million — represents the largest expatriate community in the region. While the majority are in the relatively stable UAE and Saudi Arabia, communities in smaller Gulf nations have expressed concern about escalating military activity.</p>',
                'tags' => ['West Asia'],
                'featured' => false,
                'views' => 39100,
                'trending' => 9.1,
                'days_ago' => 3,
                'author_email' => 'manoj@atomni.com',
            ],

            // GADGETS (sub-category of technology)
            [
                'title' => 'Apple Unveils \'Vision Pro 2\': Cheaper, Lighter, Better — And Finally Coming to India',
                'category' => 'gadgets',
                'excerpt' => 'Apple\'s second-generation spatial computing headset weighs 30% less, costs $2,499, and launches in India alongside 8 global markets.',
                'content' => '<p>Apple has officially announced the Vision Pro 2, its second-generation spatial computing headset that addresses the key criticisms of the original: weight, price, and availability.</p><p>The Vision Pro 2 weighs just 400 grams (compared to 600g for the original), costs $2,499 (down from $3,499), and will launch in India for the first time alongside 8 global markets in June 2026.</p><p>The upgraded headset features the M4 Max chip for enhanced performance, a larger field of view (120° vs 100°), and a new "transparent mode" that offers near-natural passthrough — making the boundaries between digital and physical worlds virtually invisible.</p><p>Apple CEO Tim Cook highlighted enterprise applications as the primary growth driver: "We\'re seeing Vision Pro used in surgery, architecture, and manufacturing. The consumer story is still evolving, but the enterprise story is already here."</p>',
                'tags' => ['AI', 'Startups'],
                'featured' => false,
                'views' => 33800,
                'trending' => 8.4,
                'days_ago' => 6,
                'author_email' => 'priya@atomni.com',
            ],

            // AI (sub-category of technology)
            [
                'title' => 'AI\'s Next Leap? New Neural Network Learns Like a Toddler — By Playing, Not Reading',
                'category' => 'ai',
                'excerpt' => 'Google DeepMind and IIT-Delhi researchers demonstrate a neural network that acquires conceptual understanding through embodied interaction rather than text training.',
                'content' => '<p>A collaboration between Google DeepMind and IIT-Delhi has produced a neural network that learns abstract concepts — like object permanence, gravity, and spatial reasoning — not by reading text, but by interacting with simulated physical environments, much like a toddler explores the world.</p><p>The system, called "CuriOS" (Curiosity-driven Observation System), was trained in a rich 3D simulation where it could push, pull, stack, and drop virtual objects. Over 10,000 hours of simulated play, it independently discovered physical principles that took traditional AI systems millions of labelled examples to learn.</p><p>"Language models are incredible at text, but they have no grounding in physical reality," said Dr. Prateek Jain, lead researcher from IIT-Delhi. "CuriOS builds intuitive physics from experience, not from reading Wikipedia articles about physics."</p><p>The implications are significant for robotics, autonomous vehicles, and industrial automation — domains where understanding the physical world is as important as understanding language. Google has announced plans to integrate CuriOS with its Gemini AI platform by 2027.</p>',
                'tags' => ['Agentic AI', 'AI'],
                'featured' => false,
                'views' => 29600,
                'trending' => 8.6,
                'days_ago' => 4,
                'author_email' => 'priya@atomni.com',
            ],

            // EDUCATION
            [
                'title' => 'India Launches National EdTech Mission: Free AI Tutors for Every Government School Student',
                'category' => 'education',
                'excerpt' => 'The ₹15,000 crore initiative will provide personalised AI-powered learning assistants in 11 Indian languages to 25 crore students.',
                'content' => '<p>Education Minister Dharmendra Pradhan launched the National EdTech Mission — a ₹15,000 crore initiative that aims to provide every government school student in India with a free, personalised AI-powered learning assistant accessible via smartphones.</p><p>The AI tutors, developed in partnership with IIT-Madras and Bhashini (India\'s language translation platform), will be available in 11 Indian languages and cover the NCERT curriculum from Class 1 to Class 12.</p><p>"No child should be left behind because they couldn\'t afford a tuition teacher," said the minister. "This AI tutor will be available 24/7, patient and adaptive, adjusting to each student\'s learning pace."</p><p>Early pilots in Rajasthan and Karnataka showed a 34% improvement in learning outcomes for students who used the AI tutor regularly, with the most significant gains observed in mathematics and science.</p><p>However, concerns have been raised about screen time for younger students, digital infrastructure gaps in rural areas, and the potential displacement of human tutors. The ministry has committed to a hybrid model where AI supplements — rather than replaces — classroom teaching.</p>',
                'tags' => ['AI', 'Digital India', 'Women in STEM'],
                'featured' => false,
                'views' => 22100,
                'trending' => 7.4,
                'days_ago' => 8,
                'author_email' => 'ananya@atomni.com',
            ],

            // More varied content
            [
                'title' => 'Ram Navami 2026: Surya Tilak Ceremony at Ayodhya Attracts Record 25 Lakh Devotees',
                'category' => 'politics',
                'excerpt' => 'The precision-engineered solar alignment illuminates the Ram Lalla idol\'s forehead at exactly 12:16 PM, watched by millions live.',
                'content' => '<p>Ayodhya witnessed an unprecedented gathering as an estimated 25 lakh devotees thronged the Ram Janmabhoomi temple complex for the Ram Navami Surya Tilak ceremony — a precision-engineered solar alignment that directs sunlight through a series of mirrors and lenses to illuminate the forehead of the Ram Lalla idol.</p><p>The ceremony, broadcast live on all major news channels and Doordarshan, occurred at exactly 12:16 PM IST, with the beam of sunlight lasting for 4 minutes. "This is not just engineering — it\'s the harmonious convergence of science, faith, and architecture," said temple Trust Chairman Nritya Gopal Das.</p><p>Security was unprecedented, with over 20,000 police personnel deployed and a multi-layer drone surveillance system monitoring the city. The Uttar Pradesh government arranged for 5,000 special buses and temporary railway services to manage the influx.</p><p>PM Modi, who joined virtually, called the ceremony "a symbol of India\'s civilisational continuity" and announced plans for a ₹10,000 crore tourism development project around the temple complex.</p>',
                'tags' => ['Ram Navami'],
                'featured' => false,
                'views' => 52100,
                'trending' => 9.2,
                'days_ago' => 2,
                'author_email' => 'manoj@atomni.com',
            ],
            [
                'title' => 'Cybersecurity Alert: Indian Banking Sector Faces Surge in AI-Powered Phishing Attacks',
                'category' => 'technology',
                'excerpt' => 'RBI issues advisory after a 400% spike in AI-generated phishing attempts targeting UPI users and online banking customers.',
                'content' => '<p>The Reserve Bank of India has issued an urgent advisory to all banks and payment service providers following a 400% spike in AI-powered phishing attacks targeting Indian consumers. The attacks use sophisticated AI-generated voice calls, deepfake videos of bank officials, and hyper-personalised email scams.</p><p>"These aren\'t your typical Nigerian prince emails," said RBI Deputy Governor Michael Patra. "The attackers are using AI to clone the voices of actual bank relationship managers and creating video calls that are virtually indistinguishable from real ones."</p><p>CERT-IN (Indian Computer Emergency Response Team) reported that over 2.3 lakh complaints were filed in February alone, with total losses exceeding ₹940 crore. The attacks primarily targeted UPI users in the 25-45 age group through WhatsApp and Telegram.</p><p>The RBI has mandated that all banks implement multi-factor authentication for transactions above ₹10,000 and has launched a nationwide awareness campaign with the tagline "Agar Sahi Lag Raha Hai, Tab Bhi Verify Karo" (Even if it looks right, verify it).</p>',
                'tags' => ['Cybersecurity', 'UPI', 'AI'],
                'featured' => false,
                'views' => 31200,
                'trending' => 7.9,
                'days_ago' => 7,
                'author_email' => 'priya@atomni.com',
            ],
            [
                'title' => 'India\'s Women in STEM: Record 43% Female Enrollment in Engineering Colleges for 2026-27',
                'category' => 'education',
                'excerpt' => 'A decade of targeted scholarships, mentorship programs, and awareness campaigns has pushed female engineering enrollment to an all-time high.',
                'content' => '<p>India has achieved a significant milestone in gender parity in STEM education, with female enrollment in engineering colleges reaching a record 43% for the 2026-27 academic year, according to AICTE data. This represents a dramatic improvement from just 23% in 2015-16.</p><p>The increase is attributed to a combination of government scholarships (Pragati and AICTE Saksham schemes), industry mentorship programs (led by companies like Infosys, TCS, and Google), and changing parental attitudes towards women in technical careers.</p><p>Computer Science remains the most popular branch among women (52% female enrollment), followed by Biotechnology (48%) and Electronics (39%). However, mechanical and civil engineering still lag at 18% and 22% respectively.</p><p>"The numbers tell an incredible story of transformation," said AICTE Chairman Prof. T.G. Sitharam. "But enrollment is only half the battle — we need to ensure these women get equal opportunities in the workplace after graduation."</p>',
                'tags' => ['Women in STEM', 'Digital India'],
                'featured' => false,
                'views' => 19400,
                'trending' => 6.9,
                'days_ago' => 15,
                'author_email' => 'ananya@atomni.com',
            ],
        ];

        // Create all posts
        foreach ($posts as $p) {
            $category = Category::where('slug', $p['category'])->first();
            $author = $allAuthors->firstWhere('email', $p['author_email']) ?? $admin;

            $post = Post::updateOrCreate(
                ['slug' => Str::slug($p['title'])],
                [
                    'author_id'      => $author->id,
                    'category_id'    => $category?->id,
                    'title'          => $p['title'],
                    'slug'           => Str::slug($p['title']),
                    'content'        => $p['content'],
                    'excerpt'        => $p['excerpt'],
                    'status'         => 'published',
                    'is_featured'    => $p['featured'],
                    'reading_time'   => max(1, (int) ceil(str_word_count(strip_tags($p['content'])) / 200)),
                    'views_count'    => $p['views'],
                    'trending_score' => $p['trending'],
                    'published_at'   => now()->subDays($p['days_ago'])->subHours(rand(0, 12)),
                ]
            );

            // Attach tags
            $tagIds = Tag::whereIn('name', $p['tags'])->pluck('id');
            $post->tags()->syncWithoutDetaching($tagIds);
        }

        // ── Team Members ─────────────────────────────────────
        $teamMembers = [
            ['name' => 'Arjun Mehta', 'role' => 'Editor-in-Chief', 'bio' => 'Award-winning journalist with 20+ years in Indian media. Previously with NDTV and The Indian Express.', 'order_column' => 1, 'is_active' => true],
            ['name' => 'Kavitha Ramachandran', 'role' => 'Managing Editor', 'bio' => 'Former Reuters correspondent specialising in South Asian politics and geopolitics.', 'order_column' => 2, 'is_active' => true],
            ['name' => 'Siddharth Joshi', 'role' => 'Head of Technology', 'bio' => 'IIT-Delhi alumnus and former Google engineer. Leads Atomni\'s tech stack and AI-driven content tools.', 'order_column' => 3, 'is_active' => true],
            ['name' => 'Nandini Patel', 'role' => 'Business Editor', 'bio' => 'CA-turned-journalist covering markets, startups, and Indian economy for 12 years.', 'order_column' => 4, 'is_active' => true],
            ['name' => 'Aditya Sharma', 'role' => 'Sports Editor', 'bio' => 'Former cricketer turned sports journalist. Covers IPL, international cricket, and Olympic sports.', 'order_column' => 5, 'is_active' => true],
            ['name' => 'Meera Krishnamurthy', 'role' => 'Science & Environment Editor', 'bio' => 'PhD in Environmental Science from JNU. Covers ISRO, climate policy, and sustainability.', 'order_column' => 6, 'is_active' => true],
        ];

        foreach ($teamMembers as $tm) {
            TeamMember::updateOrCreate(['name' => $tm['name']], $tm);
        }

        // ── Comments on Popular Posts ────────────────────────
        $publishedPosts = Post::published()->take(10)->pluck('id');
        $guestComments = [
            ['guest_name' => 'Rahul K.', 'guest_email' => 'rahul.k@example.com', 'comment_text' => 'Excellent coverage! This is exactly the kind of in-depth reporting we need.', 'is_approved' => true],
            ['guest_name' => 'Sneha Reddy', 'guest_email' => 'sneha.r@example.com', 'comment_text' => 'Very well written article. Would love to see more analysis on this topic.', 'is_approved' => true],
            ['guest_name' => 'Amit Verma', 'guest_email' => 'amit.v@example.com', 'comment_text' => 'I disagree with some of the conclusions here, but respect the thorough research.', 'is_approved' => true],
            ['guest_name' => 'Deepika M.', 'guest_email' => 'deepika.m@example.com', 'comment_text' => 'Shared this with my team at work. Really informative piece!', 'is_approved' => true],
            ['guest_name' => 'Karthik S.', 'guest_email' => 'karthik.s@example.com', 'comment_text' => 'Can Atomni do a follow-up piece on this? There\'s so much more to uncover.', 'is_approved' => true],
            ['guest_name' => 'Priyanka Jain', 'guest_email' => 'priyanka.j@example.com', 'comment_text' => 'The data in this article is eye-opening. Keep up the great work!', 'is_approved' => true],
            ['guest_name' => 'Vishal Gupta', 'guest_email' => 'vishal.g@example.com', 'comment_text' => 'This reads like propaganda. Where is the opposing viewpoint?', 'is_approved' => false],
            ['guest_name' => 'Anitha N.', 'guest_email' => 'anitha.n@example.com', 'comment_text' => 'Well-balanced reporting. Atomni is quickly becoming my go-to news source.', 'is_approved' => true],
            ['guest_name' => 'Mohammed Rafi', 'guest_email' => 'rafi.m@example.com', 'comment_text' => 'Finally, a news site that covers technology stories with proper depth and context.', 'is_approved' => true],
            ['guest_name' => 'Lakshmi Iyer', 'guest_email' => 'lakshmi.i@example.com', 'comment_text' => 'The analysis is spot on but I think you missed the environmental impact angle.', 'is_approved' => true],
            ['guest_name' => 'Rohit Sharma', 'guest_email' => 'rohit.s@example.com', 'comment_text' => 'Great article but needs more sources. Some claims need citations.', 'is_approved' => false],
            ['guest_name' => 'Sunita Devi', 'guest_email' => 'sunita.d@example.com', 'comment_text' => 'This is what quality journalism looks like. Thank you, Atomni team.', 'is_approved' => true],
        ];

        foreach ($guestComments as $i => $c) {
            $postId = $publishedPosts[$i % $publishedPosts->count()];
            Comment::updateOrCreate(
                ['guest_email' => $c['guest_email'], 'post_id' => $postId],
                array_merge($c, [
                    'post_id' => $postId,
                    'created_at' => now()->subDays(rand(0, 10))->subHours(rand(0, 23)),
                ])
            );
        }

        // ── Job Postings ─────────────────────────────────────
        $jobs = [
            [
                'title' => 'Senior Political Correspondent',
                'slug' => 'senior-political-correspondent',
                'department' => 'Editorial',
                'location' => 'New Delhi',
                'type' => 'Full-time',
                'description' => 'We are looking for an experienced political correspondent to cover national politics, Parliament proceedings, and election campaigns. The ideal candidate will have strong contacts in political circles and a proven track record of breaking stories.',
                'requirements' => "- 5+ years of experience in political journalism\n- Strong understanding of Indian constitutional and parliamentary processes\n- Fluency in Hindi and English\n- Ability to work under tight deadlines\n- A portfolio of published political articles",
                'benefits' => "- Competitive salary with annual bonuses\n- Health insurance for family\n- Press accreditation support\n- Flexible work arrangements\n- Professional development budget",
                'status' => 'active',
                'closing_date' => now()->addDays(30),
            ],
            [
                'title' => 'AI/ML Engineer — Content Intelligence',
                'slug' => 'ai-ml-engineer-content-intelligence',
                'department' => 'Technology',
                'location' => 'Bengaluru (Hybrid)',
                'type' => 'Full-time',
                'description' => 'Join our technology team to build AI-powered content recommendation, SEO optimization, and automated summarization systems. You\'ll work at the intersection of journalism and machine learning.',
                'requirements' => "- 3+ years experience with PyTorch/TensorFlow\n- Experience with NLP and transformer architectures\n- Familiarity with LLM fine-tuning and RAG systems\n- Strong Python skills\n- Bachelor's or Master's in CS/AI/ML",
                'benefits' => "- Stock options in a growing media startup\n- Remote-first culture\n- Latest hardware and cloud compute budget\n- Conference attendance allowance\n- Unlimited leaves",
                'status' => 'active',
                'closing_date' => now()->addDays(45),
            ],
            [
                'title' => 'Video Journalist / Visual Storyteller',
                'slug' => 'video-journalist-visual-storyteller',
                'department' => 'Multimedia',
                'location' => 'Mumbai',
                'type' => 'Full-time',
                'description' => 'Create compelling video stories for our digital platforms. From quick social reels to in-depth documentary features, you\'ll own the visual storytelling pipeline.',
                'requirements' => "- 2+ years in video journalism or digital content creation\n- Proficiency in Adobe Premiere Pro, After Effects\n- Drone operation certification (preferred)\n- Ability to shoot, edit, and publish under tight deadlines\n- Portfolio of published video work",
                'benefits' => "- Equipment provided (camera, drone, editing rig)\n- Travel opportunities across India\n- Health insurance and gym membership\n- Creative freedom on projects",
                'status' => 'active',
                'closing_date' => now()->addDays(25),
            ],
            [
                'title' => 'Editorial Intern — Summer 2026',
                'slug' => 'editorial-intern-summer-2026',
                'department' => 'Editorial',
                'location' => 'Remote / Any Indian City',
                'type' => 'Internship',
                'description' => 'A 3-month internship program for aspiring journalists. You\'ll work alongside our editorial team, contribute to published stories, and receive mentorship from award-winning journalists.',
                'requirements' => "- Currently pursuing or recently completed a degree in Journalism, Mass Communication, or related field\n- Strong writing skills in English\n- Curiosity about current affairs\n- Ability to research and verify facts\n- Published clips (college newspaper, blog, etc.) are a plus",
                'benefits' => "- Monthly stipend of ₹15,000\n- Published bylines on Atomni\n- Mentorship from senior editors\n- Certificate of completion\n- Pre-placement offer for top performers",
                'status' => 'active',
                'closing_date' => now()->addDays(15),
            ],
        ];

        foreach ($jobs as $j) {
            JobPosting::updateOrCreate(['slug' => $j['slug']], $j);
        }

        // ── Site Settings ────────────────────────────────────
        $settings = [
            'site_name' => 'At Omni',
            'website_tagline' => 'Breaking News, Analysis & Trending Stories',
            'site_description' => 'At Omni is India\'s fastest-growing digital news platform, delivering breaking news, in-depth analysis, and trending stories across politics, technology, business, sports, and more.',
            'contact_email' => 'contact@atomni.com',
            'contact_phone' => '+91-11-4567-8900',
            'contact_address' => 'Atomni Media Pvt. Ltd., Level 14, Connaught Place, New Delhi 110001, India',
            'social_twitter' => 'https://twitter.com/atomni',
            'social_facebook' => 'https://facebook.com/atomninews',
            'social_instagram' => 'https://instagram.com/atomni',
            'social_linkedin' => 'https://linkedin.com/company/atomni',
            'social_youtube' => 'https://youtube.com/@atomni',
            'donation_enabled' => 'true',
            'donation_link' => 'https://razorpay.me/@atomni',
            'donation_qr' => '',
            'footer_text' => 'Your trusted source for breaking news and in-depth analysis.',
            'copyright_text' => '© 2026 At Omni. All Rights Reserved.',
        ];

        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }

        $this->command->info('✅ Rich content seeded: ' . count($posts) . ' posts, ' . count($teamMembers) . ' team members, ' . count($guestComments) . ' comments, ' . count($jobs) . ' job postings.');
    }
}
