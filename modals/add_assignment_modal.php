<!-- Add Assignment Modal -->
<div class="modal fade" id="addAssignmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Assignment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addAssignmentForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Course *</label>
                        <select name="course_id" class="form-select" required>
                            <?php
                            $courses = $db->query("SELECT id, title FROM softedu_courses ORDER BY title");
                            while ($c = $courses->fetch(PDO::FETCH_ASSOC)) {
                                echo '<option value="' . $c['id'] . '">' . htmlspecialchars($c['title']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Title *</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Due Date *</label>
                        <input type="datetime-local" name="due_date" class="form-control" required>
                    </div>
                    <div id="assignmentAlert" class="alert d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-softedu">Save Assignment</button>
                </div>
            </form>
        </div>
    </div>
</div>