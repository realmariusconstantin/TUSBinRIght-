<template>
  <section class="material-page glass-page">
    <div class="material-header">
      <div class="material-icon">🍾</div>
      <h1>Glass Recycling</h1>
      <p class="material-subtitle">Clear · Green · Brown Glass</p>
    </div>

    <div class="material-card">
      <div class="info-section">
        <h2>What Can Be Recycled?</h2>
        <ul class="recycle-list">
          <li>✓ Glass bottles (wine, beer, juice)</li>
          <li>✓ Glass jars (jam, pickles, sauces)</li>
          <li>✓ Clear, green, and brown glass containers</li>
          <li>✓ Glass food containers</li>
          <li>✓ Glass beverage bottles</li>
        </ul>
      </div>

      <div class="info-section">
        <h2>How to Prepare</h2>
        <ol class="steps-list">
          <li>Rinse bottles and jars thoroughly</li>
          <li>Remove metal lids and caps</li>
          <li>Remove corks and pourers</li>
          <li>No need to remove labels</li>
          <li>Place in your recycling bin (don't break!)</li>
        </ol>
      </div>

      <div class="info-section tips">
        <h2>💡 Tips</h2>
        <p>Glass can be recycled endlessly without loss of quality. Keep different colors together if your area separates them. Never recycle mirrors, light bulbs, or ceramics with glass.</p>
      </div>

      <div class="info-section impact">
        <h2>🌍 Environmental Impact</h2>
        <p>Recycling glass saves 30% of the energy required to make new glass. One recycled glass bottle saves enough energy to power a computer for 25 minutes!</p>
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
  name: 'GlassPage',
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
