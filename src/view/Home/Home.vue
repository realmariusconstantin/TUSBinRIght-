<template>
  <div class="home-page">
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-container">
        <h1 class="welcome-text">Welcome{{ username ? `, ${username}` : '' }}!</h1>
        <p class="hero-subtitle">Scan, Search, and Recycle Smarter</p>

        <!-- Search Bar -->
        <div class="search-container">
          <input type="text" v-model="searchQuery" placeholder="Search for recyclable items..." class="search-input"
            @keyup.enter="handleSearch" />
          <button @click="handleSearch" class="search-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"></circle>
              <path d="m21 21-4.35-4.35"></path>
            </svg>
          </button>
        </div>

        <!-- Scanner Section -->
        <div class="scanner-container">
          <div class="scanner-options">
            <button @click="toggleScanner" class="scanner-btn" :class="{ active: isScannerActive }">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2">
                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                <circle cx="12" cy="13" r="4"></circle>
              </svg>
              {{ isScannerActive ? 'Stop Camera' : 'Scan with Camera' }}
            </button>

            <button @click="triggerFileInput" class="scanner-btn upload-btn">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="17 8 12 3 7 8"></polyline>
                <line x1="12" y1="3" x2="12" y2="15"></line>
              </svg>
              Upload QR/Barcode
            </button>
            <input type="file" ref="fileInput" @change="handleFileUpload" accept="image/*" style="display: none;" />
          </div>

          <!-- Camera Preview -->
          <div v-if="isScannerActive" class="camera-preview">
            <video ref="videoElement" id="video" autoplay playsinline></video>
          </div>

          <!-- Scan Result -->
          <div v-if="scanResult" class="scan-result">
            <p class="result-label">Scanned Code:</p>
            <p class="result-value">{{ scanResult }}</p>
            <p v-if="detectedMaterial" class="detected-material">
              Detected: <strong>{{ detectedMaterial }}</strong>
            </p>
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
              <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2">
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
              <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
            </div>
            <h3>2. Get Info</h3>
            <p>Instantly discover if your item is recyclable, what bin it goes in, and proper disposal methods.</p>
          </div>

          <div class="info-card">
            <div class="card-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2">
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
import { BrowserMultiFormatReader } from '@zxing/browser';

export default {
  name: 'Home',
  data() {
    return {
      username: 'Guest', // Replace with actual user data from authentication
      searchQuery: '',
      isScannerActive: false,
      scanResult: '',
      detectedMaterial: '',
      codeReader: null,
      videoStream: null,
      isProcessing: false
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

        // Initialize ZXing code reader
        this.codeReader = new BrowserMultiFormatReader();

        // Get video element
        const videoElement = this.$refs.videoElement;
        
        if (!videoElement) {
          throw new Error('Video element not found');
        }

        // Start decoding from video device (undefined = use default camera)
        await this.codeReader.decodeFromVideoDevice(
          undefined, // Let browser choose default camera (usually back camera on mobile)
          videoElement,
          (result, err) => {
            if (result && !this.isProcessing) {
              this.onScanSuccess(result.getText());
            }
            // Silently ignore errors during continuous scanning
          }
        );

        // Store video stream for cleanup
        this.videoStream = videoElement.srcObject;

      } catch (err) {
        console.error('Error starting scanner:', err);
        alert(`Unable to access camera: ${err.message}\nPlease ensure camera permissions are granted.`);
        this.isScannerActive = false;
      }
    },

    async stopScanner() {
      try {
        // Stop the code reader
        if (this.codeReader) {
          this.codeReader.reset();
        }

        // Stop video stream
        if (this.videoStream) {
          this.videoStream.getTracks().forEach(track => track.stop());
          this.videoStream = null;
        }

        // Clear video element
        const videoElement = this.$refs.videoElement;
        if (videoElement) {
          videoElement.srcObject = null;
        }
      } catch (err) {
        console.error('Error stopping scanner:', err);
      }
      
      this.isScannerActive = false;
    },

    onScanSuccess(decodedText) {
      // Prevent duplicate scans - ignore if already processing
      if (this.isProcessing || this.scanResult) {
        console.log('Already processing a scan, ignoring duplicate');
        return;
      }
      
      console.log('Scan successful:', decodedText);
      this.isProcessing = true;
      this.scanResult = decodedText;
      this.stopScanner();
      this.routeToMaterial(decodedText);
    },

    triggerFileInput() {
      this.$refs.fileInput.click();
    },

    async handleFileUpload(event) {
      const file = event.target.files[0];
      if (!file) return;

      try {
        const imageUrl = URL.createObjectURL(file);
        const img = document.createElement('img');
        img.src = imageUrl;

        img.onload = async () => {
          try {
            const reader = new BrowserMultiFormatReader();
            const result = await reader.decodeFromImageElement(img);
            this.onScanSuccess(result.getText());
          } catch (err) {
            console.error('No barcode found in image:', err);
            alert('No barcode found in the uploaded image. Please try again with a clearer image.');
          } finally {
            URL.revokeObjectURL(imageUrl);
          }
        };

        img.onerror = () => {
          URL.revokeObjectURL(imageUrl);
          alert('Error loading image. Please try a different file.');
        };
      } catch (err) {
        console.error('Error handling file upload:', err);
        alert('Error processing image. Please try again.');
      }
    },

    lookupItem() {
      if (this.scanResult) {
        this.routeToMaterial(this.scanResult);
      }
    },

    async routeToMaterial(barcodeData) {
      console.log('Processing barcode:', barcodeData);
      
      // Try to identify material type from the actual barcode
      const materialType = await this.identifyMaterial(barcodeData);
      
      if (materialType) {
        // Store detected material for display
        this.detectedMaterial = materialType.charAt(0).toUpperCase() + materialType.slice(1);
        
        // Small delay to show detection, then route
        setTimeout(() => {
          // Clear the scan result before routing to prevent re-triggering
          this.scanResult = '';
          this.detectedMaterial = '';
          this.$router.push(`/material/${materialType}`);
        }, 1000);
      } else {
        // Show options if we can't determine automatically
        this.showMaterialOptions(barcodeData);
      }
    },

    async identifyMaterial(barcodeData) {
      // Check for numeric barcodes (EAN, UPC, etc.)
      const isNumericBarcode = /^\d{8,13}$/.test(barcodeData);
      
      if (isNumericBarcode) {
        return this.identifyByNumericBarcode(barcodeData);
      }
      
      // Check for QR codes or text-based codes
      return this.identifyByTextPattern(barcodeData);
    },

    identifyByNumericBarcode(barcodeData) {
      // For product barcodes, we use heuristics based on common patterns
      // In production, you'd call an API like Open Food Facts or UPC Database
      
      // Remove spaces from barcode for comparison
      const cleanBarcode = barcodeData.replace(/\s+/g, '');
      
      const prefix = cleanBarcode.substring(0, 3);
      const prefix4 = cleanBarcode.substring(0, 4);
      const prefix7 = cleanBarcode.substring(0, 7);
      
      // Ireland products (EAN-13 starting with 5000112 - Coca-Cola HBC Ireland)
      // These need more specific detection based on the full pattern
      if (prefix7 === '5000112') {
        // Coca-Cola Ireland products
        // Cans typically have certain digit patterns in positions 8-10
        const segment = cleanBarcode.substring(7, 10);
        
        // Known can patterns for Irish Coca-Cola products
        const canSegments = ['626', '627', '628', '629', '630', '631', '632', '633', '634', '635'];
        
        // Known plastic bottle patterns
        const plasticSegments = ['636', '637', '638', '639', '640', '641', '642', '643', '644', '645'];
        
        if (canSegments.includes(segment)) {
          return 'can';
        }
        
        if (plasticSegments.includes(segment)) {
          return 'plastic';
        }
        
        // If uncertain, check the last few digits as secondary heuristic
        // Cans tend to have lower numbers in this range
        const numSegment = parseInt(segment);
        if (numSegment <= 635) {
          return 'can';
        } else {
          return 'plastic';
        }
      }
      
      // Known Coca-Cola can barcodes (US/Other regions)
      const cokeCanCodes = [
        '0049000', '0078000', // Coca-Cola Company products (US)
        '004900', '007800',   // Shorter versions
      ];
      
      // Check for known Coca-Cola/Pepsi/Soda can patterns
      for (const code of cokeCanCodes) {
        if (cleanBarcode.startsWith(code)) {
          return 'can';
        }
      }
      
      // Canned beverages - expanded ranges (soda, beer, energy drinks)
      const canRanges = [
        '0049', '0078', '0012', '0018', '0078',  // Major beverage brands (Coke, Pepsi, Dr Pepper)
        '012', '018', '049', '078',              // Alternative formats
        '051', '054', '064', '065', '066',       // Energy drinks, craft sodas
        '072', '073', '074', '075', '076', '077' // Canned foods/drinks
      ];
      
      // Water bottles (always plastic)
      const waterRanges = ['024', '028', '035', '048', '050', '052'];
      
      // Plastic bottle beverages (juice, sports drinks, etc.)
      const plasticBeverageRanges = ['040', '041', '042', '043', '053', '055', '056', '057', '058'];
      
      // Glass bottles (wine, spirits, beer in glass)
      const glassRanges = ['080', '081', '082', '083', '084', '085', '086', '087', '088', '089'];
      
      // Check for aluminum cans FIRST (highest priority)
      if (canRanges.includes(prefix4) || canRanges.includes(prefix)) {
        return 'can';
      }
      
      // Check for water bottles (plastic)
      if (waterRanges.includes(prefix)) {
        return 'plastic';
      }
      
      // Check for plastic beverage bottles
      if (plasticBeverageRanges.includes(prefix)) {
        return 'plastic';
      }
      
      // Check for glass bottles
      if (glassRanges.includes(prefix)) {
        return 'glass';
      }
      
      // If we can't determine with confidence, return null to show options
      return null;
    },

    identifyByTextPattern(barcodeData) {
      const lowerCode = barcodeData.toLowerCase();
      
      // Check for metal/can indicators FIRST (highest priority)
      if (this.containsAny(lowerCode, ['aluminum', 'aluminium', 'metal', 'steel', 'tin', 'can', 'soda can', 'beer can', 'energy drink'])) {
        return 'can';
      }
      
      // Check for plastic indicators
      if (this.containsAny(lowerCode, ['plastic', 'pet', 'hdpe', 'pp', 'ldpe', 'pvc', 'ps', 'bottle', 'water', 'juice'])) {
        // Double-check it's not a can with plastic label
        if (this.containsAny(lowerCode, ['aluminum', 'aluminium', 'can'])) {
          return 'can';
        }
        return 'plastic';
      }
      
      // Check for beverage keywords that could be either
      if (this.containsAny(lowerCode, ['beverage', 'drink', 'soda'])) {
        // Default cans for generic beverages (more common for soda/beer)
        return 'can';
      }
      
      // Check for glass indicators
      if (this.containsAny(lowerCode, ['glass', 'wine', 'beer bottle', 'spirit'])) {
        return 'glass';
      }
      
      // Check for paper indicators
      if (this.containsAny(lowerCode, ['paper', 'carton', 'cardboard', 'tetra'])) {
        return 'paper';
      }
      
      // Check for recycling symbol codes (1-7)
      return this.identifyByRecyclingCode(barcodeData);
    },

    identifyByRecyclingCode(barcodeData) {
      const match = barcodeData.match(/\b([1-7])\b/);
      if (match) {
        const num = parseInt(match[1]);
        // Codes 1-7 are all plastic types
        if (num >= 1 && num <= 7) return 'plastic';
      }
      return null;
    },

    containsAny(text, keywords) {
      return keywords.some(keyword => text.includes(keyword));
    },

    showMaterialOptions(barcodeData) {
      const message = `Scanned barcode: ${barcodeData}\n\nCouldn't automatically determine material type.\nPlease select manually:`;
      
      // Clear scan result before showing options
      this.scanResult = '';
      this.detectedMaterial = '';
      
      // In a real app, you'd show a modal with options
      // For now, use confirm dialogs as a simple approach
      if (confirm(`${message}\n\nIs this a PLASTIC item? (Click OK for Yes, Cancel to see next option)`)) {
        this.$router.push('/material/plastic');
      } else if (confirm('Is this a METAL/CAN item?')) {
        this.$router.push('/material/can');
      } else if (confirm('Is this a GLASS item?')) {
        this.$router.push('/material/glass');
      } else {
        this.$router.push('/material/paper');
      }
    }
  },

  mounted() {
    // Clear any previous scan results when component mounts
    this.scanResult = '';
    this.detectedMaterial = '';
    this.isProcessing = false;
  },

  beforeUnmount() {
    // Clean up scanner when component is destroyed
    if (this.isScannerActive) {
      this.stopScanner();
    }
  }
};
</script>

<style src="./Home.css" scoped></style>

