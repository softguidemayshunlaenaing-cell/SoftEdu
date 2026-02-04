(() => {
    // Helper: POST JSON and parse response
    function postJson(url, data) {
        return fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        }).then(res => res.json());
    }

    // Helper: show bootstrap alert
    function showAlert(el, type, text) {
        if (!el) return;
        el.className = `alert alert-${type}`;
        el.textContent = text;
        el.classList.remove('d-none');
    }

    // Helper: attach delete handlers with confirm + reload
    function attachDeleteButtons(selector, url, confirmText) {
        document.querySelectorAll(selector).forEach(btn => {
            btn.addEventListener('click', async () => {
                if (confirmText && !confirm(confirmText)) return;
                const id = btn.dataset.id;
                try {
                    const result = await postJson(url, { id });
                    if (result.success) location.reload();
                    else alert(result.message);
                } catch (err) {
                    alert('Failed to delete.');
                }
            });
        });
    }

    // Course CRUD (admin/staff dashboards)
    function initCourseCrud() {
        document.querySelector('[data-bs-target="#courseModal"]')?.addEventListener('click', () => {
            document.getElementById('courseForm')?.reset();
            const preview = document.getElementById('courseImagePreview');
            if (preview) preview.style.display = 'none';
            const idField = document.getElementById('courseId');
            if (idField) idField.value = '';
            const label = document.getElementById('courseModalLabel');
            if (label) label.textContent = 'Add New Course';
        });

        document.getElementById('courseForm')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const alertBox = document.getElementById('courseAlert');
            if (alertBox) alertBox.className = 'alert d-none';
            try {
                const url = formData.get('id')
                    ? 'backend/course/edit_course.php'
                    : 'backend/course/add_course.php';
                const res = await fetch(url, { method: 'POST', body: formData });
                const result = await res.json();
                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('courseModal'))?.hide();
                    setTimeout(() => location.reload(), 800);
                } else if (alertBox) {
                    showAlert(alertBox, 'danger', result.message || 'Failed to save course.');
                }
            } catch (err) {
                showAlert(alertBox, 'danger', 'Network error.');
            }
        });

        document.querySelectorAll('.edit-course-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                const id = btn.dataset.id;
                try {
                    const res = await fetch(`backend/course/get_course.php?id=${id}`);
                    const course = await res.json();
                    if (course) {
                        document.getElementById('courseId').value = course.id;
                        document.getElementById('courseTitle').value = course.title;
                        document.getElementById('courseDescription').value = course.description || '';
                        document.getElementById('courseDuration').value = course.duration || '';
                        document.getElementById('courseFee').value = course.fee || '';
                        document.getElementById('courseStatus').value = course.status;
                        const preview = document.getElementById('courseImagePreview');
                        if (preview) {
                            if (course.image) {
                                preview.src = `uploads/courses/${course.image}`;
                                preview.style.display = 'block';
                            } else {
                                preview.style.display = 'none';
                            }
                        }
                        document.getElementById('courseModalLabel').textContent = 'Edit Course';
                        bootstrap.Modal.getOrCreateInstance(document.getElementById('courseModal')).show();
                    }
                } catch (err) {
                    alert('Failed to load course data.');
                }
            });
        });

        document.querySelectorAll('.delete-course-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (!confirm('Delete this course?')) return;
                const id = btn.dataset.id;
                try {
                    const result = await postJson('backend/course/delete_course.php', { id });
                    if (result.success) location.reload();
                    else alert(result.message);
                } catch (err) {
                    alert('Failed to delete course.');
                }
            });
        });
    }

    // Materials CRUD (admin/staff dashboards)
    function initMaterialsCrud() {
        document.querySelectorAll('.materials-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                const courseId = btn.dataset.id;
                const courseIdField = document.getElementById('materialCourseId');
                if (courseIdField) courseIdField.value = courseId;
                try {
                    const res = await fetch(`backend/course/get_materials.php?course_id=${courseId}`);
                    const materials = await res.json();
                    let html = '<h6>Existing Materials:</h6>';
                    if (materials.length > 0) {
                        html += '<ul class="list-group">';
                        materials.forEach(m => {
                            html += `<li class="list-group-item d-flex justify-content-between">${m.title} (${m.source})</li>`;
                        });
                        html += '</ul>';
                    } else {
                        html += '<p class="text-muted">No materials yet.</p>';
                    }
                    const list = document.getElementById('materialsList');
                    if (list) list.innerHTML = html;
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('materialsModal')).show();
                } catch (err) {
                    alert('Failed to load materials.');
                }
            });
        });

        // Quick add from per-course modal
        document.getElementById('quickAddMaterialForm')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const alertBox = document.getElementById('materialAlert');
            if (alertBox) alertBox.className = 'alert d-none';
            const courseId = formData.get('course_id') || document.getElementById('materialCourseId')?.value;
            if (!courseId) {
                showAlert(alertBox, 'danger', 'Please select a course first.');
                return;
            }
            try {
                const res = await fetch('backend/course/add_material.php', { method: 'POST', body: formData });
                const result = await res.json();
                if (result.success) {
                    showAlert(alertBox, 'success', result.message || 'Material added successfully.');
                    const onSuccess = e.target.dataset.onSuccess || 'reload';
                    if (onSuccess === 'refreshModal') {
                        setTimeout(() => {
                            bootstrap.Modal.getInstance(document.getElementById('materialsModal'))?.hide();
                            document.querySelector(`.materials-btn[data-id="${courseId}"]`)?.click();
                        }, 800);
                    } else {
                        setTimeout(() => location.reload(), 800);
                    }
                } else {
                    showAlert(alertBox, 'danger', result.message || 'Failed to add material.');
                }
            } catch (err) {
                showAlert(alertBox, 'danger', 'Network error.');
            }
        });

        // Add material from global modal
        document.getElementById('addMaterialForm')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const alertBox = document.getElementById('addMaterialAlert');
            if (alertBox) alertBox.className = 'alert d-none';
            try {
                const res = await fetch('backend/course/add_material.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();
                if (result.success) {
                    showAlert(alertBox, 'success', result.message || 'Material added successfully.');
                    const redirect = e.target.dataset.successRedirect;
                    setTimeout(() => {
                        bootstrap.Modal.getInstance(document.getElementById('addMaterialModal'))?.hide();
                        if (redirect) {
                            window.location.href = redirect;
                        } else {
                            location.reload();
                        }
                    }, 800);
                } else {
                    showAlert(alertBox, 'danger', result.message || 'Failed to add material.');
                }
            } catch (err) {
                showAlert(alertBox, 'danger', 'Network error.');
            }
        });

        // Delete material
        document.querySelectorAll('.delete-material-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (!confirm('Delete this material?')) return;
                const id = btn.dataset.id;
                try {
                    await postJson('backend/course/delete_material.php', { id });
                    btn.closest('tr')?.remove();
                    const modal = bootstrap.Modal.getInstance(document.getElementById('materialsModal'));
                    if (modal && document.querySelector('#materialsModal.show')) {
                        const courseId = document.getElementById('materialCourseId')?.value;
                        if (courseId) {
                            document.querySelector(`.materials-btn[data-id="${courseId}"]`)?.click();
                        }
                    }
                } catch (err) {
                    alert('Failed to delete material.');
                }
            });
        });
    }

    // Profile update handlers (admin/staff/student dashboards)
    function initProfileHandlers() {
        const profilePreview = document.getElementById('profilePreview');
        if (profilePreview) {
            profilePreview.addEventListener('click', () => {
                const modal = new bootstrap.Modal(document.getElementById('uploadModal'));
                modal.show();
            });
        }

        document.getElementById('nameForm')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const alertBox = document.getElementById('nameAlert');
            if (alertBox) alertBox.className = 'alert d-none';
            try {
                const res = await fetch('backend/user/profile_update.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();
                if (result.success) {
                    showAlert(alertBox, 'success', result.message);
                    const sessionName = document.querySelector('[data-session-name]');
                    if (sessionName) sessionName.textContent = formData.get('name');
                } else {
                    showAlert(alertBox, 'danger', result.message);
                }
            } catch (err) {
                showAlert(alertBox, 'danger', 'Network error.');
            }
        });

        document.getElementById('passwordForm')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const alertBox = document.getElementById('passwordAlert');
            if (alertBox) alertBox.className = 'alert d-none';
            if (formData.get('new_password') !== formData.get('confirm_password')) {
                showAlert(alertBox, 'danger', 'Passwords do not match.');
                return;
            }
            try {
                const res = await fetch('backend/user/password_change.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();
                if (result.success) {
                    showAlert(alertBox, 'success', result.message);
                    e.target.reset();
                } else {
                    showAlert(alertBox, 'danger', result.message);
                }
            } catch (err) {
                showAlert(alertBox, 'danger', 'Network error.');
            }
        });

        document.getElementById('uploadForm')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const alertBox = document.getElementById('uploadAlert');
            if (alertBox) alertBox.className = 'alert d-none';
            try {
                const res = await fetch('backend/user/profile_image_upload.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();
                if (result.success) {
                    showAlert(alertBox, 'success', result.message);
                    const preview = document.getElementById('profilePreview');
                    if (preview) {
                        preview.src = 'uploads/profiles/' + result.filename + '?t=' + Date.now();
                    }
                    setTimeout(() => {
                        bootstrap.Modal.getInstance(document.getElementById('uploadModal'))?.hide();
                        e.target.reset();
                    }, 1500);
                } else {
                    showAlert(alertBox, 'danger', result.message);
                }
            } catch (err) {
                showAlert(alertBox, 'danger', 'Network error.');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Assignment delete handlers
        attachDeleteButtons(
            '.delete-assignment-btn',
            'backend/admin/delete_assignment.php',
            'Delete this assignment and all submissions?'
        );
        attachDeleteButtons(
            '.delete-submission-btn',
            'backend/admin/delete_submission.php',
            'Delete this submission?'
        );
        // Course/materials CRUD
        initCourseCrud();
        initMaterialsCrud();
        // Profile updates
        initProfileHandlers();
    });
})();
