<template>
  <div class="recycling-info-page">
    <!-- Hero Section -->
    <section class="hero">
      <div class="hero-content">
        <h1 class="page-title">Recycling Information</h1>
        <p class="page-subtitle">Learn how to recycle properly and make a difference</p>
      </div>
      <img src="../../images/waterBottle.png" alt="Water Bottle" class="floating-image bottle-1" />
      <img src="../../images/can.png" alt="Can" class="floating-image can-1" />
    </section>

    <!-- Statistics Section -->
    <section class="stats-section">
      <div class="stats-container">
        <h2 class="section-title">Recycling Impact</h2>
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon">♻️</div>
            <h3 class="stat-number">75%</h3>
            <p class="stat-label">Waste Can Be Recycled</p>
          </div>
          <div class="stat-card">
            <div class="stat-icon">🌍</div>
            <h3 class="stat-number">95%</h3>
            <p class="stat-label">Energy Saved (Aluminum)</p>
          </div>
          <div class="stat-card">
            <div class="stat-icon">🌳</div>
            <h3 class="stat-number">17</h3>
            <p class="stat-label">Trees Saved Per Ton</p>
          </div>
          <div class="stat-card">
            <div class="stat-icon">💧</div>
            <h3 class="stat-number">70%</h3>
            <p class="stat-label">Water Saved (Paper)</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Water Bottles Section -->
    <section class="item-section water-section">
      <div class="item-container">
        <div class="item-image-wrapper">
          <img src="../../images/waterBottle.png" alt="Water Bottle" class="item-image" />
        </div>
        <div class="item-content">
          <h2 class="item-title">
            <span class="title-number">1.</span>
            Plastic Water Bottles
          </h2>
          <div class="recycle-badge">RECYCLE</div>
          <ul class="item-steps">
            <li>Empty out all liquid</li>
            <li>Remove labels if possible (not required)</li>
            <li>Rinse the bottle with water</li>
            <li>Place in recycling bin with cap on</li>
          </ul>
          <div class="item-facts">
            <p><strong>Material:</strong> PET/PETE (Plastic #1)</p>
            <p><strong>Recycling Rate:</strong> ~29% in the US</p>
            <p><strong>Fact:</strong> One recycled bottle saves enough energy to power a laptop for 3 hours!</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Milk Cartons Section -->
    <section class="item-section milk-section">
      <div class="item-container reverse">
        <div class="item-content">
          <h2 class="item-title">
            <span class="title-number">2.</span>
            Milk Cartons
          </h2>
          <div class="recycle-badge">RECYCLE</div>
          <ul class="item-steps">
            <li>Empty out remaining milk</li>
            <li>Rinse thoroughly with water</li>
            <li>Flatten the carton to save space</li>
            <li>Place in recycling bin (caps can stay on)</li>
          </ul>
          <div class="item-facts">
            <p><strong>Material:</strong> Paperboard with plastic/foil lining</p>
            <p><strong>Recycling Rate:</strong> ~62% globally</p>
            <p><strong>Fact:</strong> Cartons are 70% paper from responsibly managed forests!</p>
          </div>
        </div>
        <div class="item-image-wrapper">
          <img src="../../images/milkCarton.png" alt="Milk Carton" class="item-image" />
        </div>
      </div>
      <img src="../../images/beerBottle.png" alt="Beer Bottle" class="floating-image bottle-2" />
    </section>

    <!-- Aluminum Cans Section -->
    <section class="item-section can-section">
      <div class="item-container">
        <div class="item-image-wrapper">
          <img src="../../images/can.png" alt="Aluminum Can" class="item-image" />
        </div>
        <div class="item-content">
          <h2 class="item-title">
            <span class="title-number">3.</span>
            Aluminum Cans
          </h2>
          <div class="recycle-badge">RECYCLE</div>
          <ul class="item-steps">
            <li>Empty the can completely</li>
            <li>Quick rinse to remove residue</li>
            <li>No need to remove tabs</li>
            <li>Place in recycling bin</li>
          </ul>
          <div class="item-facts">
            <p><strong>Material:</strong> 100% Aluminum</p>
            <p><strong>Recycling Rate:</strong> ~50% in the US, 75% in Europe</p>
            <p><strong>Fact:</strong> Aluminum can be recycled infinitely without losing quality!</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Glass Bottles Section -->
    <section class="item-section glass-section">
      <div class="item-container reverse">
        <div class="item-content">
          <h2 class="item-title">
            <span class="title-number">4.</span>
            Glass Bottles
          </h2>
          <div class="recycle-badge">RECYCLE</div>
          <ul class="item-steps">
            <li>Empty all contents</li>
            <li>Rinse out any residue</li>
            <li>Remove caps and corks</li>
            <li>Place in glass recycling bin</li>
          </ul>
          <div class="item-facts">
            <p><strong>Material:</strong> Soda-lime glass</p>
            <p><strong>Recycling Rate:</strong> ~33% in the US</p>
            <p><strong>Fact:</strong> Glass can be recycled endlessly and takes 1 million years to decompose!</p>
          </div>
        </div>
        <div class="item-image-wrapper">
          <img src="../../images/beerBottle.png" alt="Beer Bottle" class="item-image" />
        </div>
      </div>
    </section>

    <!-- Decycle Section -->
    <section class="decycle-section">
      <div class="decycle-container">
        <img src="../../images/milkCarton.png" alt="Milk Carton" class="floating-image milk-float" />
        <h2 class="section-title">Not Everything is Recyclable</h2>
        <p class="section-subtitle">Some items need special handling</p>
        
        <div class="decycle-grid">
          <div class="decycle-card">
            <h3>❌ Pizza Boxes</h3>
            <p>Grease contamination makes them non-recyclable</p>
          </div>
          <div class="decycle-card">
            <h3>❌ Plastic Bags</h3>
            <p>Take to store drop-off locations instead</p>
          </div>
          <div class="decycle-card">
            <h3>❌ Styrofoam</h3>
            <p>Check for special recycling programs in your area</p>
          </div>
          <div class="decycle-card">
            <h3>❌ Broken Glass</h3>
            <p>Wrap safely and dispose in trash</p>
          </div>
        </div>
      </div>
      <img src="../../images/waterBottle.png" alt="Water Bottle" class="floating-image bottle-3" />
    </section>

    <!-- Call to Action -->
    <section class="cta-section">
      <div class="cta-container">
        <h2>Ready to Make a Difference?</h2>
        <p>Start scanning and recycling today!</p>
        <button @click="goToHome" class="cta-button">Start Scanning</button>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  name: 'RecyclingInfo',
  methods: {
    goToHome() {
      this.$router.push('/');
    }
  }
};
</script>

<style src="./RecyclingInfo.css" scoped></style>
