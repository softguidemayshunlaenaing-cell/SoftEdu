<!-- Edit Assignment Modal -->
<div class="modal fade" id="editAssignmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Assignment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editAssignmentForm">
                <input type="hidden" name="id" id="editAssignmentId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Course *</label>
                        <select name="course_id" id="editCourseId" class="form-select" required disabled>
                            <?php
                            $courses = $db->query("SELECT id, title FROM softedu_courses ORDER BY title");
                            while ($c = $courses->fetch(PDO::FETCH_ASSOC)) {
                                echo '<option value="' . $c['id'] . '">' . htmlspecialchars($c['title']) . '</option>';
                            }
                            ?>
                        </select>
                        <div class="form-text">Course cannot be changed after creation.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Title *</label>
                        <input type="text" name="title" id="editTitle" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="editDescription" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Due Date *</label>
                        <input type="datetime-local" name="due_date" id="editDueDate" class="form-control" required>
                    </div>
                    <div id="editAssignmentAlert" class="alert d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Update Assignment</button>
                </div>
            </form>
        </div>
    </div>
</div>