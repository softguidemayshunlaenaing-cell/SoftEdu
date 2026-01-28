<!-- LOGIN MODAL -->
<div class="modal fade" id="loginModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 p-4">
            <div class="text-center mb-3">
                <h5 class="fw-semibold">Login to SoftEdu</h5>
                <p class="text-muted small mb-0">Access your account</p>
            </div>
            <form id="loginForm">
                <div id="loginAlert" class="alert d-none"></div>
                <div class="mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <button type="submit" class="btn btn-warning w-100 py-2">Login</button>
            </form>
            <div class="text-center mt-3">
                <small class="text-muted">Applied but can’t login? Wait for admin approval.</small>
            </div>
        </div>
    </div>
</div>