(function () {
    const modal = document.getElementById('deleteModal');
    const cancelBtn = document.getElementById('btn-cancel-delete');
    const confirmBtn = document.getElementById('btn-confirm-delete');
    const targetForm = document.getElementById('delete-target-form');
    let currentForm = null;

    window.openModal = function(form){
        currentForm = form;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    window.closeModal = function(){
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        currentForm = null;
    }

    document.querySelectorAll('.btn-open-delete').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const form = document.getElementById('delete-form-' + id);
            openModal(form);
        });
    });

    cancelBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });

    confirmBtn.addEventListener('click', function () {
        if (!currentForm) return;
        currentForm.submit();
        closeModal();
    });
})();

// Video Modal Functions
window.openVideoModal = function(videoId, title) {
    const videoModal = document.getElementById('videoModal');
    const videoPlayer = document.getElementById('adminVideoPlayer');
    const modalTitle = document.getElementById('videoModalTitle');

    modalTitle.textContent = title;
    videoPlayer.src = `/admin/videos/${videoId}/stream`;
    videoModal.classList.remove('hidden');
    videoModal.classList.add('flex');
}

window.closeVideoModal = function() {
    const videoModal = document.getElementById('videoModal');
    const videoPlayer = document.getElementById('adminVideoPlayer');

    videoPlayer.pause();
    videoPlayer.src = '';
    videoModal.classList.add('hidden');
    videoModal.classList.remove('flex');
}

document.getElementById('closeVideoModal').addEventListener('click', closeVideoModal);
document.getElementById('videoModal').addEventListener('click', function (e) {
    if (e.target === this) closeVideoModal();
});