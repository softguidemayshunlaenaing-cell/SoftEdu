<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>SoftEdu | Excellence in Education</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        :root {
            --primary-green: #28a745;
            --bg-gray: #f8f9fa;
            /* Light gray background */
            --navbar-bg: lightgray;
            /* Light gray navbar */
        }

        body {
            background-color: var(--bg-gray);
        }

        /* Landing Page Navbar */
        .navbar {
            background-color: var(--navbar-bg) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .hero-gradient {
            background: linear-gradient(135deg, var(--primary-green) 0%, #10b981 100%);
        }

        .btn-emerald {
            background-color: var(--primary-green);
            color: white;
        }
    </style>
</head>

<body>
    <?php if (isset($_SESSION['success']) || isset($_SESSION['error'])): ?>
        <div class="position-fixed top-0 start-50 translate-middle-x mt-5 pt-3" style="z-index: 1060;">
            <div class="alert alert-<?= isset($_SESSION['success']) ? 'success' : 'danger' ?> shadow-lg rounded-pill px-4">
                <?= htmlspecialchars($_SESSION['success'] ?? $_SESSION['error']) ?>
                <?php unset($_SESSION['success'], $_SESSION['error']); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php include 'includes/navbar.php'; ?>

    <section id="home" class="hero-gradient text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 text-center text-lg-start">
                    <span class="badge bg-white text-success mb-3 px-3 py-2 rounded-pill fw-bold">
                        <i class="bi bi-stars me-1"></i> ADMISSIONS OPEN 2026
                    </span>
                    <h1 class="display-2 fw-bold mb-4"
                        style="font-family: 'Playfair Display', serif; line-height: 1.1;">
                        Empowering Minds,<br>Shaping Futures.
                    </h1>
                    <p class="fs-5 mb-5 opacity-90 fw-light">
                        Experience a new standard of academic excellence with SoftEdu.
                        Join 5,000+ students already advancing their careers.
                    </p>
                    <div class="d-flex flex-column flex-sm-row justify-content-center justify-content-lg-start gap-3">
                        <button class="btn btn-light btn-lg px-5 py-3 fw-bold rounded-pill text-success shadow"
                            data-bs-toggle="modal" data-bs-target="#applyModal">
                            Start Journey
                        </button>
                        <button class="btn btn-outline-light btn-lg px-5 py-3 fw-bold rounded-pill">
                            Explore Courses
                        </button>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-block">
                    <div class="position-relative">
                        <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&q=80&w=800"
                            class="img-fluid rounded-5 shadow-2xl" alt="Students">
                        <div class="position-absolute bottom-0 start-0 m-4 p-3 bg-white rounded-4 shadow-lg text-dark">
                            <div class="d-flex align-items-center gap-3">
                                <div class="icon-box mb-0" style="width: 40px; height: 40px;">
                                    <i class="bi bi-check-circle-fill"></i>
                                </div>
                                <span class="fw-bold">Accredited Excellence</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="learning-path" class="py-5">
        <div class="container pt-5">
            <div class="row g-4 justify-content-center">
                <div class="col-md-4">
                    <div class="card feature-card p-5 text-center">
                        <div class="icon-box"><i class="bi bi-mortarboard-fill fs-2"></i></div>
                        <h4 class="fw-bold">Expert Faculty</h4>
                        <p class="text-muted mb-0">Learn from top industry leaders and researchers globally.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card p-5 text-center">
                        <div class="icon-box"><i class="bi bi-globe2 fs-2"></i></div>
                        <h4 class="fw-bold">Global Reach</h4>
                        <p class="text-muted mb-0">Join an alumni network spanning over 45 countries.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card p-5 text-center">
                        <div class="icon-box"><i class="bi bi-lightning-charge-fill fs-2"></i></div>
                        <h4 class="fw-bold">Fast-Track Learning</h4>
                        <p class="text-muted mb-0">Modern curriculums designed for immediate industry entry.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Get in Touch</h2>
                <p class="text-muted">Have questions? We'd love to hear from you.</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-6">
                    <form action="backend/contact_submit.php" method="POST" class="p-4 shadow rounded-4 bg-white">
                        <div class="mb-3">
                            <label for="name" class="form-label fw-medium">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Your Name"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label fw-medium">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="you@example.com" required>
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label fw-medium">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" placeholder="Subject"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label fw-medium">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5"
                                placeholder="Write your message..." required></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-emerald fw-bold py-3 rounded-pill">Send
                                Message</button>
                        </div>
                    </form>
                </div>
                <div class="col-lg-6 d-flex flex-column">
                    <div class="shadow rounded-4 overflow-hidden mb-3" style="min-height:450px;">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3819.123456789!2d96.13000!3d16.82xxxx!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30c1xxxxxxxxxxxx%3A0xyyyyyyyyyyyyy!2s575B%20Pyay%20Rd%2C%20Yangon%2C%20Myanmar!5e0!3m2!1sen!2smm!4vXXXXXXXXXXXXXX"
                            width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-dark text-white py-5 mt-5">
        <div class="container text-center">
            <div class="mb-4">
                <a class="navbar-brand fw-bold fs-3" href="index.php">
                    <span style="color: var(--primary-green);">SOFT</span><span class="text-white">EDU</span>
                </a>
            </div>
            <p class="text-secondary small mb-0">
                &copy; <?= date('Y') ?> SoftEdu International Academy. All Rights Reserved.
            </p>
        </div>
    </footer>

    <?php include 'modals/login_modal.php'; ?>
    <?php include 'modals/apply_modal.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>

</html>