<!-- ADD MATERIAL MODAL (global) -->
<div class="modal fade" id="addMaterialModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Course Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addMaterialForm">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="addMaterialCourse" class="form-label">Course *</label>
                            <select name="course_id" id="addMaterialCourse" class="form-select" required>
                                <option value="">Select a course</option>
                                <?php
                                $courses = $db->query("SELECT id, title FROM softedu_courses WHERE status = 'active' ORDER BY title");
                                while ($c = $courses->fetch(PDO::FETCH_ASSOC)) {
                                    echo '<option value="' . $c['id'] . '">' . htmlspecialchars($c['title']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="addMaterialTitle" class="form-label">Title *</label>
                            <input type="text" name="title" id="addMaterialTitle" class="form-control"
                                placeholder="e.g., Week 1 Lecture" required>
                        </div>
                        <div class="col-md-4">
                            <label for="addMaterialType" class="form-label">Type *</label>
                            <select name="material_type" id="addMaterialType" class="form-select" required>
                                <option value="video">Video</option>
                                <option value="pdf">PDF</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="addMaterialSource" class="form-label">Source *</label>
                            <select name="source" id="addMaterialSource" class="form-select" required>
                                <option value="youtube">YouTube</option>
                                <option value="google_drive">Google Drive</option>
                                <option value="external">External Link</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="addMaterialRole" class="form-label">Role Access</label>
                            <select name="role_access" id="addMaterialRole" class="form-select">
                                <option value="all">All Roles</option>
                                <option value="student">Students Only</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="addMaterialUrl" class="form-label">URL *</label>
                            <input type="url" name="material_url" id="addMaterialUrl" class="form-control"
                                placeholder="https://..." required>
                            <div class="form-text">
                                For Google Drive: use "Shareable link" → change to <code>preview</code> or
                                <code>uc?export=download</code>
                            </div>
                        </div>
                    </div>
                    <div id="addMaterialAlert" class="alert d-none mt-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-softedu">Add Material</button>
                </div>
            </form>
        </div>
    </div>
</div>