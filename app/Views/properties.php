<?php
/** @var string $baseUrl */
/** @var callable $imgUrl */
/** @var array $properties */
/** @var int $total */
/** @var int $page */
/** @var int $pages */
/** @var array $filters */
/** @var array $districts */
/** @var array $typeOptions */
/** @var array $flash */
require __DIR__ . '/partials/header.php';
?>

<div class="page-top">
    <div class="container">
        <h1>Browse Properties</h1>
        <p><?= number_format($total) ?> propert<?= $total === 1 ? 'y' : 'ies' ?> found<?= $filters['search'] ? ' for "' . htmlspecialchars($filters['search']) . '"' : '' ?>
            <?= $filters['district'] ? ' in ' . htmlspecialchars($filters['district']) : '' ?></p>
    </div>
</div>

<div class="container browse-layout">

    <!-- ── SIDEBAR ── -->
    <aside class="filter-sidebar">
        <div class="filter-box">
            <div class="filter-header">
                <span class="filter-title">Filters</span>
                <a href="<?= $baseUrl ?>/property" class="filter-clear">Clear all</a>
            </div>

            <form action="<?= $baseUrl ?>/property" method="GET" id="filterForm">

                <div class="filter-group">
                    <label class="filter-label">Search</label>
                    <div class="search-input-wrap">
                        <i data-lucide="search"></i>
                        <input type="text" name="search" placeholder="Location, keyword…"
                               value="<?= htmlspecialchars($filters['search']) ?>" class="form-input">
                    </div>
                </div>

                <div class="filter-group">
                    <label class="filter-label">Price Range (UGX/month)</label>
                    <div class="price-inputs">
                        <input type="number" name="min_price" placeholder="Min"
                               value="<?= htmlspecialchars($filters['min_price']) ?>" class="form-input">
                        <span>–</span>
                        <input type="number" name="max_price" placeholder="Max"
                               value="<?= htmlspecialchars($filters['max_price']) ?>" class="form-input">
                    </div>
                </div>

                <div class="filter-group">
                    <label class="filter-label">District / Location</label>
                    <select name="district" class="form-input">
                        <option value="">All Districts</option>
                        <?php foreach ($districts as $d): ?>
                        <option value="<?= htmlspecialchars($d) ?>" <?= $filters['district'] === $d ? 'selected' : '' ?>>
                            <?= htmlspecialchars($d) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">Property Type</label>
                    <?php foreach ($typeOptions as $val => $label): ?>
                    <label class="checkbox-label">
                        <input type="radio" name="type" value="<?= $val ?>"
                               <?= $filters['type'] === $val ? 'checked' : '' ?>>
                        <?= $label ?>
                    </label>
                    <?php endforeach; ?>
                    <label class="checkbox-label">
                        <input type="radio" name="type" value="" <?= $filters['type'] === '' ? 'checked' : '' ?>> All Types
                    </label>
                </div>

                <div class="filter-group">
                    <label class="filter-label">Min Bedrooms</label>
                    <select name="bedrooms" class="form-input">
                        <option value="">Any</option>
                        <?php foreach ([1,2,3] as $n): ?>
                        <option value="<?= $n ?>" <?= (string)$filters['bedrooms'] === (string)$n ? 'selected' : '' ?>><?= $n ?>+</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">Minimum Rating</label>
                    <div class="rating-btns">
                        <?php foreach (['', '3', '4', '4.5'] as $r): ?>
                        <button type="button" class="rating-btn <?= $filters['min_rating'] === $r ? 'active' : '' ?>"
                                onclick="document.querySelector('[name=min_rating]').value='<?= $r ?>'; document.getElementById('filterForm').submit()">
                            <?= $r ? $r . '+ ★' : 'All' ?>
                        </button>
                        <?php endforeach; ?>
                        <input type="hidden" name="min_rating" value="<?= htmlspecialchars($filters['min_rating']) ?>">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <i data-lucide="filter"></i> Apply Filters
                </button>
            </form>
        </div>
    </aside>

    <!-- ── MAIN ── -->
    <main class="browse-main">

        <!-- Flash -->
        <?php if (!empty($flash)): ?>
            <?php foreach ($flash as $type => $msg): ?>
            <div class="alert alert-<?= $type ?>">
                <i data-lucide="<?= $type === 'success' ? 'check-circle' : 'alert-circle' ?>"></i>
                <?= htmlspecialchars($msg) ?>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Sort bar -->
        <div class="browse-bar">
            <p class="results-count"><?= number_format($total) ?> result<?= $total !== 1 ? 's' : '' ?></p>
            <div class="sort-wrap">
                <label>Sort by:</label>
                <select name="sort" id="sortSelect" class="form-input sort-select" onchange="applySort(this.value)">
                    <option value=""           <?= $filters['sort'] === ''           ? 'selected' : '' ?>>Best Match</option>
                    <option value="rating"     <?= $filters['sort'] === 'rating'     ? 'selected' : '' ?>>Highest Rated</option>
                    <option value="price_asc"  <?= $filters['sort'] === 'price_asc'  ? 'selected' : '' ?>>Price: Low → High</option>
                    <option value="price_desc" <?= $filters['sort'] === 'price_desc' ? 'selected' : '' ?>>Price: High → Low</option>
                    <option value="newest"     <?= $filters['sort'] === 'newest'     ? 'selected' : '' ?>>Newest</option>
                </select>
            </div>
        </div>

        <?php if (empty($properties)): ?>
        <div class="empty-state">
            <i data-lucide="search" style="width:48px;height:48px;color:var(--gray-200)"></i>
            <h3>No properties found</h3>
            <p>Try adjusting your filters or <a href="<?= $baseUrl ?>/property">clear all filters</a>.</p>
        </div>
        <?php else: ?>

        <div class="prop-list">
            <?php foreach ($properties as $prop): ?>
            <div class="prop-list-card">
                <a href="<?= $baseUrl ?>/property/detail/<?= $prop['id'] ?>" class="prop-list-img-wrap">
                    <?php if ($prop['badge']): ?>
                    <span class="card-badge badge-<?= strtolower(str_replace(' ', '-', $prop['badge'])) ?>"><?= htmlspecialchars($prop['badge']) ?></span>
                    <?php endif; ?>
                    <?php $src = $imgUrl($prop['primary_image']); ?>
                    <?php if ($src): ?>
                    <img src="<?= htmlspecialchars($src) ?>" alt="<?= htmlspecialchars($prop['title']) ?>" class="card-img" loading="lazy">
                    <?php else: ?>
                    <div class="card-img card-no-img"><i data-lucide="image"></i></div>
                    <?php endif; ?>
                </a>
                <div class="prop-list-body">
                    <div class="prop-list-meta">
                        <span class="prop-type-badge"><?= \App\Models\Property::typeLabel($prop['type']) ?></span>
                        <span class="prop-district"><i data-lucide="map-pin"></i> <?= htmlspecialchars($prop['location']) ?></span>
                    </div>
                    <h3 class="prop-list-title">
                        <a href="<?= $baseUrl ?>/property/detail/<?= $prop['id'] ?>"><?= htmlspecialchars($prop['title']) ?></a>
                    </h3>
                    <p class="prop-list-desc"><?= htmlspecialchars(substr($prop['description'] ?? '', 0, 120)) ?>…</p>
                    <div class="prop-list-features">
                        <span><i data-lucide="bed"></i> <?= $prop['bedrooms'] ?> Bed<?= $prop['bedrooms'] > 1 ? 's' : '' ?></span>
                        <span><i data-lucide="bath"></i> <?= $prop['bathrooms'] ?> Bath<?= $prop['bathrooms'] > 1 ? 's' : '' ?></span>
                        <?php if ($prop['area_sqm']): ?>
                        <span><i data-lucide="ruler"></i> <?= $prop['area_sqm'] ?> m²</span>
                        <?php endif; ?>
                    </div>
                    <div class="prop-list-footer">
                        <div>
                            <span class="card-price">UGX <?= number_format((float)$prop['price']) ?> <small>/<?= $prop['price_period'] ?></small></span>
                        </div>
                        <div class="prop-list-actions">
                            <span class="card-rating"><i data-lucide="star"></i> <?= number_format((float)$prop['rating'], 1) ?> (<?= $prop['rating_count'] ?>)</span>
                            <a href="<?= $baseUrl ?>/property/detail/<?= $prop['id'] ?>" class="btn btn-sm btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($pages > 1): ?>
        <div class="pagination">
            <?php
            $qs    = http_build_query(array_filter(array_merge($filters, ['page' => null])));
            $qsSep = $qs ? '&' : '';
            ?>
            <?php if ($page > 1): ?>
            <a href="<?= $baseUrl ?>/property?<?= $qs . $qsSep ?>page=<?= $page - 1 ?>" class="page-btn"><i data-lucide="chevron-left"></i></a>
            <?php endif; ?>
            <?php for ($p = max(1, $page - 2); $p <= min($pages, $page + 2); $p++): ?>
            <a href="<?= $baseUrl ?>/property?<?= $qs . $qsSep ?>page=<?= $p ?>"
               class="page-btn <?= $p === $page ? 'active' : '' ?>"><?= $p ?></a>
            <?php endfor; ?>
            <?php if ($page < $pages): ?>
            <a href="<?= $baseUrl ?>/property?<?= $qs . $qsSep ?>page=<?= $page + 1 ?>" class="page-btn"><i data-lucide="chevron-right"></i></a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php endif; ?>
    </main>
</div>

<script>
function applySort(val) {
    const url = new URL(window.location.href);
    url.searchParams.set('sort', val);
    window.location.href = url.toString();
}
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
