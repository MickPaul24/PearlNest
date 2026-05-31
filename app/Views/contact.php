<?php
/** @var string $baseUrl */
/** @var array $flash */
require __DIR__ . '/partials/header.php';
?>

<div class="page-top">
    <div class="container">
        <h1>Contact the Broker</h1>
        <p>Have a question or want to enquire about a property? We'd love to hear from you.</p>
    </div>
</div>

<section class="section">
    <div class="container contact-layout">

        <div class="contact-info-col">
            <h2>Get in Touch</h2>
            <p>PearlNest operates as your trusted broker. Whether you want to view a property, discuss pricing, or need guidance navigating the rental market in Uganda, we're here to help.</p>

            <div class="contact-details">
                <div class="contact-detail-item">
                    <div class="contact-icon"><i data-lucide="phone"></i></div>
                    <div>
                        <strong>Phone / WhatsApp</strong>
                        <p>+256 700 123 456</p>
                    </div>
                </div>
                <div class="contact-detail-item">
                    <div class="contact-icon"><i data-lucide="mail"></i></div>
                    <div>
                        <strong>Email</strong>
                        <p>info@pearlnest.ug</p>
                    </div>
                </div>
                <div class="contact-detail-item">
                    <div class="contact-icon"><i data-lucide="map-pin"></i></div>
                    <div>
                        <strong>Office</strong>
                        <p>Plot 1, Kampala Road<br>Kampala, Uganda</p>
                    </div>
                </div>
                <div class="contact-detail-item">
                    <div class="contact-icon"><i data-lucide="clock"></i></div>
                    <div>
                        <strong>Working Hours</strong>
                        <p>Mon – Fri: 8 AM – 6 PM<br>Sat: 9 AM – 2 PM</p>
                    </div>
                </div>
            </div>

            <div class="contact-social">
                <a href="#" aria-label="WhatsApp"><i data-lucide="message-circle"></i></a>
                <a href="#" aria-label="Facebook"><i data-lucide="facebook"></i></a>
                <a href="#" aria-label="Instagram"><i data-lucide="instagram"></i></a>
            </div>
        </div>

        <div class="contact-form-col">
            <?php if (!empty($flash)): ?>
                <?php foreach ($flash as $type => $msg): ?>
                <div class="alert alert-<?= $type ?>">
                    <i data-lucide="<?= $type === 'success' ? 'check-circle' : 'alert-circle' ?>"></i>
                    <?= htmlspecialchars($msg) ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="contact-form-box">
                <h2>Send a Message</h2>
                <form action="<?= $baseUrl ?>/contact/submit" method="POST">
                    <div class="form-group">
                        <label>Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-input" placeholder="Your full name" required>
                    </div>
                    <div class="form-row-2">
                        <div class="form-group">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-input" placeholder="your@email.com" required>
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="tel" name="phone" class="form-input" placeholder="+256 7XX XXX XXX">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Message <span class="text-danger">*</span></label>
                        <textarea name="message" class="form-input" rows="5" required
                                  placeholder="Tell us what you're looking for, or ask any question…"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i data-lucide="send"></i> Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>
