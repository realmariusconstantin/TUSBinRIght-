/**
 * Backend Error Handling Composable
 * 
 * This composable provides reusable error handling for backend validation
 * and other API errors from CodeIgniter 4.
 * 
 * Usage:
 * const { errors, generalError, handleError, clearErrors } = useBackendErrors();
 */

import { ref, computed } from 'vue';

export function useBackendErrors() {
  // Field-specific validation errors from backend
  const errors = ref({});
  
  // General error message
  const generalError = ref('');

  /**
   * Handle error from backend API call
   * Extracts validation errors and general error messages
   * 
   * @param {Error} error - Axios error object
   */
  const handleError = (error) => {
    // Clear previous errors
    errors.value = {};
    generalError.value = '';

    console.error('Backend Error:', error);

    // Handle validation errors (422 Unprocessable Entity or 400 Bad Request)
    if (error.response?.status === 422 || error.response?.status === 400) {
      // Check for field-specific validation errors
      if (error.response.data?.errors && typeof error.response.data.errors === 'object') {
        errors.value = error.response.data.errors;
      } 
      // Check for general validation message
      else if (error.response.data?.message) {
        generalError.value = error.response.data.message;
      }
    } 
    // Handle other backend errors (404, 500, etc.)
    else if (error.response?.data?.message) {
      generalError.value = error.response.data.message;
    } 
    // Handle network or unknown errors
    else if (error.message) {
      generalError.value = error.message;
    } 
    else {
      generalError.value = 'An unexpected error occurred. Please try again.';
    }
  };

  /**
   * Clear all errors
   */
  const clearErrors = () => {
    errors.value = {};
    generalError.value = '';
  };

  /**
   * Clear a specific field error
   * @param {string} field - Field name to clear error for
   */
  const clearError = (field) => {
    if (errors.value[field]) {
      delete errors.value[field];
    }
  };

  /**
   * Check if there are any errors
   * @returns {boolean}
   */
  const hasErrors = computed(() => {
    return Object.keys(errors.value).length > 0 || generalError.value !== '';
  });

  /**
   * Check if a specific field has an error
   * @param {string} field - Field name to check
   * @returns {boolean}
   */
  const hasError = (field) => {
    return !!errors.value[field];
  };

  /**
   * Get error message for a specific field
   * @param {string} field - Field name
   * @returns {string|null}
   */
  const getError = (field) => {
    return errors.value[field] || null;
  };

  return {
    // State
    errors,
    generalError,
    hasErrors,
    
    // Methods
    handleError,
    clearErrors,
    clearError,
    hasError,
    getError
  };
}

/**
 * Example Usage in Login Component:
 * 
 * <script setup>
 * import { ref } from 'vue';
 * import { useBackendErrors } from '@/composables/useBackendErrors';
 * import apiClient from '@/lib/api';
 * 
 * const { errors, generalError, handleError, clearErrors } = useBackendErrors();
 * 
 * const form = ref({
 *   email: '',
 *   password: ''
 * });
 * 
 * const isLoading = ref(false);
 * 
 * const handleLogin = async () => {
 *   clearErrors();
 *   isLoading.value = true;
 *   
 *   try {
 *     const response = await apiClient.post('/login', form.value);
 *     // Handle success
 *     console.log('Login successful', response.data);
 *   } catch (error) {
 *     handleError(error);
 *   } finally {
 *     isLoading.value = false;
 *   }
 * };
 * </script>
 * 
 * <template>
 *   <form @submit.prevent="handleLogin">
 *     <!-- General error message -->
 *     <div v-if="generalError" class="alert alert-error">
 *       {{ generalError }}
 *     </div>
 *     
 *     <!-- Email field -->
 *     <input
 *       v-model="form.email"
 *       type="email"
 *       :class="{ 'error': errors.email }"
 *     />
 *     <span v-if="errors.email" class="error-message">
 *       {{ errors.email }}
 *     </span>
 *     
 *     <!-- Password field -->
 *     <input
 *       v-model="form.password"
 *       type="password"
 *       :class="{ 'error': errors.password }"
 *     />
 *     <span v-if="errors.password" class="error-message">
 *       {{ errors.password }}
 *     </span>
 *     
 *     <button type="submit" :disabled="isLoading">Login</button>
 *   </form>
 * </template>
 */
