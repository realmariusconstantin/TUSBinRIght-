/**
 * Central Axios Client for API Communication
 * 
 * This module creates a configured Axios instance that:
 * - Uses the base URL from environment variables
 * - Automatically attaches JWT tokens to requests
 * - Handles 401 Unauthorized responses (auto-logout)
 * - Provides error handling utilities
 */

import axios from 'axios';
import router from '../router';

// Create Axios instance with base configuration
const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8080/api',
  timeout: 10000, // 10 second timeout
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

/**
 * Request Interceptor
 * Automatically attaches JWT token to all outgoing requests
 */
apiClient.interceptors.request.use(
  (config) => {
    // Get token from localStorage
    const token = localStorage.getItem('auth_token');
    
    // If token exists, add it to Authorization header
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    
    console.log('API Request:', config.method?.toUpperCase(), config.url);
    return config;
  },
  (error) => {
    console.error('Request Error:', error);
    return Promise.reject(error);
  }
);

/**
 * Response Interceptor
 * Handles responses and errors globally
 * - Automatically logs out user on 401 Unauthorized
 * - Provides consistent error handling
 */
apiClient.interceptors.response.use(
  (response) => {
    // Successfully received response
    console.log('API Response:', response.status, response.config.url);
    return response;
  },
  (error) => {
    console.error('API Error:', error.response?.status, error.config?.url);
    
    // Handle 401 Unauthorized - User token expired or invalid
    if (error.response?.status === 401) {
      console.warn('Unauthorized! Logging out user...');
      
      // Clear authentication data
      localStorage.removeItem('auth_token');
      localStorage.removeItem('user_data');
      
      // Redirect to login page
      router.push('/login');
      
      // Optional: Show notification to user
      // You can integrate a toast/notification library here
      alert('Session expired. Please log in again.');
    }
    
    // Handle 403 Forbidden - User doesn't have permission
    if (error.response?.status === 403) {
      console.warn('Access forbidden');
      alert('You do not have permission to access this resource.');
    }
    
    // Handle 500 Internal Server Error
    if (error.response?.status === 500) {
      console.error('Server error');
      alert('Server error. Please try again later.');
    }
    
    return Promise.reject(error);
  }
);

/**
 * Helper function to handle API errors
 * Extracts error message from response
 */
export const getErrorMessage = (error) => {
  if (error.response?.data?.message) {
    return error.response.data.message;
  }
  if (error.response?.data?.error) {
    return error.response.data.error;
  }
  if (error.message) {
    return error.message;
  }
  return 'An unexpected error occurred';
};

/**
 * Example Usage:
 * 
 * // Import the client
 * import apiClient from '@/lib/api';
 * 
 * // GET request
 * const response = await apiClient.get('/profile');
 * 
 * // POST request
 * const response = await apiClient.post('/update', { name: 'John' });
 * 
 * // PUT request
 * const response = await apiClient.put('/profile', userData);
 * 
 * // DELETE request
 * const response = await apiClient.delete('/account');
 * 
 * // With error handling
 * try {
 *   const response = await apiClient.get('/protected-route');
 *   console.log(response.data);
 * } catch (error) {
 *   console.error(getErrorMessage(error));
 * }
 */

export default apiClient;
