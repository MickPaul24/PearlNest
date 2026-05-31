<?php
/** @var string $baseUrl */
/** @var array $flash */
require __DIR__ . '/partials/header.php';
?>

<div class="page-top">
    <div class="container">
        <h1>About PearlNest</h1>
        <p>Uganda's trusted property brokerage &mdash; Pearl of Africa, one home at a time.</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="about-grid">
            <div class="about-img-col">
                <img src="https://picsum.photos/seed/kampala-city/600/500" alt="Kampala City" class="about-img">
            </div>
            <div class="about-text-col">
                <h2>Who We Are</h2>
                <p>PearlNest is a Kampala-based property brokerage connecting people who need quality accommodation with verified property owners across Uganda. We specialise in hostels, self-contained units, studio apartments, and multi-bedroom rentals.</p>
                <p>We act as the trusted middleman — meaning you never deal directly with unknown landlords. Every inquiry you send comes to us first, and we personally verify the property and connect you safely.</p>
                <div class="about-highlights">
                    <div class="highlight-item">
                        <i data-lucide="shield"></i>
                        <div>
                            <strong>Verified Listings</strong>
                            <p>Every property on PearlNest is personally inspected and verified by our team before being listed.</p>
                        </div>
                    </div>
                    <div class="highlight-item">
                        <i data-lucide="handshake"></i>
                        <div>
                            <strong>Broker-Mediated</strong>
                            <p>You contact the broker, the broker contacts the owner. Safe, professional, and hassle-free.</p>
                        </div>
                    </div>
                    <div class="highlight-item">
                        <i data-lucide="map-pin"></i>
                        <div>
                            <strong>Uganda-Wide Coverage</strong>
                            <p>From Kololo and Muyenga to Ntinda, Naalya, and Entebbe &mdash; we cover all major areas.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item"><span class="stat-number">10+</span><span class="stat-label">Properties Listed</span></div>
            <div class="stat-item"><span class="stat-number">6</span><span class="stat-label">Districts Covered</span></div>
            <div class="stat-item"><span class="stat-number">100%</span><span class="stat-label">Broker Verified</span></div>
            <div class="stat-item"><span class="stat-number">24h</span><span class="stat-label">Response Time</span></div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container text-center">
        <h2 class="section-title">Our Mission</h2>
        <p class="section-sub" style="max-width:640px;margin:0 auto 2rem;">To make renting in Uganda safe, transparent, and accessible for everyone &mdash; students, professionals, families, and expats alike.</p>
        <a href="<?= $baseUrl ?>/contact" class="btn btn-primary">Get in Touch</a>
    </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>
