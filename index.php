<?php
session_start();


// Admins or guests can see landing page as normal
?>
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
        #contactAlert {
            transition: opacity 0.5s ease;
        }

        .opacity-0 {
            opacity: 0;
        }

        /* Navbar link hover */
        .navbar-nav .nav-link {
            transition: color 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            color: var(--primary-green) !important;
            /* Change to your desired hover color */
        }

        .feature-card,
        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 20px;
        }

        .feature-card:hover,
        #learning-path .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .step-number {
            box-shadow: 0 4px 15px rgba(25, 135, 84, 0.3);
        }
    </style>

</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <section id="home" class="hero-gradient text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <!-- Left Column -->
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
                    <?php
                    // Default links for guests
                    $startJourneyLink = '#applyModal';
                    $exploreCoursesLink = '#loginModal';
                    $exploreCoursesModal = true;

                    // Adjust links if user is logged in
                    if (isset($_SESSION['user_id'])) {
                        $userRole = strtolower(trim($_SESSION['user_role']));
                        if ($userRole === 'student') {
                            $startJourneyLink = 'dashboard.php?page=courses';
                            $exploreCoursesLink = 'dashboard.php?page=courses';
                            $exploreCoursesModal = false; // normal link, no modal
                        }
                    }
                    ?>

                    <div
                        class="d-flex flex-column flex-sm-row justify-content-center justify-content-lg-start gap-3 mb-5">
                        <!-- Start Journey Button -->
                        <a href="<?= $startJourneyLink ?>"
                            class="btn btn-light btn-lg px-5 py-3 fw-bold rounded-pill text-success shadow"
                            <?= $startJourneyLink === '#applyModal' ? 'data-bs-toggle="modal" data-bs-target="#applyModal"' : '' ?>>
                            Start Journey
                        </a>

                        <!-- Explore Courses Button -->
                        <a href="<?= $exploreCoursesLink ?>"
                            class="btn btn-outline-light btn-lg px-5 py-3 fw-bold rounded-pill" <?= $exploreCoursesModal ? 'data-bs-toggle="modal" data-bs-target="#loginModal"' : '' ?>>
                            Explore Courses
                        </a>
                    </div>


                </div>

                <!-- Right Column -->
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


    </section>
    <!-- Testimonials Carousel -->
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="text-center fw-bold mb-4">What Our Students Say</h3>
            <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner text-center">
                    <div class="carousel-item active">
                        <p class="fs-5 fst-italic">
                            "SoftEdu has transformed my career. The faculty are amazing and the support is
                            unmatched!"
                        </p>
                        <strong>— Aung Min, Alumni</strong>
                    </div>
                    <div class="carousel-item">
                        <p class="fs-5 fst-italic">
                            "The global exposure I got here helped me land my dream job right after graduation."
                        </p>
                        <strong>— Thida Win, Alumni</strong>
                    </div>
                    <div class="carousel-item">
                        <p class="fs-5 fst-italic">
                            "Joining SoftEdu was the best decision of my life. Highly recommend to anyone aiming
                            high!"
                        </p>
                        <strong>— Ko Ko, Student</strong>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </div>




    <section id="learning-path" class="py-5 bg-white">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold" style="font-family: 'Playfair Display', serif;">Your Journey to Success
                </h2>
                <p class="text-muted mx-auto" style="max-width: 600px;">Follow our structured path designed to take you
                    from a beginner to an industry-ready professional.</p>
            </div>

            <div class="row g-4 justify-content-center position-relative">
                <div class="col-md-4">
                    <div class="card border-0 text-center h-100 p-4">
                        <div class="step-number mb-4 mx-auto d-flex align-items-center justify-content-center bg-success text-white rounded-circle"
                            style="width: 60px; height: 60px; font-size: 1.5rem; font-weight: bold;">1</div>
                        <div class="icon-box mb-3 mx-auto text-success">
                            <i class="bi bi-person-plus-fill fs-1"></i>
                        </div>
                        <h4 class="fw-bold">Easy Enrollment</h4>
                        <p class="text-muted">Choose your desired program and complete the simple application process to
                            get started.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 text-center h-100 p-4">
                        <div class="step-number mb-4 mx-auto d-flex align-items-center justify-content-center bg-success text-white rounded-circle"
                            style="width: 60px; height: 60px; font-size: 1.5rem; font-weight: bold;">2</div>
                        <div class="icon-box mb-3 mx-auto text-success">
                            <i class="bi bi-book-half fs-1"></i>
                        </div>
                        <h4 class="fw-bold">Expert-Led Learning</h4>
                        <p class="text-muted">Engage with interactive lessons, live sessions, and practical projects
                            guided by industry mentors.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 text-center h-100 p-4">
                        <div class="step-number mb-4 mx-auto d-flex align-items-center justify-content-center bg-success text-white rounded-circle"
                            style="width: 60px; height: 60px; font-size: 1.5rem; font-weight: bold;">3</div>
                        <div class="icon-box mb-3 mx-auto text-success">
                            <i class="bi bi-mortarboard-fill fs-1"></i>
                        </div>
                        <h4 class="fw-bold">Global Certification</h4>
                        <p class="text-muted">Receive your accredited certificate and gain access to our worldwide
                            alumni and job network.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Get in Touch</h2>
                <p class="text-muted">Have questions? We'd love to hear from you.</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="p-4 shadow rounded-4 bg-white">
                        <!-- Alert inside the card -->
                        <div id="contactAlert" class="alert d-none text-center" role="alert"></div>

                        <form id="contactForm" action="#" method="POST">
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
                                <input type="text" class="form-control" id="subject" name="subject"
                                    placeholder="Subject" required>
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
                </div>

                <div class="col-lg-6 d-flex flex-column">
                    <div class="shadow rounded-4 overflow-hidden flex-grow-1 mb-3" style="min-height:350px;">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3819.123456789!2d96.13000!3d16.82xxxx!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30c1xxxxxxxxxxxx%3A0xyyyyyyyyyyyyy!2s575B%20Pyay%20Rd%2C%20Yangon%2C%20Myanmar!5e0!3m2!1sen!2smm!4vXXXXXXXXXXXXXX"
                            width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                    <div class=" row g-3">
                        <div class="col-md-6">
                            <div
                                class="p-3 bg-white shadow-sm rounded-4 d-flex align-items-center gap-3 border-start border-success border-4">
                                <div class="icon-box bg-success bg-opacity-10 text-success p-2 rounded-3">
                                    <i class="bi bi-envelope-fill fs-4"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Email Us</small>
                                    <a href="mailto:supportedu@gmail.com"
                                        class="text-decoration-none text-dark fw-bold">supportedu@gmail.com</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div
                                class="p-3 bg-white shadow-sm rounded-4 d-flex align-items-center gap-3 border-start border-success border-4">
                                <div class="icon-box bg-success bg-opacity-10 text-success p-2 rounded-3">
                                    <i class="bi bi-telephone-fill fs-4"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Call Us</small>
                                    <a href="tel:09973829157"
                                        class="text-decoration-none text-dark fw-bold">09973829157</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <?php include 'modals/login_modal.php'; ?>
    <?php include 'modals/apply_modal.php'; ?>

    <?php include 'includes/scripts.php'; ?>
    <script src="assets/js/main.js"></script>
    <script>
        const contactForm = document.getElementById('contactForm');
        const alertBox = document.getElementById('contactAlert');

        contactForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const submitBtn = contactForm.querySelector('button[type="submit"]');
            const formData = new FormData(contactForm);

            // Disable button and show sending
            submitBtn.disabled = true;
            submitBtn.textContent = 'Sending...';

            // Hide alert completely before fetch
            alertBox.textContent = '';
            alertBox.className = 'alert d-none text-center';
            alertBox.style.opacity = '0';

            try {
                const response = await fetch('backend/guest/contact_submit.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                // Show alert with proper styling AFTER response
                alertBox.textContent = data.message;

                if (data.success) {
                    // GREEN success alert
                    alertBox.className = 'alert alert-success text-center';
                } else {
                    // RED danger alert
                    alertBox.className = 'alert alert-danger text-center';
                }

                alertBox.style.opacity = '1';

                if (data.success) {
                    contactForm.reset();
                }

            } catch (err) {
                // RED error alert
                alertBox.textContent = 'An error occurred. Please try again.';
                alertBox.className = 'alert alert-danger text-center';
                alertBox.style.opacity = '1';
            } finally {
                // Always reset button
                submitBtn.disabled = false;
                submitBtn.textContent = 'Send Message';

                // Auto-hide after 4 seconds
                setTimeout(() => {
                    alertBox.style.opacity = '0';
                    setTimeout(() => {
                        alertBox.className = 'alert d-none text-center';
                    }, 1000);
                }, 4000);
            }
        });
    </script>


</body>

</html>