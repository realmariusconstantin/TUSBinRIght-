<template>
  <section class="material-page can-page">
    <div class="material-header">
      <div class="material-icon">🥫</div>
      <h1>Metal & Can Recycling</h1>
      <p class="material-subtitle">Aluminum · Steel · Tin</p>
    </div>

    <div class="material-card">
      <div class="info-section">
        <h2>What Can Be Recycled?</h2>
        <ul class="recycle-list">
          <li>✓ Aluminum soda and beer cans</li>
          <li>✓ Steel food cans (soup, vegetables, beans)</li>
          <li>✓ Tin cans and lids</li>
          <li>✓ Aluminum foil and trays</li>
          <li>✓ Metal bottle caps</li>
        </ul>
      </div>

      <div class="info-section">
        <h2>How to Prepare</h2>
        <ol class="steps-list">
          <li>Rinse cans to remove food and liquid</li>
          <li>Remove paper labels (optional)</li>
          <li>Crush cans to save space</li>
          <li>Keep lids attached or place inside</li>
          <li>Place in your recycling bin</li>
        </ol>
      </div>

      <div class="info-section tips">
        <h2>💡 Tips</h2>
        <p>Aluminum cans are 100% recyclable and can be recycled infinitely. A recycled can is back on the shelf in as little as 60 days!</p>
      </div>

      <div class="info-section impact">
        <h2>🌍 Environmental Impact</h2>
        <p>Recycling aluminum saves 95% of the energy needed to make new aluminum from raw materials. One recycled can saves enough energy to run a laptop for 3 hours!</p>
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
  name: 'CanPage',
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
