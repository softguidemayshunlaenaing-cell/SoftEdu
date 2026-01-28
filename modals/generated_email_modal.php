<!-- Generated Email Modal -->
<div class="modal fade" id="generatedEmailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Generated Email</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Admin can edit the email before creating the student account:</p>
                <input type="email" id="generatedEmailInput" class="form-control" />
                <div id="emailAlert" class="alert alert-danger mt-2 d-none"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmApproveBtn">Approve</button>
            </div>
        </div>
    </div>
</div>