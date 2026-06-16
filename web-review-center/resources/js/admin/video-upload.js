(function () {
    const form = document.getElementById('uploadForm');
    const btn = document.getElementById('btn-upload');
    const modal = document.getElementById('uploadModal');

    let uploadToken = null;

    function hasClientErrors() {
        let hasError = false;
        const title = form.querySelector('input[name="title"]').value.trim();
        const video = form.querySelector('input[name="video"]').files[0];
        const subject = form.querySelector('select[name="subject_id"]').value;
        const link = form.querySelector('input[name="google_form_link"]').value.trim();
        if (!title) { document.getElementById('error-title').textContent = 'The title field is required.'; hasError = true; }
        if (!video) { document.getElementById('error-video').textContent = 'The video field is required.'; hasError = true; }
        if (!subject) { document.getElementById('error-subject_id').textContent = 'The subject field is required.'; hasError = true; }
        if (!link) { document.getElementById('error-google_form_link').textContent = 'The google form link field is required.'; hasError = true; }
        return hasError;
    }


form.addEventListener('submit', async function (e) {
    e.preventDefault();

    if (hasClientErrors()) return;

    const formData = new FormData(form);

    uploadToken = Date.now() + '-' + Math.random().toString(36).substring(2);
    formData.append('upload_token', uploadToken);

    btn.disabled = true;
    modal.classList.add('show');

    try {
        const res = await fetch('/admin/videos/upload', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });

        const data = await res.json();
        if (res.ok) {

            setTimeout(() => {
                window.location.href = data.redirect ?? '/admin/videos';
            }, 800);

        } else {
            throw new Error('Upload failed');
        }

    } catch (err) {
        console.error(err);
        modal.classList.remove('show');
    }

    btn.disabled = false;
});

})();