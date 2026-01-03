import axios from 'axios';
window.axios = axios;

// Set CSRF token for all axios requests
function setCsrfToken() {
    const token = document.head.querySelector('meta[name="csrf-token"]');
    if (token) {
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
    } else {
        console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
    }
}

setCsrfToken();

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Handle 419 errors globally - refresh CSRF token and retry
window.axios.interceptors.response.use(
    response => response,
    async error => {
        if (error.response && error.response.status === 419) {
            // CSRF token expired, refresh it
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
                        // Update meta tag
                        const metaTag = document.head.querySelector('meta[name="csrf-token"]');
                        if (metaTag) {
                            metaTag.setAttribute('content', data.token);
                        }
                        
                        // Update axios defaults
                        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = data.token;
                        
                        // Retry the original request
                        const config = error.config;
                        if (config) {
                            config.headers['X-CSRF-TOKEN'] = data.token;
                            return window.axios.request(config);
                        }
                    }
                }
            } catch (refreshError) {
                console.error('Failed to refresh CSRF token:', refreshError);
            }
            
            // If refresh failed, show user-friendly message
            if (confirm('Your session has expired. Click OK to refresh the page and continue.')) {
                window.location.reload();
            }
        }
        return Promise.reject(error);
    }
);

// Expose refresh function globally
window.refreshCsrfToken = async function() {
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
                const metaTag = document.head.querySelector('meta[name="csrf-token"]');
                if (metaTag) {
                    metaTag.setAttribute('content', data.token);
                }
                window.axios.defaults.headers.common['X-CSRF-TOKEN'] = data.token;
                return data.token;
            }
        }
    } catch (error) {
        console.error('Failed to refresh CSRF token:', error);
    }
    return null;
};
