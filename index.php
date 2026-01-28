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

    <nav class="navbar navbar-expand-lg fixed-top shadow-sm py-3">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3" href="#">
                <span style="color: var(--primary-green);">SOFT</span><span class="text-dark">EDU</span>
            </a>

            <button class="navbar-toggler border-0 shadow-none" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link px-3 fw-medium text-dark" href="#home">Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link px-3 fw-medium text-dark dropdown-toggle" href="#programs"
                            data-bs-toggle="dropdown">Programs</a>
                        <ul class="dropdown-menu border-0 shadow-lg rounded-4 p-2 mt-2">
                            <li><a class="dropdown-item rounded-3 py-2" href="#computer-science">Computer Science</a>
                            </li>
                            <li><a class="dropdown-item rounded-3 py-2" href="#business-management">Business
                                    Management</a></li>
                            <li><a class="dropdown-item rounded-3 py-2" href="#graphic-design">Graphic Design</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link px-3 fw-medium text-dark" href="#about">About</a></li>
                    <li class="nav-item"><a class="nav-link px-3 fw-medium text-dark" href="#contact">Contact</a></li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <a href="#" class="btn btn-link text-decoration-none fw-bold text-dark px-4" data-bs-toggle="modal"
                        data-bs-target="#loginModal">Sign In</a>
                    <a href="#" class="btn btn-emerald px-4 shadow-sm" data-bs-toggle="modal"
                        data-bs-target="#applyModal">Apply Now</a>
                </div>
            </div>
        </div>
    </nav>

    <section class="hero-gradient text-white">
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

    <section class="py-5">
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

    <footer class="bg-dark text-white py-5 mt-5">
        <div class="container text-center">
            <div class="mb-4">
                <a class="navbar-brand fw-bold fs-3" href="#">
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