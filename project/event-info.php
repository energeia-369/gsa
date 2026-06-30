<?php
$event = $_GET['event'] ?? 'elite';
$pageTitle = "Event Information";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$nexusData = [
  'elite' => [
    'title' => 'NEXUS ELITE BUSINESS SUMMIT',
    'subtitle' => 'Connecting Global Leaders & Visionaries',
    'url' => 'https://cybeorch.com/nexusone',
    'urlText' => 'Visit Nexus Elite Website →',
    'image' => 'assets/images/nexas card.png',
    'desc' => 'Welcome to the Nexus Elite Business Summit!<br><br>A premium business summit designed for connecting global leaders, innovative startups, savvy investors, and visionaries. Explore new opportunities and build your network with the best in the industry.'
  ],
  'maytriya' => [
    'title' => 'MAYTRIYA MEET',
    'subtitle' => 'Leadership & Franchise Summit',
    'url' => 'https://energeia369.com/maytriya/',
    'urlText' => 'Visit Maytriya Meet Website →',
    'image' => 'assets/images/maytriya card dark.png', // Assuming there's a dark version, or just maytriya card.png
    'desc' => 'Welcome to the Maytriya Meet!<br><br>This is a carefully curated meet exclusively for investors, franchise brands, bold entrepreneurs, and top business leaders. Discover franchising potentials and leadership strategies that shape the future.'
  ],
  'gsa' => [
    'title' => 'GLOBAL SPORTS ARENA',
    'subtitle' => 'Sports, Tourism & Entertainment',
    'url' => 'gsa-details.php',
    'urlText' => 'Explore GSA Details →',
    'image' => 'assets/images/gsa card.png',
    'desc' => 'Welcome to the Global Sports Arena!<br><br>GSA is where Sports, Tourism, and Entertainment come together on a spectacular global stage. Witness thrilling events and become part of a massive sports community.'
  ]
];

$data = $nexusData[$event] ?? $nexusData['elite'];
?>

<div class="event-info-wrapper">
    <!-- Hero Section -->
    <div class="event-hero">
        <div class="event-hero-bg" style="background-image: url('<?php echo htmlspecialchars($data['image']); ?>');"></div>
        <div class="event-hero-content">
            <span class="event-badge">ABOUT THIS EVENT</span>
            <h1 class="event-title text-3xl md:text-5xl font-black mb-4 text-white drop-shadow-md"><?php echo htmlspecialchars($data['title']); ?></h1>
            <p class="event-subtitle"><?php echo htmlspecialchars($data['subtitle']); ?></p>
            
            <a href="<?php echo htmlspecialchars($data['url']); ?>" class="event-cta-top">
                <?php echo htmlspecialchars($data['urlText']); ?>
            </a>
        </div>
    </div>

    <!-- Content Section -->
    <div class="event-content-section max-w-7xl mx-auto px-4 py-10 md:py-20 relative z-10">
        <div class="event-content-grid grid grid-cols-1 lg:grid-cols-2 gap-8 md:gap-16 items-center">
            <div class="event-image-side">
                <img src="<?php echo htmlspecialchars($data['image']); ?>" alt="<?php echo htmlspecialchars($data['title']); ?>" class="event-featured-image">
            </div>
            <div class="event-text-side">
                <h3 class="event-section-heading">Event Overview</h3>
                <div class="event-description">
                    <?php echo $data['desc']; ?>
                </div>
                
                <div class="event-highlights">
                    <div class="highlight-item">
                        <span class="highlight-icon">🌐</span>
                        <span>Global Networking</span>
                    </div>
                    <div class="highlight-item">
                        <span class="highlight-icon">💡</span>
                        <span>Premium Insights</span>
                    </div>
                    <div class="highlight-item">
                        <span class="highlight-icon">🤝</span>
                        <span>Partnerships</span>
                    </div>
                </div>

                <div class="event-action-bottom">
                    <a href="<?php echo htmlspecialchars($data['url']); ?>" class="event-cta-bottom">
                        <?php echo htmlspecialchars($data['urlText']); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Scoped styles for event-info page */
.event-info-wrapper {
    background-color: var(--bg-primary, #050505);
    color: var(--text-primary, #f5f6fa);
    min-height: 100vh;
    font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    position: relative;
    overflow: hidden;
}

.event-hero {
    position: relative;
    padding: 150px 20px 100px;
    text-align: center;
    border-bottom: 1px solid rgba(197, 168, 92, 0.2);
}

.event-hero-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    opacity: 0.25; /* Blends the image with the theme's background color */
    z-index: 1;
    filter: blur(2px) grayscale(20%);
}

.event-hero-content {
    max-width: 100%;
    margin: 0 auto;
    position: relative;
    z-index: 2;
    animation: fadeInUp 0.8s ease-out;
}

.event-badge {
    background: rgba(197, 168, 92, 0.15);
    color: #c5a85c;
    padding: 6px 16px;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 700;
    letter-spacing: 2px;
    display: inline-block;
    margin-bottom: 20px;
    border: 1px solid rgba(197, 168, 92, 0.4);
    backdrop-filter: blur(5px);
}

.event-title {
    margin: 0 0 15px 0;
    color: var(--text-primary, #ffffff);
    text-shadow: 0 2px 10px rgba(0,0,0,0.3);
}

.event-subtitle {
    font-size: 1.4rem;
    color: #c5a85c;
    margin-bottom: 40px;
    font-weight: 600;
}

.event-cta-top {
    display: inline-block;
    background: #c5a85c;
    color: #0b0c10;
    padding: 16px 36px;
    border-radius: 12px;
    font-weight: 800;
    text-decoration: none;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    box-shadow: 0 10px 30px rgba(197, 168, 92, 0.3);
}

.event-cta-top:hover {
    transform: translateY(-3px);
    background: #d4b768;
    box-shadow: 0 15px 40px rgba(197, 168, 92, 0.4);
    color: #0b0c10;
}

.event-content-section {
    position: relative;
    z-index: 2;
}

.event-content-grid {
    /* Responsiveness handled by Tailwind grid classes */
}

.event-image-side {
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 15px 40px rgba(0,0,0,0.4);
    border: 1px solid rgba(197, 168, 92, 0.3);
}

.event-featured-image {
    width: 100%;
    height: auto;
    display: block;
    transition: transform 0.5s ease;
}

.event-image-side:hover .event-featured-image {
    transform: scale(1.05);
}

.event-section-heading {
    color: #c5a85c;
    font-size: 1.8rem;
    margin-bottom: 25px;
    position: relative;
    padding-bottom: 15px;
}

.event-section-heading::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 60px;
    height: 3px;
    background: #c5a85c;
    border-radius: 2px;
}

.event-description {
    font-size: 1.15rem;
    line-height: 1.8;
    color: var(--text-secondary, #d1d5db);
    margin-bottom: 40px;
}

.event-highlights {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.highlight-item {
    background: rgba(197, 168, 92, 0.05);
    padding: 15px;
    border-radius: 12px;
    border: 1px solid rgba(197, 168, 92, 0.15);
    display: flex;
    align-items: center;
    gap: 15px;
    transition: all 0.3s ease;
}

.highlight-item:hover {
    background: rgba(197, 168, 92, 0.1);
    border-color: rgba(197, 168, 92, 0.4);
    transform: translateY(-2px);
}

.highlight-icon {
    font-size: 1.5rem;
}

.event-cta-bottom {
    display: inline-block;
    border: 2px solid #c5a85c;
    color: #c5a85c;
    padding: 14px 32px;
    border-radius: 12px;
    font-weight: 700;
    text-decoration: none;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    background: transparent;
}

.event-cta-bottom:hover {
    background: #c5a85c;
    color: #0b0c10;
    transform: translateY(-2px);
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 900px) {
    .event-content-grid {
        grid-template-columns: 1fr;
    }
    .event-title {
        font-size: 2.5rem;
    }
}

/* Light Theme Overrides */
body.light-theme .event-info-wrapper {
    background-color: var(--bg-primary, #fbf7ef);
}
body.light-theme .event-hero-bg {
    opacity: 0.12; /* Very faint image background on light theme so it blends softly into cream */
    filter: blur(2px) grayscale(0%);
}
body.light-theme .event-title {
    text-shadow: none;
    color: var(--text-primary, #1a1b26);
}
body.light-theme .event-description {
    color: var(--text-secondary, #4a4d5e);
}
body.light-theme .highlight-item {
    background: rgba(255, 255, 255, 0.6);
    box-shadow: 0 4px 15px rgba(0,0,0,0.03);
}
body.light-theme .highlight-item:hover {
    background: #ffffff;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}
body.light-theme .event-image-side {
    box-shadow: 0 15px 40px rgba(0,0,0,0.1);
}
body.light-theme .event-cta-bottom {
    color: #9d823b;
    border-color: #9d823b;
}
body.light-theme .event-cta-bottom:hover {
    background: #9d823b;
    color: #ffffff;
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
