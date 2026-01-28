<!-- COURSE MODAL -->
<div class="modal fade" id="courseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="courseModalLabel">Add New Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="courseForm" enctype="multipart/form-data">
                <input type="hidden" name="id" id="courseId">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="courseTitle" class="form-label">Course Title *</label>
                                <input type="text" class="form-control" id="courseTitle" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label for="courseDescription" class="form-label">Description</label>
                                <textarea class="form-control" id="courseDescription" name="description"
                                    rows="3"></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="courseDuration" class="form-label">Duration (e.g., "3
                                            Years")</label>
                                        <input type="text" class="form-control" id="courseDuration" name="duration">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="courseFee" class="form-label">Fee (USD)</label>
                                        <input type="number" step="0.01" class="form-control" id="courseFee" name="fee">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="courseStatus" class="form-label">Status</label>
                                <select class="form-select" id="courseStatus" name="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="mb-3">
                                <label for="courseImage" class="form-label">Course Image</label>
                                <input type="file" class="form-control" id="courseImage" name="image" accept="image/*">
                                <div class="form-text">Max 2MB. JPG/PNG.</div>
                                <img id="courseImagePreview" src="" class="mt-2" style="max-width:100%; display:none;">
                            </div>
                        </div>
                    </div>
                    <div id="courseAlert" class="alert d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-softedu">Save Course</button>
                </div>
            </form>
        </div>
    </div>
</div>