<template>
  <div class="home-page">
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-container">
        <h1 class="welcome-text">Welcome{{ username ? `, ${username}` : '' }}!</h1>
        <p class="hero-subtitle">Scan, Search, and Recycle Smarter</p>
        
        <!-- Search Bar -->
        <div class="search-container">
          <input 
            type="text" 
            v-model="searchQuery" 
            placeholder="Search for recyclable items..." 
            class="search-input"
            @keyup.enter="handleSearch"
          />
          <button @click="handleSearch" class="search-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"></circle>
              <path d="m21 21-4.35-4.35"></path>
            </svg>
          </button>
        </div>

        <!-- Scanner Section -->
        <div class="scanner-container">
          <div class="scanner-options">
            <button 
              @click="toggleScanner" 
              class="scanner-btn"
              :class="{ active: isScannerActive }"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                <circle cx="12" cy="13" r="4"></circle>
              </svg>
              {{ isScannerActive ? 'Stop Camera' : 'Scan with Camera' }}
            </button>
            
            <button @click="triggerFileInput" class="scanner-btn upload-btn">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="17 8 12 3 7 8"></polyline>
                <line x1="12" y1="3" x2="12" y2="15"></line>
              </svg>
              Upload QR/Barcode
            </button>
            <input 
              type="file" 
              ref="fileInput" 
              @change="handleFileUpload" 
              accept="image/*" 
              style="display: none;"
            />
          </div>

          <!-- Camera Preview -->
          <div v-if="isScannerActive" class="camera-preview">
            <div id="qr-reader" ref="qrReader"></div>
          </div>

          <!-- Scan Result -->
          <div v-if="scanResult" class="scan-result">
            <p class="result-label">Scanned Code:</p>
            <p class="result-value">{{ scanResult }}</p>
            <button @click="lookupItem" class="lookup-btn">Look Up Item</button>
          </div>
        </div>
      </div>
    </section>

    <!-- Information Sections -->
    <section class="info-section">
      <div class="info-container">
        <h2>How It Works</h2>
        <div class="info-cards">
          <div class="info-card">
            <div class="card-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
              </svg>
            </div>
            <h3>1. Scan or Search</h3>
            <p>Use your camera to scan barcodes or QR codes on recyclable items, or search manually by name.</p>
          </div>

          <div class="info-card">
            <div class="card-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
            </div>
            <h3>2. Get Info</h3>
            <p>Instantly discover if your item is recyclable, what bin it goes in, and proper disposal methods.</p>
          </div>

          <div class="info-card">
            <div class="card-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
              </svg>
            </div>
            <h3>3. Recycle Right</h3>
            <p>Follow the guidelines and contribute to a cleaner, greener planet. Track your recycling impact!</p>
          </div>
        </div>
      </div>
    </section>

    <!-- About Section -->
    <section class="about-section">
      <div class="about-container">
        <h2>About TUSBinRight+</h2>
        <p>
          We're on a mission to make recycling easier and more accessible for everyone. 
          Our platform uses cutting-edge technology to help you identify recyclable materials, 
          understand proper disposal methods, and track your environmental impact.
        </p>
        <p>
          Join thousands of users who are making a difference, one scan at a time. 
          Together, we can reduce waste and build a sustainable future.
        </p>
        <div class="stats">
          <div class="stat-item">
            <h3>10K+</h3>
            <p>Items Scanned</p>
          </div>
          <div class="stat-item">
            <h3>5K+</h3>
            <p>Active Users</p>
          </div>
          <div class="stat-item">
            <h3>95%</h3>
            <p>Accuracy Rate</p>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import { Html5Qrcode } from 'html5-qrcode';

export default {
  name: 'Home',
  data() {
    return {
      username: 'Guest', // Replace with actual user data from authentication
      searchQuery: '',
      isScannerActive: false,
      scanResult: '',
      html5QrCode: null
    };
  },
  methods: {
    handleSearch() {
      if (this.searchQuery.trim()) {
        console.log('Searching for:', this.searchQuery);
        // Add your search logic here (API call, routing, etc.)
        alert(`Searching for: ${this.searchQuery}`);
      }
    },
    
    async toggleScanner() {
      if (this.isScannerActive) {
        await this.stopScanner();
      } else {
        await this.startScanner();
      }
    },
    
    async startScanner() {
      try {
        this.isScannerActive = true;
        await this.$nextTick(); // Wait for DOM to update
        
        this.html5QrCode = new Html5Qrcode("qr-reader");
        
        const config = { 
          fps: 10, 
          qrbox: { width: 250, height: 250 },
          formatsToSupport: [
            Html5Qrcode.SCAN_TYPE_CAMERA
          ]
        };
        
        await this.html5QrCode.start(
          { facingMode: "environment" }, // Use back camera
          config,
          this.onScanSuccess,
          this.onScanError
        );
      } catch (err) {
        console.error('Error starting scanner:', err);
        alert('Unable to access camera. Please ensure camera permissions are granted.');
        this.isScannerActive = false;
      }
    },
    
    async stopScanner() {
      if (this.html5QrCode) {
        try {
          await this.html5QrCode.stop();
          this.html5QrCode.clear();
        } catch (err) {
          console.error('Error stopping scanner:', err);
        }
      }
      this.isScannerActive = false;
    },
    
    onScanSuccess(decodedText, decodedResult) {
      console.log('Scan successful:', decodedText);
      this.scanResult = decodedText;
      this.stopScanner();
    },
    
    onScanError(errorMessage) {
      // Silent error handling - scanning errors are common and expected
    },
    
    triggerFileInput() {
      this.$refs.fileInput.click();
    },
    
    async handleFileUpload(event) {
      const file = event.target.files[0];
      if (!file) return;
      
      try {
        const html5QrCode = new Html5Qrcode("qr-reader-file");
        const result = await html5QrCode.scanFile(file, true);
        this.scanResult = result;
        console.log('File scan result:', result);
      } catch (err) {
        console.error('Error scanning file:', err);
        alert('Could not read QR/Barcode from image. Please try another image.');
      }
    },
    
    lookupItem() {
      if (this.scanResult) {
        console.log('Looking up item:', this.scanResult);
        // Add your lookup logic here (API call, database query, etc.)
        alert(`Looking up item with code: ${this.scanResult}`);
      }
    }
  },
  
  beforeUnmount() {
    // Clean up scanner when component is destroyed
    if (this.html5QrCode && this.isScannerActive) {
      this.stopScanner();
    }
  }
};
</script>

<style src="./Home.css" scoped></style>
