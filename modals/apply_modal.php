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
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="form-control" required inputmode="numeric"
                                pattern="^(09\d{7,9}|\+959\d{7,9})$"
                                title="Enter valid Myanmar phone number (09xxxxxxxxx or +959xxxxxxxxx)">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Township</label>
                            <input type="text" name="township" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Why join SoftEdu? <span class="text-danger">*</span></label>
                            <textarea name="notes" class="form-control" rows="3" required></textarea>
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