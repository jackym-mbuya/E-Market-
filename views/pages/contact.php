<?php
$page_title = 'Contact Us';
require_once '../layouts/header.php';

$success = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $success = 'Thank you for contacting us! We will get back to you soon.';
}
?>
<!-- Main Content -->
<main class="main-content">
    <div class="container">
        <nav aria-label="breadcrumb" class="breadcrumb-nav">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
                <li class="breadcrumb-item active">Contact Us</li>
            </ol>
        </nav>
        
        <?php if($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-8">
                <div class="contact-form-card">
                    <h2>Contact Us</h2>
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Your Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <input type="text" name="subject" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea name="message" class="form-control" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </form>
                </div>
            </div>
            <div class="col-md-4">
                <div class="contact-info-card">
                    <h3>Get In Touch</h3>
                    <div class="contact-info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?php echo $settings['site_address'] ?? 'Address'; ?></span>
                    </div>
                    <div class="contact-info-item">
                        <i class="fas fa-phone"></i>
                        <span><?php echo $settings['site_phone'] ?? 'Phone'; ?></span>
                    </div>
                    <div class="contact-info-item">
                        <i class="fas fa-envelope"></i>
                        <span><?php echo $settings['site_email'] ?? 'Email'; ?></span>
                    </div>
                    <div class="contact-info-item">
                        <i class="fas fa-clock"></i>
                        <span>Mon - Fri: 9:00 AM - 6:00 PM</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../layouts/footer.php'; ?>
