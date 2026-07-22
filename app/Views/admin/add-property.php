<?php
/** @var string $baseUrl */
/** @var array $typeOptions */
/** @var array $flash */
require __DIR__ . '/layout.php';
?>

<div class="admin-page-header">
    <div>
        <h2>Add New Property</h2>
        <p class="admin-page-sub">Fill in the details below to list a new property.</p>
    </div>
    <a href="<?= $baseUrl ?>/admin/properties" class="btn btn-outline"><i data-lucide="arrow-left"></i> Back</a>
</div>

<form action="<?= $baseUrl ?>/admin/add" method="POST" enctype="multipart/form-data">
    <div class="form-two-col">

        <!-- LEFT -->
        <div class="form-col">
            <div class="admin-card">
                <div class="admin-card-header"><h3>Basic Information</h3></div>
                <div class="admin-card-body">
                    <div class="form-group">
                        <label>Property Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-input" placeholder="e.g. Kololo Executive 2BR" required>
                    </div>
                    <div class="form-row-2">
                        <div class="form-group">
                            <label>Property Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-input" required>
                                <?php foreach ($typeOptions as $val => $label): ?>
                                <option value="<?= $val ?>"><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-input">
                                <option value="available">Available</option>
                                <option value="under_review">Under Review</option>
                                <option value="rented">Rented</option>
                                <option value="sold">Sold</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-input" rows="5"
                                  placeholder="Describe the property, its features, surrounding area…"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Amenities <small class="text-muted">(comma-separated)</small></label>
                        <input type="text" name="amenities" class="form-input"
                               placeholder="Wi-Fi, Parking, Security, Generator, Air Conditioning">
                    </div>
                    <div class="form-group">
                        <label>Badge <small class="text-muted">(optional highlight)</small></label>
                        <select name="badge" class="form-input">
                            <option value="">None</option>
                            <option value="VERIFIED">VERIFIED</option>
                            <option value="POPULAR">POPULAR</option>
                            <option value="LAST AVAILABLE">LAST AVAILABLE</option>
                            <option value="NEW">NEW</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_featured" value="1">
                            <strong>Feature this property</strong> (shows on homepage)
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT -->
        <div class="form-col">
            <div class="admin-card">
                <div class="admin-card-header"><h3>Location &amp; Pricing</h3></div>
                <div class="admin-card-body">
                    <div class="form-group">
                        <label>Location <span class="text-danger">*</span></label>
                        <input type="text" name="location" class="form-input" placeholder="e.g. Kololo, Kampala" required>
                    </div>
                    <div class="form-row-2">
                        <div class="form-group">
                            <label>District</label>
                            <select name="district" class="form-input">
                                <option value="">Select district</option>
                                <?php foreach (['Kampala Central','Nakawa','Makindye','Rubaga','Kawempe','Wakiso','Mukono','Entebbe'] as $d): ?>
                                <option value="<?= $d ?>"><?= $d ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Street / Address</label>
                            <input type="text" name="address" class="form-input" placeholder="Plot 5, Upper Road…">
                        </div>
                    </div>
                    <div class="form-row-2">
                        <div class="form-group">
                            <label>Price (UGX) <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-input" placeholder="850000" min="0" required>
                        </div>
                        <div class="form-group">
                            <label>Price Period</label>
                            <select name="price_period" class="form-input">
                                <option value="month">Per Month</option>
                                <option value="night">Per Night</option>
                                <option value="year">Per Year</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row-2">
                        <div class="form-group">
                            <label>Touring Fee (UGX)</label>
                            <input type="number" name="touring_fee" class="form-input" placeholder="0" min="0" step="0.01">
                        </div>
                        <div class="form-group"></div>
                    </div>
                    <div class="form-row-3">
                        <div class="form-group">
                            <label>Bedrooms</label>
                            <input type="number" name="bedrooms" class="form-input" value="1" min="1">
                        </div>
                        <div class="form-group">
                            <label>Bathrooms</label>
                            <input type="number" name="bathrooms" class="form-input" value="1" min="1">
                        </div>
                        <div class="form-group">
                            <label>Area (m²)</label>
                            <input type="number" name="area_sqm" class="form-input" placeholder="65">
                        </div>
                    </div>
                </div>
            </div>

            <div class="admin-card mt-3">
                <div class="admin-card-header"><h3>Images &amp; Video</h3></div>
                <div class="admin-card-body">
                    <div class="form-group">
                        <label>Property Images <small class="text-muted">(JPEG/PNG/WEBP, max 5 MB each)</small></label>
                        <div class="file-upload-area" id="imageDropZone">
                            <i data-lucide="upload-cloud" style="width:36px;height:36px;margin-bottom:8px;"></i>
                            <p>Drag &amp; drop images or click to browse</p>
                            <input type="file" name="images[]" id="imageInput" multiple accept="image/*" class="file-input-hidden">
                        </div>
                        <div id="imagePreview" class="image-preview-grid"></div>
                    </div>
                    <div class="form-group">
                        <label>Property Video <small class="text-muted">(MP4/WEBM, max 50 MB)</small></label>
                        <input type="text" name="video_title" class="form-input mb-2" placeholder="Video title (optional)">
                        <input type="file" name="video" accept="video/*" class="form-input">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div style="display:flex;gap:12px;margin-top:16px;">
        <button type="submit" class="btn btn-primary"><i data-lucide="save"></i> Save Property</button>
        <a href="<?= $baseUrl ?>/admin/properties" class="btn btn-outline">Cancel</a>
    </div>
</form>

<script>
const imageInput  = document.getElementById('imageInput');
const previewGrid = document.getElementById('imagePreview');
const dropZone    = document.getElementById('imageDropZone');

dropZone.addEventListener('click', () => imageInput.click());
dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('drag-over'); });
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over'));
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('drag-over');
    showPreviews(e.dataTransfer.files);
});
imageInput.addEventListener('change', () => showPreviews(imageInput.files));

function showPreviews(files) {
    previewGrid.innerHTML = '';
    Array.from(files).forEach((file, i) => {
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.className = 'preview-thumb';
            div.innerHTML = `<img src="${e.target.result}" alt="preview">${i === 0 ? '<span>Primary</span>' : ''}`;
            previewGrid.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}
</script>

<?php require __DIR__ . '/layout-end.php'; ?>
