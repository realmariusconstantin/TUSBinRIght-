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
          <div class="stat-card" v-for="(stat, i) in stats" :key="i">
            <div class="stat-icon">{{ stat.icon }}</div>
            <h3 class="stat-number">{{ stat.number }}</h3>
            <p class="stat-label">{{ stat.label }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Item Sections -->
    <section
      v-for="(item, i) in items"
      :key="i"
      :class="['item-section', { reverse: i % 2 === 1 }]"
    >
      <div class="item-container" :class="{ reverse: i % 2 === 1 }">
        <div class="item-image-wrapper">
          <img :src="item.image" :alt="item.title" class="item-image" />
        </div>
        <div class="item-content">
          <h2 class="item-title">
            <span class="title-number">{{ i + 1 }}.</span>
            {{ item.title }}
          </h2>
          <div class="recycle-badge">RECYCLE</div>
          <ul class="item-steps">
            <li v-for="(step, j) in item.steps" :key="j">{{ step }}</li>
          </ul>
          <div class="item-facts">
            <p><strong>Material:</strong> {{ item.material }}</p>
            <p><strong>Recycling Rate:</strong> {{ item.rate }}</p>
            <p><strong>Fact:</strong> {{ item.fact }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Non-Recyclables Section -->
    <section class="decycle-section">
      <div class="decycle-container">
        <h2 class="section-title">Not Everything is Recyclable</h2>
        <p class="section-subtitle">Some items need special handling</p>
        <div class="decycle-grid">
          <div class="decycle-card" v-for="(item, i) in nonRecyclables" :key="i">
            <h3>{{ item.icon }} {{ item.title }}</h3>
            <p>{{ item.desc }}</p>
          </div>
        </div>
      </div>
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
  name: "RecyclingInfo",
  data() {
    return {
      stats: [
        { icon: "♻️", number: "75%", label: "Waste Can Be Recycled" },
        { icon: "🌍", number: "95%", label: "Energy Saved (Aluminum)" },
        { icon: "🌳", number: "17", label: "Trees Saved Per Ton" },
        { icon: "💧", number: "70%", label: "Water Saved (Paper)" },
      ],
      items: [
        {
          title: "Plastic Water Bottles",
          image: "../../images/waterBottle.png",
          steps: [
            "Empty out all liquid",
            "Rinse the bottle",
            "Remove label (optional)",
            "Recycle with cap on",
          ],
          material: "PET/PETE (Plastic #1)",
          rate: "~29% in the US",
          fact: "One recycled bottle powers a laptop for 3 hours!",
        },
        {
          title: "Milk Cartons",
          image: "../../images/milkCarton.png",
          steps: [
            "Empty remaining milk",
            "Rinse thoroughly",
            "Flatten to save space",
            "Recycle (caps can stay)",
          ],
          material: "Paperboard with plastic/foil lining",
          rate: "~62% globally",
          fact: "Cartons are 70% paper from responsibly managed forests!",
        },
        {
          title: "Aluminum Cans",
          image: "../../images/can.png",
          steps: [
            "Empty completely",
            "Quick rinse",
            "No need to remove tabs",
            "Recycle directly",
          ],
          material: "100% Aluminum",
          rate: "~50% US / 75% Europe",
          fact: "Aluminum can be recycled infinitely without quality loss!",
        },
        {
          title: "Glass Bottles",
          image: "../../images/beerBottle.png",
          steps: [
            "Empty contents",
            "Rinse residue",
            "Remove caps/corks",
            "Recycle in glass bin",
          ],
          material: "Soda-lime glass",
          rate: "~33% in the US",
          fact: "Glass can be recycled endlessly and takes 1M years to decompose!",
        },
      ],
      nonRecyclables: [
        { icon: "❌", title: "Pizza Boxes", desc: "Grease makes them non-recyclable" },
        { icon: "❌", title: "Plastic Bags", desc: "Take to store drop-off locations" },
        { icon: "❌", title: "Styrofoam", desc: "Needs special recycling programs" },
        { icon: "❌", title: "Broken Glass", desc: "Wrap safely and dispose in trash" },
      ],
    };
  },
  methods: {
    goToHome() {
      this.$router.push("/");
    },
  },
};
</script>

<style src="./RecyclingInfo.css" scoped></style>
