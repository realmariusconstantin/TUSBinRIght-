<template>
  <section class="material-page paper-page">
    <div class="material-header">
      <div class="material-icon">📦</div>
      <h1>Paper & Carton Recycling</h1>
      <p class="material-subtitle">Cardboard · Paper · Cartons</p>
    </div>

    <div class="material-card">
      <div class="info-section">
        <h2>What Can Be Recycled?</h2>
        <ul class="recycle-list">
          <li>✓ Cardboard boxes and packaging</li>
          <li>✓ Newspapers and magazines</li>
          <li>✓ Office paper and envelopes</li>
          <li>✓ Milk and juice cartons</li>
          <li>✓ Paper bags and wrapping paper</li>
        </ul>
      </div>

      <div class="info-section">
        <h2>How to Prepare</h2>
        <ol class="steps-list">
          <li>Remove plastic windows from envelopes</li>
          <li>Flatten cardboard boxes</li>
          <li>Remove tape and staples if possible</li>
          <li>Keep paper dry and clean</li>
          <li>Place in your recycling bin</li>
        </ol>
      </div>

      <div class="info-section tips">
        <h2>💡 Tips</h2>
        <p>Avoid recycling greasy pizza boxes, paper towels, or tissues. Wet or soiled paper can contaminate the recycling stream. Shredded paper should go in a paper bag.</p>
      </div>

      <div class="info-section impact">
        <h2>🌍 Environmental Impact</h2>
        <p>Recycling one ton of paper saves 17 trees, 7,000 gallons of water, and 3 cubic yards of landfill space. It also saves enough energy to power an average home for 6 months!</p>
      </div>

      <div class="action-buttons">
        <button class="btn btn-secondary" @click="goBack">Back</button>
        <button class="btn btn-primary" @click="scanMore">Scan More</button>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'PaperPage',
  methods: {
    scanMore() {
      this.$router.push('/');
    },
    goBack() {
      this.$router.back();
    }
  }
};
</script>

<style src="./Material.css" scoped></style>
