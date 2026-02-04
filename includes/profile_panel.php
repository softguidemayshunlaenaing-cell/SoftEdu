<?php
$profileBtnClass = $profileBtnClass ?? 'btn btn-success';
$profileImg = !empty($user['profile_image'])
    ? 'uploads/profiles/' . htmlspecialchars($user['profile_image'])
    : 'https://ui-avatars.com/api/?name=' . urlencode($user['name'] ?? 'U') . '&background=059669&color=fff&size=128';
?>
<div class="card">
    <div class="card-body">
        <div class="text-center mb-4">
            <img src="<?= $profileImg ?>" id="profilePreview" class="rounded-circle mb-3" width="120" height="120"
                style="object-fit: cover; border: 3px solid #e2e8f0;">
            <h4 class="mb-1"><?= htmlspecialchars($user['name'] ?? '') ?></h4>
            <span class="badge bg-success"><?= ucfirst($_SESSION['user_role'] ?? '') ?></span>
        </div>
        <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#accountTab">Account</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#securityTab">Security</button>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="accountTab">
                <form id="nameForm">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Full Name</label>
                        <input type="text" class="form-control" name="name"
                            value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Email Address (Read Only)</label>
                        <input type="email" class="form-control bg-light"
                            value="<?= htmlspecialchars($_SESSION['user_email'] ?? '') ?>" disabled>
                    </div>
                    <button type="submit" class="<?= htmlspecialchars($profileBtnClass) ?>">Update Name</button>
                    <div id="nameAlert" class="alert d-none mt-3"></div>
                </form>
            </div>
            <div class="tab-pane fade" id="securityTab">
                <form id="passwordForm">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Current Password</label>
                        <input type="password" class="form-control" name="current_password" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">New Password</label>
                        <input type="password" class="form-control" name="new_password" minlength="8" required>
                        </div>
                        <div class="col-md-6 mb-3">
                        <label class="form-label fw-medium">Confirm New Password</label>
                        <input type="password" class="form-control" name="confirm_password" minlength="8" required>
                        </div>
                    </div>
                    <button type="submit" class="<?= htmlspecialchars($profileBtnClass) ?>">Change Password</button>
                    <div id="passwordAlert" class="alert d-none mt-3"></div>
                </form>
            </div>
        </div>
    </div>
</div>
