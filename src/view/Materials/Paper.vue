<template>
    <section class="material-page paper-page">
        <div class="material-header">
            <div class="material-icon">📦</div>
            <h1>Paper & Carton Recycling</h1>
            <p class="material-subtitle">Cardboard · Paper · Cartons</p>
        </div>

        <div class="material-card">
            <div class="info-section">
                <h2>Select Your Location</h2>
                <div class="buttons-container">
                <button
                    v-for="loc in locations"
                    :key="loc.id"
                    class="btn btn-secondary"
                    :class="{ active: selectedLocation === loc.id }"
                    @click="fetchRulesForLocation(loc.id)"
                >
                    {{ loc.location }}
                </button>
                </div>
            </div>

            <div
            v-if="selectedLocation && !isLoadingRules"
            class="info-section impact"
            >
            <template v-if="rules.length > 0">
                <h2>Disposal Rules for {{ selectedLocationName }}</h2>
                <ul class="rules-list">
                <li v-for="rule in rules" :key="rule.id">
                    🗑️ <strong>{{ rule.bin_type }}</strong> — {{ rule.description }}
                </li>
                </ul>
            </template>

            <template v-else>
                <h2>No Specific Rules for {{ selectedLocationName }}</h2>
                <p>
                No location-based disposal instructions were found for this item.
                Please follow the general recycling guidelines below.
                </p>
            </template>
            </div>

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
import api from '@/lib/api';

export default {
  name: 'PaperPage',
  data() {
    return {
      locations: [],
      selectedLocation: null,
      selectedLocationName: '',
      rules: [],
      itemTypeId: 4,
      isLoadingRules: false 
    };
  },
  methods: {
    async fetchLocations() {
      try {
        const res = await api.get('/admin/locations');
        this.locations = res.data.locations || [];
      } catch (err) {
        console.error('Error fetching locations:', err);
      }
    },

    async fetchRulesForLocation(locationId) {
      try {
        this.selectedLocation = locationId;
        const location = this.locations.find((l) => l.id === locationId);
        this.selectedLocationName = location ? location.location : 'Unknown';
        this.isLoadingRules = true;
        
        const res = await api.get('/disposal-rules-location', {
          params: {
            item_type_id: this.itemTypeId,
            location_id: locationId
          }
        });

        this.rules = res.data.rules || [];
      } catch (err) {
        console.error('Error fetching disposal rules:', err);
        this.rules = [];
      } finally {
        this.isLoadingRules = false;
      }
    },

    scanMore() {
      this.$router.push('/');
    },
    goBack() {
      this.$router.back();
    }
  },
  mounted() {
    this.fetchLocations();
  }
};
</script>

<style src="./Material.css" scoped></style>