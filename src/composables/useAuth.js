/**
 * Authentication Composable
 * 
 * Provides reusable authentication logic including:
 * - Check if user is logged in
 * - Get current user info from backend
 * - Handle logout
 * - Login and register helpers
 */

import { ref, computed } from 'vue';
import apiClient, { getErrorMessage } from '@/lib/api';
import router from '../router';

// Reactive state (shared across all component instances)
const user = ref(null);
const isAuthenticated = ref(false);
const isLoading = ref(false);

export function useAuth() {
  /**
   * Check if user has a valid token
   */
  const hasToken = () => {
    return !!localStorage.getItem('auth_token');
  };

  /**
   * Initialize auth state from localStorage
   */
  const initAuth = () => {
    const token = localStorage.getItem('auth_token');
    const userData = localStorage.getItem('user_data');
    
    if (token && userData) {
      try {
        user.value = JSON.parse(userData);
        isAuthenticated.value = true;
      } catch (error) {
        console.error('Failed to parse user data:', error);
        logout();
      }
    }
  };

  /**
   * Login user
   * @param {string} email - User email
   * @param {string} password - User password
   * @returns {Promise<Object>} Response with user data and token
   */
  const login = async (email, password) => {
    isLoading.value = true;
    
    try {
      // Call backend login endpoint
      const response = await apiClient.post('/login', {
        email,
        password
      });
      
      // Extract token and user data from response
      const { token, user: userData } = response.data;
      
      // Store in localStorage
      localStorage.setItem('auth_token', token);
      localStorage.setItem('user_data', JSON.stringify(userData));
      
      // Update reactive state
      user.value = userData;
      isAuthenticated.value = true;
      
      return response.data;
    } catch (error) {
      console.error('Login failed:', error);
      throw new Error(getErrorMessage(error));
    } finally {
      isLoading.value = false;
    }
  };

  /**
   * Register new user
   * @param {Object} userData - User registration data
   * @returns {Promise<Object>} Response with user data and token
   */
  const register = async (userData) => {
    isLoading.value = true;
    
    try {
      // Call backend register endpoint
      const response = await apiClient.post('/register', userData);
      
      // Extract token and user data from response
      const { token, user: newUser } = response.data;
      
      // Store in localStorage
      localStorage.setItem('auth_token', token);
      localStorage.setItem('user_data', JSON.stringify(newUser));
      
      // Update reactive state
      user.value = newUser;
      isAuthenticated.value = true;
      
      return response.data;
    } catch (error) {
      console.error('Registration failed:', error);
      throw new Error(getErrorMessage(error));
    } finally {
      isLoading.value = false;
    }
  };

  /**
   * Fetch current user info from backend
   * Validates token and refreshes user data
   * @returns {Promise<Object>} Current user data
   */
  const fetchUser = async () => {
    if (!hasToken()) {
      throw new Error('No authentication token found');
    }
    
    isLoading.value = true;
    
    try {
      // Call backend /me endpoint to get current user
      const response = await apiClient.get('/me');
      
      // Update user data
      const userData = response.data.user || response.data;
      user.value = userData;
      isAuthenticated.value = true;
      
      // Update localStorage
      localStorage.setItem('user_data', JSON.stringify(userData));
      
      return userData;
    } catch (error) {
      console.error('Failed to fetch user:', error);
      // If fetch fails, logout user
      logout();
      throw new Error(getErrorMessage(error));
    } finally {
      isLoading.value = false;
    }
  };

  /**
   * Logout user
   * Clears all authentication data and redirects to login
   */
  const logout = async () => {
    try {
      // Optional: Call backend logout endpoint
      if (hasToken()) {
        await apiClient.post('/logout').catch(() => {
          // Ignore errors on logout - still clear local data
        });
      }
    } finally {
      // Clear all authentication data
      localStorage.removeItem('auth_token');
      localStorage.removeItem('user_data');
      
      // Reset reactive state
      user.value = null;
      isAuthenticated.value = false;
      
      // Redirect to login page
      router.push('/login');
    }
  };

  /**
   * Update user profile
   * @param {Object} profileData - Updated profile data
   * @returns {Promise<Object>} Updated user data
   */
  const updateProfile = async (profileData) => {
    isLoading.value = true;
    
    try {
      const response = await apiClient.put('/profile', profileData);
      
      // Update local user data
      const updatedUser = response.data.user || response.data;
      user.value = updatedUser;
      localStorage.setItem('user_data', JSON.stringify(updatedUser));
      
      return updatedUser;
    } catch (error) {
      console.error('Profile update failed:', error);
      throw new Error(getErrorMessage(error));
    } finally {
      isLoading.value = false;
    }
  };

  // Computed properties
  const currentUser = computed(() => user.value);
  const isLoggedIn = computed(() => isAuthenticated.value && hasToken());

  return {
    // State
    user: currentUser,
    isAuthenticated: isLoggedIn,
    isLoading: computed(() => isLoading.value),
    
    // Methods
    login,
    register,
    logout,
    fetchUser,
    updateProfile,
    hasToken,
    initAuth
  };
}

/**
 * Example Usage in Components:
 * 
 * <script setup>
 * import { useAuth } from '@/composables/useAuth';
 * 
 * const { user, isAuthenticated, login, logout, fetchUser } = useAuth();
 * 
 * // Check if logged in
 * if (isAuthenticated.value) {
 *   console.log('User:', user.value);
 * }
 * 
 * // Login
 * const handleLogin = async () => {
 *   try {
 *     await login('user@example.com', 'password123');
 *     // Success - user is logged in
 *   } catch (error) {
 *     console.error(error.message);
 *   }
 * };
 * 
 * // Fetch user data
 * onMounted(async () => {
 *   if (isAuthenticated.value) {
 *     await fetchUser();
 *   }
 * });
 * 
 * // Logout
 * const handleLogout = () => {
 *   logout();
 * };
 * </script>
 */
