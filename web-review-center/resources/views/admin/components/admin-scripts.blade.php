@if(isset($adminProfileUrl))
<script>
    (function() {
        function modifyLogoLink() {
            // AdminLTE uses .brand-link for the logo link in sidebar
            const brandLinks = document.querySelectorAll('.brand-link, a.navbar-brand');
            brandLinks.forEach(function(link) {
                if (link && link.href) {
                    // Only modify if it's pointing to dashboard or home
                    if (link.href.includes('dashboard') || link.href === window.location.origin + '/' || link.href.endsWith('#') || !link.href.includes('/admin/users/')) {
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

{{-- Ensure CSRF token meta tag exists --}}
@if(!isset($csrfToken))
    @php
        $csrfToken = csrf_token();
    @endphp
@endif
<script>
    // Ensure CSRF token meta tag exists in head
    (function() {
        if (!document.querySelector('meta[name="csrf-token"]')) {
            const meta = document.createElement('meta');
            meta.name = 'csrf-token';
            meta.content = '{{ $csrfToken }}';
            document.head.appendChild(meta);
        } else {
            // Update existing token
            const meta = document.querySelector('meta[name="csrf-token"]');
            if (meta) {
                meta.content = '{{ $csrfToken }}';
            }
        }
    })();
</script>

