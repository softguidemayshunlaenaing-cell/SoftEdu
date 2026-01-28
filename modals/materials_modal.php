<!-- MATERIALS MODAL (per-course) -->
<div class="modal fade" id="materialsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Course Materials</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="materialsList"></div>
                <hr>
                <form id="quickAddMaterialForm">
                    <input type="hidden" name="course_id" id="materialCourseId">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label for="materialTitle" class="form-label">Title *</label>
                            <input type="text" name="title" id="materialTitle" class="form-control"
                                placeholder="e.g., Week 1 Lecture" required>
                        </div>
                        <div class="col-md-3">
                            <label for="materialType" class="form-label">Type *</label>
                            <select name="material_type" id="materialType" class="form-select" required>
                                <option value="video">Video</option>
                                <option value="pdf">PDF</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="materialSource" class="form-label">Source *</label>
                            <select name="source" id="materialSource" class="form-select" required>
                                <option value="youtube">YouTube</option>
                                <option value="google_drive">Google Drive</option>
                                <option value="external">External Link</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-softedu w-100">Add</button>
                        </div>
                    </div>
                    <div class="mt-2">
                        <label for="materialUrl" class="form-label">URL *</label>
                        <input type="url" name="material_url" id="materialUrl" class="form-control"
                            placeholder="https://..." required>
                    </div>
                </form>
                <div id="materialAlert" class="alert d-none mt-2"></div>
            </div>
        </div>
    </div>
</div>