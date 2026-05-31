<?php
/** @var string $baseUrl */
/** @var callable $imgUrl */
/** @var array $featured */
/** @var array $stats */
/** @var array $flash */
require __DIR__ . '/partials/header.php';
?>

<!-- ═══════════════ HERO ═══════════════ -->
<section class="hero">
    <div class="hero-overlay"></div>
    <div class="container hero-content">
        <div class="hero-text">
            <h1>Find Your Next Home<br>or Short Stay in Uganda.</h1>
            <p>Premium rentals and professional hostel management for modern Ugandans. Experience trusted brokerage and seamless property matching.</p>
        </div>

        <div class="hero-search-box">
            <form action="<?= $baseUrl ?>/property" method="GET" class="hero-form">
                <div class="hero-form-row">
                    <div class="hero-field">
                        <label><i data-lucide="map-pin"></i> Location</label>
                        <input type="text" name="search" placeholder="Where to? e.g. Kololo, Ntinda…" class="form-input">
                    </div>
                    <div class="hero-field">
                        <label><i data-lucide="building-2"></i> Property Type</label>
                        <select name="type" class="form-input">
                            <option value="">All Types</option>
                            <?php foreach (\App\Models\Property::typeOptions() as $val => $label): ?>
                                <option value="<?= $val ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="hero-form-row">
                    <div class="hero-field">
                        <label><i data-lucide="tag"></i> Min Price (UGX/mo)</label>
                        <input type="number" name="min_price" placeholder="Min" class="form-input">
                    </div>
                    <div class="hero-field">
                        <label><i data-lucide="tag"></i> Max Price (UGX/mo)</label>
                        <input type="number" name="max_price" placeholder="Max" class="form-input">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block">
                    <i data-lucide="search"></i> Search Available Properties
                </button>
            </form>
        </div>
    </div>
</section>

<!-- ═══════════════ FLASH ═══════════════ -->
<?php if (!empty($flash)): ?>
<div class="container mt-4">
    <?php foreach ($flash as $type => $msg): ?>
    <div class="alert alert-<?= $type ?>">
        <i data-lucide="<?= $type === 'success' ? 'check-circle' : 'alert-circle' ?>"></i>
        <?= htmlspecialchars($msg) ?>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- ═══════════════ FEATURED ═══════════════ -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <div>
                <h2 class="section-title">Featured Properties</h2>
                <p class="section-sub">Handpicked stays based on ratings and amenities.</p>
            </div>
            <a href="<?= $baseUrl ?>/property" class="btn btn-outline">View All <i data-lucide="arrow-right"></i></a>
        </div>

        <?php if (empty($featured)): ?>
            <p class="text-muted text-center py-5">No featured properties yet.</p>
        <?php else: ?>

        <div class="featured-top">
            <?php $hero = $featured[0]; ?>
            <a href="<?= $baseUrl ?>/property/detail/<?= $hero['id'] ?>" class="featured-hero">
                <div class="card-img-wrap" style="height:340px;">
                    <?php if ($hero['badge']): ?>
                    <span class="card-badge badge-<?= strtolower(str_replace(' ', '-', $hero['badge'])) ?>"><?= htmlspecialchars($hero['badge']) ?></span>
                    <?php endif; ?>
                    <?php $src = $imgUrl($hero['primary_image']); ?>
                    <?php if ($src): ?>
                    <img src="<?= htmlspecialchars($src) ?>" alt="<?= htmlspecialchars($hero['title']) ?>" class="card-img">
                    <?php else: ?>
                    <div class="card-img card-no-img"><i data-lucide="image"></i></div>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="card-meta">
                        <span class="prop-type-badge"><?= \App\Models\Property::typeLabel($hero['type']) ?></span>
                        <span class="card-rating"><i data-lucide="star"></i> <?= number_format((float)$hero['rating'], 1) ?></span>
                    </div>
                    <h3 class="card-title"><?= htmlspecialchars($hero['title']) ?></h3>
                    <p class="card-location"><i data-lucide="map-pin"></i> <?= htmlspecialchars($hero['location']) ?></p>
                    <div class="card-price-row">
                        <span class="card-price">UGX <?= number_format((float)$hero['price']) ?> <small>/<?= $hero['price_period'] ?></small></span>
                    </div>
                </div>
            </a>

            <?php if (!empty($featured[1])): $side = $featured[1]; ?>
            <div class="featured-side">
                <a href="<?= $baseUrl ?>/property/detail/<?= $side['id'] ?>" class="card">
                    <div class="card-img-wrap" style="height:160px;">
                        <?php if ($side['badge']): ?>
                        <span class="card-badge badge-<?= strtolower(str_replace(' ', '-', $side['badge'])) ?>"><?= htmlspecialchars($side['badge']) ?></span>
                        <?php endif; ?>
                        <?php $src = $imgUrl($side['primary_image']); ?>
                        <?php if ($src): ?>
                        <img src="<?= htmlspecialchars($src) ?>" alt="<?= htmlspecialchars($side['title']) ?>" class="card-img">
                        <?php else: ?>
                        <div class="card-img card-no-img"><i data-lucide="image"></i></div>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <p class="card-meta-sm"><?= htmlspecialchars($side['location']) ?> &bull; <?= \App\Models\Property::typeLabel($side['type']) ?></p>
                        <h4 class="card-title-sm"><?= htmlspecialchars($side['title']) ?></h4>
                        <div class="card-price-row">
                            <span class="card-price">UGX <?= number_format((float)$side['price']) ?> <small>/mo</small></span>
                            <span class="card-rating"><i data-lucide="star"></i> <?= number_format((float)$side['rating'], 1) ?></span>
                        </div>
                    </div>
                </a>
            </div>
            <?php endif; ?>
        </div>

        <?php $rest = array_slice($featured, 2); ?>
        <?php if ($rest): ?>
        <div class="prop-grid mt-4">
            <?php foreach ($rest as $prop): ?>
            <a href="<?= $baseUrl ?>/property/detail/<?= $prop['id'] ?>" class="card prop-card">
                <div class="card-img-wrap">
                    <?php if ($prop['badge']): ?>
                    <span class="card-badge badge-<?= strtolower(str_replace(' ', '-', $prop['badge'])) ?>"><?= htmlspecialchars($prop['badge']) ?></span>
                    <?php endif; ?>
                    <?php $src = $imgUrl($prop['primary_image']); ?>
                    <?php if ($src): ?>
                    <img src="<?= htmlspecialchars($src) ?>" alt="<?= htmlspecialchars($prop['title']) ?>" class="card-img" loading="lazy">
                    <?php else: ?>
                    <div class="card-img card-no-img"><i data-lucide="image"></i></div>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <p class="card-meta-sm"><?= \App\Models\Property::typeLabel($prop['type']) ?> &bull; <?= htmlspecialchars($prop['district'] ?? $prop['location']) ?></p>
                    <h4 class="card-title-sm"><?= htmlspecialchars($prop['title']) ?></h4>
                    <div class="card-price-row">
                        <span class="card-price">UGX <?= number_format((float)$prop['price']) ?> <small>/mo</small></span>
                        <span class="card-rating"><i data-lucide="star"></i> <?= number_format((float)$prop['rating'], 1) ?></span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php endif; ?>
    </div>
</section>

<!-- ═══════════════ STATS ═══════════════ -->
<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <span class="stat-number"><?= ($stats['total'] ?? 0) ?>+</span>
                <span class="stat-label">Properties Managed</span>
            </div>
            <div class="stat-item">
                <span class="stat-number"><?= ($stats['available'] ?? 0) ?></span>
                <span class="stat-label">Active Listings</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">6</span>
                <span class="stat-label">Districts Covered</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">4.7</span>
                <span class="stat-label">Average Rating</span>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════ HOW IT WORKS ═══════════════ -->
<section class="section how-section">
    <div class="container">
        <h2 class="section-title text-center">How PearlNest Works</h2>
        <p class="section-sub text-center mb-5">We act as your trusted broker — bridging the gap between property owners and tenants securely.</p>
        <div class="how-grid">
            <div class="how-card">
                <div class="how-icon"><i data-lucide="search"></i></div>
                <h3>1. Browse &amp; Filter</h3>
                <p>Search our verified listings by location, price, type, and rating. Find the perfect fit for your budget and lifestyle.</p>
            </div>
            <div class="how-card">
                <div class="how-icon"><i data-lucide="send"></i></div>
                <h3>2. Send Inquiry</h3>
                <p>Reach out through our secure inquiry form. Your details go directly to our broker — never to a stranger online.</p>
            </div>
            <div class="how-card">
                <div class="how-icon"><i data-lucide="handshake"></i></div>
                <h3>3. Broker Connects You</h3>
                <p>Our broker verifies the property, arranges viewings, and facilitates a safe introduction to the property owner.</p>
            </div>
            <div class="how-card">
                <div class="how-icon"><i data-lucide="key"></i></div>
                <h3>4. Move In</h3>
                <p>Finalise the agreement, pay the deposit, and get your keys. Our broker supports you through every step.</p>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════ CTA ═══════════════ -->
<section class="cta-section">
    <div class="container text-center">
        <h2>Ready to Find Your Perfect Space?</h2>
        <p>Browse hundreds of verified properties across Kampala and surrounding areas.</p>
        <div class="cta-btns">
            <a href="<?= $baseUrl ?>/property" class="btn btn-white">Browse All Properties</a>
            <a href="<?= $baseUrl ?>/contact" class="btn btn-outline-white">Talk to Our Broker</a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>
