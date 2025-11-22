<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to logout?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>

                <form id="logoutForm" method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-danger">Logout</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function(){
        // Bind any admin logout link/button
        document.addEventListener('click', function(e){
            const target = e.target.closest('[data-admin-logout], a[href="#logout"]');
            if (target) {
                e.preventDefault();
                // Use Bootstrap modal if available, otherwise fallback
                if (window.jQuery && jQuery.fn.modal) {
                    $('#logoutModal').modal('show');
                } else {
                    // Fallback for non-Bootstrap
                    document.getElementById('logoutModal').style.display = 'block';
                }
            }
        });
    });
</script>

@if(isset($adminProfileUrl))
<script>
    (function() {
        function modifyLogoLink() {
            // AdminLTE uses .brand-link for the logo link in sidebar
            const brandLinks = document.querySelectorAll('.brand-link, a.navbar-brand');
            brandLinks.forEach(function(link) {
                if (link && link.href) {
                    // Modify logo link to point to admin edit page
                    // Check if it's the default dashboard/home link
                    const href = link.getAttribute('href');
                    if (!href || href === '#' || href === '/' || href.includes('dashboard') || href === window.location.origin || !href.includes('/admin/users/')) {
                        link.href = '{{ $adminProfileUrl }}';
                    }
                }
            });
        }
        
        // Run multiple times to catch dynamically loaded elements
        modifyLogoLink();
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', modifyLogoLink);
        }
        setTimeout(modifyLogoLink, 100);
        setTimeout(modifyLogoLink, 500);
        setTimeout(modifyLogoLink, 1000);
    })();
</script>
@endif
