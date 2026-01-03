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
        const response = await originalFetch(...args);
        
        if (response.status === 419) {
            // CSRF token expired, try to refresh
            const newToken = await refreshCsrfToken();
            
            if (newToken && args[1] && args[1].method && args[1].method !== 'GET') {
                // Retry the request with new token
                const config = { ...args[1] };
                config.headers = config.headers || {};
                config.headers['X-CSRF-TOKEN'] = newToken;
                
                const retryResponse = await originalFetch(args[0], config);
                if (retryResponse.ok) {
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

    // Periodically refresh CSRF token (every 30 minutes)
    setInterval(async () => {
        await refreshCsrfToken();
    }, 30 * 60 * 1000);

    // Expose refresh function globally
    window.refreshCsrfToken = refreshCsrfToken;
    window.getCsrfToken = getCsrfToken;
})();

