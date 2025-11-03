// src/lib/api.js
import axios from 'axios';

// Create Axios instance configured for HttpOnly cookie authentication
const api = axios.create({
    // ✅ Add "/api" to the backend base URL
    baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost/tusbinright/public',
    headers: {
        'Content-Type': 'application/json'
    },
    withCredentials: true // ensures cookies/JWTs are sent automatically
});

// Optional: Handle expired tokens globally
api.interceptors.response.use(
    response => response,
    error => {
        if (error.response && error.response.status === 401) {
            localStorage.removeItem('user'); // Clear any cached session
        }
        return Promise.reject(error);
    }
);

export default api;
