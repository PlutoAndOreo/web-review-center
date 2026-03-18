/**
 * Global CSRF Token Handler
 * Handles CSRF token refresh and 419 error recovery
 */

(function() {
    'use strict';

    // Get CSRF token from meta tag
    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : null;
    }

    // Update CSRF token in meta tag
    function updateCsrfToken(token) {
        const meta = document.querySelector('meta[name="csrf-token"]');
        if (meta && token) {
            meta.setAttribute('content', token);
        }
    }

    // Refresh CSRF token
    async function refreshCsrfToken() {
        try {
            const response = await fetch('/api/csrf-token', {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                const data = await response.json();
                if (data.token) {
                    updateCsrfToken(data.token);
                    
                    // Update axios if available
                    if (window.axios) {
                        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = data.token;
                    }
                    
                    return data.token;
                }
            }
        } catch (error) {
            console.error('Failed to refresh CSRF token:', error);
        }
        
        return null;
    }

    // Handle 419 errors in fetch requests
    const originalFetch = window.fetch;
    window.fetch = async function(...args) {
        let response = await originalFetch(...args);
        
        // Clone response for reading status without consuming body
        if (response.status === 419) {
            // CSRF token expired, try to refresh
            const newToken = await refreshCsrfToken();
            
            if (newToken && args[1] && args[1].method && args[1].method !== 'GET') {
                // Retry the request with new token
                const config = { ...args[1] };
                config.headers = config.headers || {};
                config.headers['X-CSRF-TOKEN'] = newToken;
                
                // Clone the original request if it has a body
                if (args[1] && args[1].body) {
                    // For FormData, we need to recreate it
                    if (args[1].body instanceof FormData) {
                        config.body = args[1].body;
                    } else {
                        config.body = args[1].body;
                    }
                }
                
                const retryResponse = await originalFetch(args[0], config);
                if (retryResponse.ok || retryResponse.status !== 419) {
                    return retryResponse;
                }
            }
            
            // If refresh failed or retry failed, show user-friendly message
            if (confirm('Your session has expired. Click OK to refresh the page and continue.')) {
                window.location.reload();
            }
        }
        
        return response;
    };

    // Ensure CSRF token is included in all XMLHttpRequest POST/PUT/DELETE requests
    const originalXHRSend = XMLHttpRequest.prototype.send;
    XMLHttpRequest.prototype.send = function(data) {
        // Ensure CSRF token is included for non-GET requests
        const method = this._method || (this._url ? 'GET' : 'POST');
        if (method !== 'GET' && method !== 'HEAD') {
            const token = getCsrfToken();
            if (token) {
                try {
                    this.setRequestHeader('X-CSRF-TOKEN', token);
                } catch (e) {
                    // Header might already be set
                }
            }
        }
        return originalXHRSend.apply(this, [data]);
    };
    
    // Store method and url when open is called
    const originalXHROpen = XMLHttpRequest.prototype.open;
    XMLHttpRequest.prototype.open = function(method, url, ...rest) {
        this._method = method.toUpperCase();
        this._url = url;
        return originalXHROpen.apply(this, [method, url, ...rest]);
    };

    // Periodically refresh CSRF token (every 15 minutes to prevent expiration)
    setInterval(async () => {
        await refreshCsrfToken();
    }, 15 * 60 * 1000);
    
    // Refresh token on page visibility change (when user returns to tab)
    document.addEventListener('visibilitychange', async () => {
        if (!document.hidden) {
            await refreshCsrfToken();
        }
    });
    
    // Handle form submissions - refresh token before submit and handle 419 errors
    document.addEventListener('submit', async function(e) {
        const form = e.target;
        if (form && form.tagName === 'FORM' && form.method.toUpperCase() !== 'GET') {
            // Refresh token before form submission to prevent expiration
            const newToken = await refreshCsrfToken();
            
            // Update form's hidden CSRF input if it exists
            if (newToken) {
                const csrfInput = form.querySelector('input[name="_token"]');
                if (csrfInput) {
                    csrfInput.value = newToken;
                }
            }
        }
    }, true); // Use capture phase to run before other handlers
    
    // Expose refresh function globally
    window.refreshCsrfToken = refreshCsrfToken;
    window.getCsrfToken = getCsrfToken;
    
    // Initialize token refresh on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', refreshCsrfToken);
    } else {
        refreshCsrfToken();
    }
})();

