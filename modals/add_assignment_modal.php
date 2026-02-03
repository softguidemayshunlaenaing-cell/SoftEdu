<!-- Add Assignment Modal -->
<div class="modal fade" id="addAssignmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Assignment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addAssignmentForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <!-- Course -->
                    <div class="mb-3">
                        <label class="form-label">Course <span class="text-danger">*</span></label>
                        <select name="course_id" class="form-select" required>
                            <option value="">Select a course</option>
                            <?php
                            $courses = $db->query("SELECT id, title FROM softedu_courses WHERE status = 'active' ORDER BY title");
                            while ($c = $courses->fetch(PDO::FETCH_ASSOC)) {
                                echo '<option value="' . $c['id'] . '">' . htmlspecialchars($c['title']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Title -->
                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required
                            placeholder="e.g., Week 3 Project">
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"
                            placeholder="Assignment instructions and requirements"></textarea>
                    </div>

                    <!-- Due Date (CRITICAL FIX) -->
                    <div class="mb-3">
                        <label class="form-label">Due Date <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="due_date" class="form-control" required>
                        <!-- <small class="text-muted">Students must submit before this date/time</small> -->
                    </div>

                    <!-- Late Submission Settings (Optional but recommended) -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Late Days Allowed</label>
                            <input type="number" name="late_days" class="form-control" min="0" value="3"
                                placeholder="Default: 3">
                            <!-- <small class="text-muted">Days after deadline for late submissions</small> -->
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Penalty per Day (%)</label>
                            <input type="number" name="late_penalty" class="form-control" min="0" max="100" value="5"
                                placeholder="Default: 5%">
                            <!-- <small class="text-muted">Deducted from grade for each late day</small> -->
                        </div>
                    </div>

                    <!-- Assignment File -->
                    <div class="mb-3">
                        <label class="form-label">Assignment File (Optional)</label>
                        <input type="file" name="assignment_file" class="form-control"
                            accept=".pdf,.doc,.docx,.zip,.jpg,.jpeg,.png">
                        <small class="text-muted">Allowed: PDF, DOC, DOCX, ZIP, JPG, PNG (Max 10MB)</small>
                    </div>

                    <!-- Alert Box -->
                    <div id="assignmentAlert" class="alert d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> Save Assignment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>