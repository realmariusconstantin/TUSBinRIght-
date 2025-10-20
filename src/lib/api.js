import axios from 'axios';

// Create Axios instance configured for HttpOnly cookie authentication
const api = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost/tusbinright/public', 
    headers: {
        'Content-Type': 'application/json'
    },

    // This ensures the JWT stored in HttpOnly cookie is automatically sent to backend
    withCredentials: true
});

// Optional: Add response interceptor to handle 401 Unauthorized globally
api.interceptors.response.use(
    response => response,
    error => {
        // If backend returns 401, JWT is invalid/expired
        if (error.response && error.response.status === 401) {
            // Clear any user data but DON'T force redirect
            // Let the component handle the redirect logic
            localStorage.removeItem('user');
        }
        return Promise.reject(error);
    }
);

export default api;
