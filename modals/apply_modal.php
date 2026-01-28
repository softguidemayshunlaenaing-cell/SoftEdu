<!-- APPLY MODAL -->
<div class="modal fade" id="applyModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header px-4">
                <h5 class="modal-title fw-semibold">Apply to SoftEdu</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="applyForm">
                <div class="modal-body px-4">
                    <div id="applyAlert" class="alert d-none"></div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="text" name="name" class="form-control" placeholder="Full Name *" required>
                        </div>
                        <div class="col-md-6">
                            <input type="email" name="email" class="form-control" placeholder="Email *" required>
                        </div>
                        <div class="col-md-6">
                            <input type="tel" name="phone" class="form-control" placeholder="Phone *" required
                                inputmode="numeric" pattern="^(09\d{7,9}|\+959\d{7,9})$"
                                title="Enter valid Myanmar phone number (09xxxxxxxxx or +959xxxxxxxxx)">
                        </div>

                        <div class="col-md-6">
                            <input type="text" name="township" class="form-control" placeholder="Township">
                        </div>
                        <div class="col-12">
                            <textarea name="address" class="form-control" rows="2" placeholder="Address"></textarea>
                        </div>
                        <div class="col-12">
                            <textarea name="notes" class="form-control" rows="3" placeholder="Why join SoftEdu? *"
                                required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-4">
                    <button type="submit" class="btn btn-warning w-100 py-2">Submit Application</button>
                </div>
            </form>
        </div>
    </div>
</div>