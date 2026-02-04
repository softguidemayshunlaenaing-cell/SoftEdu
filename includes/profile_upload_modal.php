<?php
$uploadBtnClass = $uploadBtnClass ?? 'btn btn-success';
?>
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Profile Photo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="uploadForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Choose Image</label>
                        <input type="file" class="form-control" name="profile_image" accept="image/*" required>
                    </div>
                    <div id="uploadAlert" class="alert d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="<?= htmlspecialchars($uploadBtnClass) ?>">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
