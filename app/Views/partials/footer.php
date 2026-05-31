<?php /** @var string $baseUrl */ ?>

<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="<?= $baseUrl ?>/" class="navbar-brand mb-3" style="display:inline-flex;">
                    <div class="logo-icon"><i data-lucide="home"></i></div>
                    <span class="logo-text">Pearl<span class="logo-accent">Nest</span></span>
                </a>
                <p class="footer-tagline">Uganda's trusted broker for hostels, rentals, and apartments. Connecting you to verified properties across Kampala and beyond.</p>
                <div class="footer-social">
                    <a href="#" aria-label="Facebook"><i data-lucide="facebook"></i></a>
                    <a href="#" aria-label="Instagram"><i data-lucide="instagram"></i></a>
                    <a href="#" aria-label="WhatsApp"><i data-lucide="message-circle"></i></a>
                    <a href="#" aria-label="Twitter/X"><i data-lucide="twitter"></i></a>
                </div>
            </div>

            <div class="footer-col">
                <h4>Properties</h4>
                <ul>
                    <li><a href="<?= $baseUrl ?>/property?type=hostel_shared">Shared Hostels</a></li>
                    <li><a href="<?= $baseUrl ?>/property?type=hostel_private">Private Hostel Rooms</a></li>
                    <li><a href="<?= $baseUrl ?>/property?type=studio">Studio Apartments</a></li>
                    <li><a href="<?= $baseUrl ?>/property?type=1br">1 Bedroom</a></li>
                    <li><a href="<?= $baseUrl ?>/property?type=2br">2 Bedroom</a></li>
                    <li><a href="<?= $baseUrl ?>/property?type=3br">3+ Bedroom</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Locations</h4>
                <ul>
                    <li><a href="<?= $baseUrl ?>/property?district=Kampala+Central">Kampala Central</a></li>
                    <li><a href="<?= $baseUrl ?>/property?district=Nakawa">Nakawa</a></li>
                    <li><a href="<?= $baseUrl ?>/property?district=Makindye">Makindye</a></li>
                    <li><a href="<?= $baseUrl ?>/property?district=Rubaga">Rubaga</a></li>
                    <li><a href="<?= $baseUrl ?>/property?district=Kawempe">Kawempe</a></li>
                    <li><a href="<?= $baseUrl ?>/property?district=Wakiso">Wakiso</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Support</h4>
                <ul>
                    <li><a href="<?= $baseUrl ?>/about">About Us</a></li>
                    <li><a href="<?= $baseUrl ?>/contact">Contact Broker</a></li>
                    <li><a href="<?= $baseUrl ?>/contact">FAQ</a></li>
                </ul>
                <div class="footer-contact-info">
                    <p><i data-lucide="phone"></i> +256 700 123 456</p>
                    <p><i data-lucide="mail"></i> info@pearlnest.ug</p>
                    <p><i data-lucide="map-pin"></i> Kampala, Uganda</p>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> PearlNest Uganda. All rights reserved. &mdash; Pearl of Africa, one home at a time.</p>
        </div>
    </div>
</footer>

<script src="<?= $baseUrl ?>/js/lucide.min.js"></script>
<script src="<?= $baseUrl ?>/js/main.js"></script>
</body>
</html>
