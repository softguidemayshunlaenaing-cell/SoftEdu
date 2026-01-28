<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once './backend/config/db.php';
$database = new Database();
$db = $database->getConnection();

// Fetch user info
$stmt = $db->prepare("SELECT * FROM softedu_users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user)
    die('User not found.');

$needPassword = (int) $user['force_password_change'] === 1;
$needDocuments = (int) $user['force_document_upload'] === 1;

if (!$needPassword && !$needDocuments) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Setup | SoftEdu</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-green: #059669;
            --primary-light: #f0fdf4;
            --bg-main: #f8fafc;
            --surface: #ffffff;
            --border-color: #e2e8f0;
        }

        body {
            background-color: var(--bg-main);
            color: #334155;
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .onboarding-card {
            background: var(--surface);
            max-width: 600px;
            width: 100%;
            border-radius: 24px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
            overflow: hidden;
        }

        .header-banner {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }

        .header-banner h2 {
            font-weight: 700;
            margin-bottom: 8px;
        }

        .header-banner p {
            opacity: 0.9;
            font-size: 0.95rem;
        }

        .form-content {
            padding: 40px;
        }

        .step-title {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            color: var(--primary-green);
            font-weight: 700;
            font-size: 1.1rem;
        }

        .step-number {
            background: var(--primary-green);
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            margin-right: 12px;
        }

        .form-label {
            font-weight: 600;
            color: #475569;
            font-size: 0.85rem;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            background-color: #f9fafb;
            transition: all 0.2s;
        }

        .form-control:focus {
            background-color: #fff;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.1);
        }

        /* Styled File Input */
        input[type="file"]::file-selector-button {
            background: var(--primary-light);
            color: var(--primary-green);
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            margin-right: 15px;
            transition: 0.2s;
        }

        input[type="file"]::file-selector-button:hover {
            background: #dcfce7;
        }

        .btn-submit {
            background-color: var(--primary-green);
            color: white;
            border: none;
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            font-weight: 700;
            margin-top: 20px;
            transition: all 0.2s;
        }

        .btn-submit:hover {
            background-color: #047857;
            transform: translateY(-1px);
        }

        #onboardingMsg {
            border-radius: 12px;
            padding: 12px;
            font-size: 0.9rem;
            text-align: center;
            display: none;
        }

        .msg-visible {
            display: block !important;
            margin-top: 15px;
        }
    </style>
</head>

<body>

    <div class="onboarding-card">
        <div class="header-banner">
            <h2>Welcome, <?= htmlspecialchars($user['name']) ?>!</h2>
            <p>Let's get your account verified and secured.</p>
        </div>

        <div class="form-content">
            <form id="onboardingForm" enctype="multipart/form-data">

                <?php if ($needPassword): ?>
                    <div class="step-title">
                        <div class="step-number">1</div> Change Password
                    </div>

                    <div class="mb-3 position-relative">
                        <label class="form-label">New Password</label>
                        <input type="password" id="new_password" name="new_password" class="form-control" required>
                        <span class="toggle-password" style="position:absolute; right:10px; top:38px; cursor:pointer;">
                            👁️
                        </span>
                    </div>

                    <div class="mb-4 position-relative">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                        <span class="toggle-password" style="position:absolute; right:10px; top:38px; cursor:pointer;">
                            👁️
                        </span>
                    </div>

                    <hr class="my-4" style="opacity: 0.1;">
                <?php endif; ?>

                <?php if ($needDocuments): ?>
                    <div class="step-title">
                        <div class="step-number"><?= $needPassword ? '2' : '1' ?></div> Upload Documents
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">NRC (Front)</label>
                            <input type="file" name="nrc_front" class="form-control" accept="image/*" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NRC (Back)</label>
                            <input type="file" name="nrc_back" class="form-control" accept="image/*" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Household List (Front)</label>
                            <input type="file" name="thangounsayin_front" class="form-control" accept="image/*" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Household List (Back)</label>
                            <input type="file" name="thangounsayin_back" class="form-control" accept="image/*" required>
                        </div>
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn-submit">Complete Setup</button>
            </form>

            <div id="onboardingMsg"></div>
        </div>
    </div>

    <script>
        document.getElementById('onboardingForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const msgDiv = document.getElementById('onboardingMsg');

            msgDiv.className = 'msg-visible alert alert-info';
            msgDiv.innerText = 'Processing your request...';

            try {
                const res = await fetch('./backend/auth/student_onboarding_process.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await res.json();

                if (data.success) {
                    msgDiv.className = 'msg-visible alert alert-success';
                    msgDiv.innerText = 'Success! Redirecting to dashboard...';
                    setTimeout(() => window.location.href = 'dashboard.php', 1500);
                } else {
                    msgDiv.className = 'msg-visible alert alert-danger';
                    msgDiv.innerText = data.message || 'Something went wrong.';
                }
            } catch (err) {
                msgDiv.className = 'msg-visible alert alert-danger';
                msgDiv.innerText = 'Connection error. Please try again.';
            }
        });
        // Toggle password visibility
        document.querySelectorAll('.toggle-password').forEach(function (span) {
            span.addEventListener('click', function () {
                const input = this.previousElementSibling;
                if (input.type === 'password') {
                    input.type = 'text';
                    this.textContent = '🙈'; // change icon
                } else {
                    input.type = 'password';
                    this.textContent = '👁️';
                }
            });
        });
    </script>

</body>

</html>