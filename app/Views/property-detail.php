<?php
/** @var string $baseUrl */
/** @var callable $imgUrl */
/** @var array $property */
/** @var array $ratings */
/** @var array $related */
/** @var array $flash */
require __DIR__ . '/partials/header.php';
?>

<div class="container detail-layout">

    <!-- ── LEFT: property info ── -->
    <div class="detail-main">

        <!-- Breadcrumb -->
        <nav class="breadcrumb-nav">
            <a href="<?= $baseUrl ?>/">Home</a> <i data-lucide="chevron-right"></i>
            <a href="<?= $baseUrl ?>/property">Properties</a> <i data-lucide="chevron-right"></i>
            <span><?= htmlspecialchars($property['title']) ?></span>
        </nav>

        <!-- Gallery -->
        <?php $images = $property['images']; ?>
        <?php if ($images): ?>
        <div class="gallery">
            <?php $primary = array_filter($images, fn($i) => $i['is_primary']); ?>
            <?php $primaryImg = reset($primary) ?: $images[0]; ?>
            <div class="gallery-main">
                <?php if ($property['badge']): ?>
                <span class="card-badge badge-<?= strtolower(str_replace(' ', '-', $property['badge'])) ?>"><?= htmlspecialchars($property['badge']) ?></span>
                <?php endif; ?>
                <img src="<?= htmlspecialchars($imgUrl($primaryImg['image_path'])) ?>"
                     alt="<?= htmlspecialchars($property['title']) ?>"
                     id="galleryMain" class="gallery-main-img">
            </div>
            <?php if (count($images) > 1): ?>
            <div class="gallery-thumbs">
                <?php foreach ($images as $img): ?>
                <img src="<?= htmlspecialchars($imgUrl($img['image_path'])) ?>"
                     alt="thumbnail"
                     class="gallery-thumb <?= $img['is_primary'] ? 'active' : '' ?>"
                     onclick="document.getElementById('galleryMain').src=this.src; document.querySelectorAll('.gallery-thumb').forEach(t=>t.classList.remove('active')); this.classList.add('active')">
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="gallery-placeholder">
            <i data-lucide="image" style="width:64px;height:64px"></i>
            <p>No images uploaded yet</p>
        </div>
        <?php endif; ?>

        <!-- Flash -->
        <?php if (!empty($flash)): ?>
            <?php foreach ($flash as $type => $msg): ?>
            <div class="alert alert-<?= $type ?> mt-3">
                <i data-lucide="<?= $type === 'success' ? 'check-circle' : 'alert-circle' ?>"></i>
                <?= htmlspecialchars($msg) ?>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Title block -->
        <div class="detail-title-block">
            <div>
                <span class="prop-type-badge"><?= \App\Models\Property::typeLabel($property['type']) ?></span>
                <?php if ($property['status'] !== 'available'): ?>
                <span class="status-badge status-<?= $property['status'] ?>"><?= ucfirst(str_replace('_', ' ', $property['status'])) ?></span>
                <?php endif; ?>
                <h1><?= htmlspecialchars($property['title']) ?></h1>
                <p class="detail-location"><i data-lucide="map-pin"></i> <?= htmlspecialchars($property['address'] ?: $property['location']) ?>, <?= htmlspecialchars($property['district'] ?? '') ?></p>
            </div>
            <div class="detail-price-block">
                <span class="detail-price">UGX <?= number_format((float)$property['price']) ?></span>
                <span class="detail-period">/ <?= $property['price_period'] ?></span>
                <div class="detail-rating">
                    <?php for ($s = 1; $s <= 5; $s++): ?>
                        <i data-lucide="star" class="<?= $s <= round($property['rating']) ? 'text-gold' : 'text-muted' ?>"></i>
                    <?php endfor; ?>
                    <span><?= number_format((float)$property['rating'], 1) ?> (<?= $property['rating_count'] ?> review<?= $property['rating_count'] != 1 ? 's' : '' ?>)</span>
                </div>
            </div>
        </div>

        <!-- Key facts -->
        <div class="detail-facts">
            <div class="fact-item"><i data-lucide="bed"></i><span><?= $property['bedrooms'] ?> Bedroom<?= $property['bedrooms'] > 1 ? 's' : '' ?></span></div>
            <div class="fact-item"><i data-lucide="bath"></i><span><?= $property['bathrooms'] ?> Bathroom<?= $property['bathrooms'] > 1 ? 's' : '' ?></span></div>
            <?php if ($property['area_sqm']): ?>
            <div class="fact-item"><i data-lucide="ruler"></i><span><?= $property['area_sqm'] ?> m²</span></div>
            <?php endif; ?>
            <div class="fact-item"><i data-lucide="calendar-check"></i><span><?= ucfirst(str_replace('_', ' ', $property['status'])) ?></span></div>
        </div>

        <!-- Description -->
        <?php if ($property['description']): ?>
        <div class="detail-section">
            <h2>About this Property</h2>
            <p><?= nl2br(htmlspecialchars($property['description'])) ?></p>
        </div>
        <?php endif; ?>

        <!-- Amenities -->
        <?php if (!empty($property['amenities'])): ?>
        <div class="detail-section">
            <h2>Amenities</h2>
            <ul class="amenities-list">
                <?php foreach ($property['amenities'] as $amenity): ?>
                <li><i data-lucide="check-circle"></i> <?= htmlspecialchars(trim($amenity)) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Video -->
        <?php if (!empty($property['videos'])): ?>
        <div class="detail-section">
            <h2>Property Video</h2>
            <?php foreach ($property['videos'] as $video): ?>
            <video controls class="detail-video">
                <source src="<?= $baseUrl . '/' . htmlspecialchars($video['video_path']) ?>" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <?php if ($video['title']): ?>
            <p class="video-caption"><?= htmlspecialchars($video['title']) ?></p>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Reviews -->
        <div class="detail-section">
            <h2>Reviews</h2>
            <?php if (empty($ratings)): ?>
            <p class="text-muted">No reviews yet. Be the first to review this property!</p>
            <?php else: ?>
            <div class="reviews-list">
                <?php foreach ($ratings as $r): ?>
                <div class="review-item">
                    <div class="review-header">
                        <div class="reviewer-avatar"><?= strtoupper(substr($r['name'] ?? 'A', 0, 1)) ?></div>
                        <div>
                            <strong><?= htmlspecialchars($r['name'] ?? 'Anonymous') ?></strong>
                            <div class="review-stars">
                                <?php for ($s = 1; $s <= 5; $s++): ?>
                                <i data-lucide="star" class="<?= $s <= $r['rating'] ? 'text-gold' : 'text-muted' ?>"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <span class="review-date"><?= date('M Y', strtotime($r['created_at'])) ?></span>
                    </div>
                    <?php if ($r['review']): ?>
                    <p class="review-text"><?= htmlspecialchars($r['review']) ?></p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Add review form -->
            <div class="review-form-box">
                <h3>Leave a Review</h3>
                <form action="<?= $baseUrl ?>/rating/submit" method="POST">
                    <input type="hidden" name="property_id" value="<?= $property['id'] ?>">
                    <div class="form-group">
                        <label>Your Name</label>
                        <input type="text" name="reviewer_name" class="form-input" placeholder="Optional">
                    </div>
                    <div class="form-group">
                        <label>Rating <span class="text-danger">*</span></label>
                        <div class="star-picker" id="starPicker">
                            <?php for ($s = 1; $s <= 5; $s++): ?>
                            <span class="star-pick" data-val="<?= $s ?>">★</span>
                            <?php endfor; ?>
                        </div>
                        <input type="hidden" name="rating" id="ratingInput" required>
                    </div>
                    <div class="form-group">
                        <label>Your Review</label>
                        <textarea name="review" class="form-input" rows="3" placeholder="Tell others about this property…"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </form>
            </div>
        </div>

        <!-- Related properties -->
        <?php if (!empty($related)): ?>
        <div class="detail-section">
            <h2>Similar Properties in <?= htmlspecialchars($property['district'] ?? $property['location']) ?></h2>
            <div class="prop-grid">
                <?php foreach ($related as $rel): ?>
                <a href="<?= $baseUrl ?>/property/detail/<?= $rel['id'] ?>" class="card prop-card">
                    <div class="card-img-wrap" style="height:160px;">
                        <?php $src = $imgUrl($rel['primary_image']); ?>
                        <?php if ($src): ?>
                        <img src="<?= htmlspecialchars($src) ?>" alt="<?= htmlspecialchars($rel['title']) ?>" class="card-img" loading="lazy">
                        <?php else: ?>
                        <div class="card-img card-no-img"><i data-lucide="image"></i></div>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <p class="card-meta-sm"><?= \App\Models\Property::typeLabel($rel['type']) ?></p>
                        <h4 class="card-title-sm"><?= htmlspecialchars($rel['title']) ?></h4>
                        <span class="card-price">UGX <?= number_format((float)$rel['price']) ?> <small>/mo</small></span>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- ── RIGHT: Inquiry sidebar ── -->
    <aside class="detail-sidebar">
        <div class="inquiry-box sticky-sidebar">
            <h3><i data-lucide="send"></i> Contact the Broker</h3>
            <p class="inquiry-note">Interested in this property? Send us a message and our broker will be in touch within 24 hours.</p>

            <?php if ($property['status'] !== 'available'): ?>
            <div class="alert alert-warning">
                <i data-lucide="alert-triangle"></i>
                This property is currently <strong><?= ucfirst(str_replace('_', ' ', $property['status'])) ?></strong>. You may still enquire about availability.
            </div>
            <?php endif; ?>

            <form action="<?= $baseUrl ?>/property/inquiry" method="POST">
                <input type="hidden" name="property_id" value="<?= $property['id'] ?>">
                <div class="form-group">
                    <label>Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-input" placeholder="Your name" required>
                </div>
                <div class="form-group">
                    <label>Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-input" placeholder="your@email.com" required>
                </div>
                <div class="form-group">
                    <label>Phone (WhatsApp preferred)</label>
                    <input type="tel" name="phone" class="form-input" placeholder="+256 7XX XXX XXX">
                </div>
                <div class="form-group">
                    <label>Message <span class="text-danger">*</span></label>
                    <textarea name="message" class="form-input" rows="4" required
                              placeholder="Hi, I am interested in this property. When can I view it?"></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-block">
                    <i data-lucide="send"></i> Send Inquiry
                </button>
            </form>

            <div class="broker-note">
                <i data-lucide="shield"></i>
                <span>Your details are shared only with our verified broker — not with the public.</span>
            </div>
        </div>
    </aside>
</div>

<script>
// Star picker — re-run createIcons after state changes
const stars = document.querySelectorAll('.star-pick');
const ratingInput = document.getElementById('ratingInput');

function highlightStars(n) {
    stars.forEach((s, i) => {
        s.style.color = i < n ? 'var(--accent)' : 'var(--gray-200)';
    });
}

stars.forEach(s => {
    s.addEventListener('mouseover', () => highlightStars(+s.dataset.val));
    s.addEventListener('mouseout',  () => highlightStars(+ratingInput.value || 0));
    s.addEventListener('click',     () => {
        ratingInput.value = s.dataset.val;
        highlightStars(+s.dataset.val);
    });
});
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
