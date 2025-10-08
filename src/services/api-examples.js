/**
 * QUICK REFERENCE - Protected API Calls
 * 
 * This file shows examples of how to make protected API calls
 * using the configured Axios client.
 */

import apiClient, { getErrorMessage } from '@/lib/api';

// ==========================================
// GET REQUESTS - Fetch Data
// ==========================================

/**
 * Example 1: Fetch current user profile
 */
export const getCurrentUser = async () => {
  try {
    const response = await apiClient.get('/me');
    return response.data;
  } catch (error) {
    console.error('Error fetching user:', getErrorMessage(error));
    throw error;
  }
};

/**
 * Example 2: Fetch user's scan history
 */
export const getScanHistory = async () => {
  try {
    const response = await apiClient.get('/scans');
    return response.data;
  } catch (error) {
    console.error('Error fetching scans:', getErrorMessage(error));
    throw error;
  }
};

/**
 * Example 3: Fetch recycling statistics
 */
export const getRecyclingStats = async () => {
  try {
    const response = await apiClient.get('/stats');
    return response.data;
  } catch (error) {
    console.error('Error fetching stats:', getErrorMessage(error));
    throw error;
  }
};

// ==========================================
// POST REQUESTS - Create Data
// ==========================================

/**
 * Example 4: Save a new scan
 */
export const saveScan = async (scanData) => {
  try {
    const response = await apiClient.post('/scans', {
      barcode: scanData.barcode,
      material_type: scanData.materialType,
      scanned_at: new Date().toISOString()
    });
    return response.data;
  } catch (error) {
    console.error('Error saving scan:', getErrorMessage(error));
    throw error;
  }
};

/**
 * Example 5: Submit feedback
 */
export const submitFeedback = async (feedback) => {
  try {
    const response = await apiClient.post('/feedback', {
      message: feedback.message,
      rating: feedback.rating
    });
    return response.data;
  } catch (error) {
    console.error('Error submitting feedback:', getErrorMessage(error));
    throw error;
  }
};

// ==========================================
// PUT REQUESTS - Update Data
// ==========================================

/**
 * Example 6: Update user profile
 */
export const updateProfile = async (profileData) => {
  try {
    const response = await apiClient.put('/profile', {
      name: profileData.name,
      email: profileData.email,
      phone: profileData.phone
    });
    return response.data;
  } catch (error) {
    console.error('Error updating profile:', getErrorMessage(error));
    throw error;
  }
};

/**
 * Example 7: Update password
 */
export const updatePassword = async (passwordData) => {
  try {
    const response = await apiClient.put('/password', {
      current_password: passwordData.currentPassword,
      new_password: passwordData.newPassword
    });
    return response.data;
  } catch (error) {
    console.error('Error updating password:', getErrorMessage(error));
    throw error;
  }
};

// ==========================================
// DELETE REQUESTS - Remove Data
// ==========================================

/**
 * Example 8: Delete a scan from history
 */
export const deleteScan = async (scanId) => {
  try {
    const response = await apiClient.delete(`/scans/${scanId}`);
    return response.data;
  } catch (error) {
    console.error('Error deleting scan:', getErrorMessage(error));
    throw error;
  }
};

/**
 * Example 9: Delete user account
 */
export const deleteAccount = async () => {
  try {
    const response = await apiClient.delete('/account');
    return response.data;
  } catch (error) {
    console.error('Error deleting account:', getErrorMessage(error));
    throw error;
  }
};

// ==========================================
// USAGE IN VUE COMPONENTS
// ==========================================

/**
 * Example 10: Using in a Vue component
 * 
 * <script setup>
 * import { ref, onMounted } from 'vue';
 * import { getCurrentUser, updateProfile } from '@/services/api-examples';
 * 
 * const user = ref(null);
 * const isLoading = ref(false);
 * 
 * // Fetch user on component mount
 * onMounted(async () => {
 *   isLoading.value = true;
 *   try {
 *     user.value = await getCurrentUser();
 *   } catch (error) {
 *     console.error('Failed to load user');
 *   } finally {
 *     isLoading.value = false;
 *   }
 * });
 * 
 * // Update user profile
 * const handleUpdateProfile = async () => {
 *   try {
 *     const updatedUser = await updateProfile({
 *       name: 'John Doe',
 *       email: 'john@example.com',
 *       phone: '123-456-7890'
 *     });
 *     user.value = updatedUser;
 *     alert('Profile updated!');
 *   } catch (error) {
 *     alert('Failed to update profile');
 *   }
 * };
 * </script>
 */

// ==========================================
// FILE UPLOAD EXAMPLES
// ==========================================

/**
 * Example 11: Upload profile picture
 */
export const uploadProfilePicture = async (file) => {
  try {
    const formData = new FormData();
    formData.append('profile_picture', file);
    
    const response = await apiClient.post('/profile/picture', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });
    
    return response.data;
  } catch (error) {
    console.error('Error uploading picture:', getErrorMessage(error));
    throw error;
  }
};

// ==========================================
// QUERY PARAMETERS
// ==========================================

/**
 * Example 12: Fetch scans with pagination and filters
 */
export const getScansFiltered = async (filters) => {
  try {
    const response = await apiClient.get('/scans', {
      params: {
        page: filters.page || 1,
        limit: filters.limit || 10,
        material_type: filters.materialType,
        start_date: filters.startDate,
        end_date: filters.endDate
      }
    });
    return response.data;
  } catch (error) {
    console.error('Error fetching filtered scans:', getErrorMessage(error));
    throw error;
  }
};

// ==========================================
// NOTES
// ==========================================

/**
 * IMPORTANT NOTES:
 * 
 * 1. JWT Token: Automatically attached to all requests by the Axios interceptor
 * 2. Error Handling: 401 errors trigger automatic logout and redirect to /login
 * 3. Base URL: Configured in .env as VITE_API_BASE_URL
 * 4. Timeout: Set to 10 seconds (configurable in src/lib/api.js)
 * 5. Content-Type: Defaults to application/json (except for file uploads)
 * 
 * BACKEND API STRUCTURE:
 * All endpoints should be prefixed with /api (e.g., /api/profile, /api/scans)
 * This is configured in the .env file and Axios client
 */
