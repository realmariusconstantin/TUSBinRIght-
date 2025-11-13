import { ref } from 'vue';
import api from '@/lib/api';

const user = ref(null);
const isLoading = ref(false);
const errors = ref({});
const successMessage = ref('');

export function useAuth() {
  const clearMessages = () => {
    errors.value = {};
    successMessage.value = '';
  };

  const login = async (email, password) => {
    clearMessages();
    isLoading.value = true;
    try {
      const { data } = await api.post('/login', { email, password });

      if (data.status === 'success') {
        user.value = data.user;
        //no need to store JWT manually; cookie is handled by browser
        localStorage.setItem('user', JSON.stringify(data.user));
        successMessage.value = data.message || 'Login successful!';
        return { success: true };
      } else {
        errors.value = { general: data.message || 'Login failed' };
        return { success: false };
      }
    } catch (e) {
      const errorData = e.response?.data || {};
      
      // Handle rate limiting (429 status)
      if (e.response?.status === 429) {
        errors.value = { general: errorData.message || 'Too many login attempts' };
        return { 
          success: false, 
          data: {
            rateLimited: true,
            message: errorData.message,
            remainingTime: errorData.remaining_time,
            attemptsRemaining: errorData.attempts_remaining
          }
        };
      }
      
      // Handle general errors
      if (errorData.errors) {
        errors.value = errorData.errors;
      } else {
        errors.value = { general: errorData.message || 'Login failed.' };
      }
      
      // Return attempts remaining if available
      return { 
        success: false, 
        data: {
          rateLimited: false,
          attemptsRemaining: errorData.attempts_remaining
        }
      };
    } finally {
      isLoading.value = false;
    }
  };

  const logout = async () => {
    isLoading.value = true;
    try {
      await api.post('/logout'); //cookie deleted by backend
      user.value = null;
      localStorage.removeItem('user');
      return { success: true };
    } catch (e) {
      return { success: false };
    } finally {
      isLoading.value = false;
    }
  };

  const fetchUser = async () => {
    isLoading.value = true;
    try {
      console.log('Fetching user profile...');
      const { data } = await api.get('/profile');
      console.log('Profile response:', data);
      
      if (data.status === 'success') {
        user.value = data.user;
        localStorage.setItem('user', JSON.stringify(data.user));
        return { success: true };
      } else {
        user.value = null;
        return { success: false };
      }
    } catch (error) {
      console.error('fetchUser error:', error.response?.data || error.message);
      user.value = null;
      localStorage.removeItem('user');
      return { success: false };
    } finally {
      isLoading.value = false;
    }
  };

  const register = async (name, email, password, confirmPassword) => {
    clearMessages();
    isLoading.value = true;
    try {
      const { data } = await api.post('/register', { name, email, password, confirmPassword });
      if (data.status === 'success') {
        // Registration now returns user data (auto-login)
        if (data.user) {
          user.value = data.user;
          localStorage.setItem('user', JSON.stringify(data.user));
        }
        successMessage.value = data.message || 'Registration successful!';
        return { success: true };
      } else {
        errors.value = { general: data.message || 'Registration failed' };
        return { success: false };
      }
    } catch (e) {
      if (e.response?.data?.errors) {
        errors.value = e.response.data.errors;
      } else {
        errors.value = { general: e?.response?.data?.message || 'Registration failed' };
      }
      return { success: false };
    } finally {
      isLoading.value = false;
    }
  };

  const initUser = () => {
    const storedUser = localStorage.getItem('user');
    if (storedUser) {
      try {
        user.value = JSON.parse(storedUser);
      } catch {
        localStorage.removeItem('user');
      }
    }
  };

  return {
    user,
    isLoading,
    errors,
    successMessage,
    login,
    logout,
    fetchUser,
    register,
    clearMessages,
    initUser
  };
}
