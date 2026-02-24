<?php
require_once '../../config/functions.php';

$settings = getSettings();
?>
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h3>About eMarket</h3>
                    <p>We are a leading e-commerce platform offering quality products at competitive prices. Shop with confidence.</p>
                    <div class="social-links">
                        <a href="<?php echo $settings['facebook'] ?? '#'; ?>"><i class="fab fa-facebook-f"></i></a>
                        <a href="<?php echo $settings['twitter'] ?? '#'; ?>"><i class="fab fa-twitter"></i></a>
                        <a href="<?php echo $settings['instagram'] ?? '#'; ?>"><i class="fab fa-instagram"></i></a>
                        <a href="<?php echo $settings['youtube'] ?? '#'; ?>"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h3>Customer Service</h3>
                    <ul class="footer-links">
                        <li><a href="#">Shipping & Returns</a></li>
                        <li><a href="#">Order Tracking</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms & Conditions</a></li>
                        <li><a href="#">Contact Us</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3>Categories</h3>
                    <ul class="footer-links">
                        <li><a href="#">Electronics</a></li>
                        <li><a href="#">Fashion</a></li>
                        <li><a href="#">Home & Garden</a></li>
                        <li><a href="#">Health & Beauty</a></li>
                        <li><a href="#">Sports & Toys</a></li>
                        <li><a href="#">Books & Media</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3>Newsletter</h3>
                    <p>Subscribe to get special offers and updates</p>
                    <form class="newsletter-form">
                        <input type="email" placeholder="Enter your email">
                        <button type="submit" class="btn btn-primary">Subscribe</button>
                    </form>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> eMarket. All rights reserved.</p>
                <div class="payment-methods">
                    <i class="fab fa-cc-visa"></i>
                    <i class="fab fa-cc-mastercard"></i>
                    <i class="fab fa-cc-paypal"></i>
                    <i class="fab fa-cc-amex"></i>
                </div>
            </div>
        </div>
    </footer>

    <!-- Quick View Modal -->
    <div id="quickViewModal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal('quickViewModal')">&times;</span>
            <div id="quickViewContent"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/script.js"></script>
</body>
</html>
