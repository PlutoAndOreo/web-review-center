(function () {
    const form = document.getElementById('uploadForm');
    const labelEl = document.getElementById('progressLabel');
    const btn = document.getElementById('btn-upload');
    const modal = document.getElementById('uploadModal');
    let pollTimer = null;
    let uploadToken = null;
    let processStartMs = 0;
    let lastProcessPercent = 0;
    let lastProcessTs = 0;

    function setProgress(percent, label) { if (label) labelEl.textContent = label; }

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

    form.addEventListener('submit', function (e) {
    e.preventDefault();
    
        const formData = new FormData(form);
        const xhr = new XMLHttpRequest();
        const startTime = Date.now();

        btn.disabled = true;

        // clear previous ajax errors
        ['title','description','video','subject_id','google_form_link'].forEach(function(name){
            const el = document.getElementById('error-' + name);
            if (el) el.textContent = '';
        });

        // Do not show modal if there are client-side errors
        if (hasClientErrors()) {
            btn.disabled = false;
            return;
        }

        // show and initialize minimal loading UI only when there are no errors
        modal.classList.add('show');
        setProgress(0, 'Starting upload...');
        // set token to correlate server progress
        uploadToken = (Math.random().toString(36).slice(2)) + Date.now().toString(36);
        formData.append('upload_token', uploadToken);

        // progress bar removed; keep label-only updates
        xhr.upload.onprogress = function () { setProgress(0, 'Uploading...'); };

            xhr.onreadystatechange = function () {
            if (xhr.readyState === 2) {
                    // begin polling server-side processing progress
                    labelEl.textContent = 'Processing on server...';
                    processStartMs = Date.now();
                    lastProcessPercent = 0;
                    lastProcessTs = processStartMs;
                    if (uploadToken && !pollTimer) {
                        pollTimer = setInterval(async function() {
                            try {
                                const res = await fetch(`${progressUrlBase}/${uploadToken}`, {
                                    headers: { 'Accept': 'application/json' }
                                });
                                const json = await res.json();
                                if (typeof json.percent === 'number') {
                                    setProgress(json.percent, 'Processing on server...');
                                    const now = Date.now();
                                    const elapsedProcSec = (now - processStartMs) / 1000;
                                    let rate = 0; // percent per second
                                    if (json.percent > 0 && elapsedProcSec > 0) {
                                        rate = json.percent / elapsedProcSec;
                                    }
                                    lastProcessPercent = json.percent;
                                    lastProcessTs = now;
                                }
                                if (json.percent >= 100) {
                                    labelEl.textContent = 'Finalizing...';
                                    clearInterval(pollTimer); pollTimer = null;
                                }
                            } catch (_) {}
                        }, 800);
                    }
            }
            if (xhr.readyState === 4) {
                btn.disabled = false;
                    const isJSON = (xhr.getResponseHeader('Content-Type') || '').includes('application/json');
                    let data = null;
                    if (isJSON) {
                        try { data = JSON.parse(xhr.responseText); } catch (_) {}
                    }

                    if (xhr.status >= 200 && xhr.status < 300) {
                        labelEl.textContent = 'Completed';
                        if (data && data.redirect) {
                                    modal.classList.remove('show');
                                    window.location.href = data.redirect;
                        } else if (data && data.message) {
                                    modal.classList.remove('show');
                                    alert(data.message);
                        } else {
                                    modal.classList.remove('show');
                                    window.location.reload();
                        }
                } else if (xhr.status === 422 && data) {
                        // Laravel validation errors
                        labelEl.textContent = 'Validation error';
                        if (data.errors) {
                            Object.keys(data.errors).forEach(function (field) {
                                const el = document.getElementById('error-' + field);
                                if (el) el.textContent = (data.errors[field] || []).join(' ');
                            });
                        }
                    // hide modal since submission has validation errors
                    modal.classList.remove('show');
                        if (pollTimer) { clearInterval(pollTimer); pollTimer = null; }
        } else {
                        labelEl.textContent = 'Failed';
                    }
        }
    };

        xhr.open('POST', uploadUrl, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.send(formData);
});
})();