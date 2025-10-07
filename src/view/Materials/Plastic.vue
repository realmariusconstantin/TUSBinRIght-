<template>
    <section class="material-page plastic-page">
        <div class="material-header">
            <div class="material-icon">♻️</div>
            <h1>Plastic Recycling</h1>
            <p class="material-subtitle">Types 1-7 · PET, HDPE, PVC, LDPE, PP, PS, Other</p>
        </div>

        <div class="material-card">
            <div class="info-section">
                <h2>What Can Be Recycled?</h2>
                <ul class="recycle-list">
                    <li>✓ Plastic bottles (water, soda, juice)</li>
                    <li>✓ Milk jugs and detergent containers</li>
                    <li>✓ Food containers and yogurt cups</li>
                    <li>✓ Plastic bags (at designated drop-off locations)</li>
                    <li>✓ Bottle caps and lids</li>
                </ul>
            </div>

            <div class="info-section">
                <h2>How to Prepare</h2>
                <ol class="steps-list">
                    <li>Rinse containers to remove food residue</li>
                    <li>Remove labels if easily detachable</li>
                    <li>Flatten bottles to save space</li>
                    <li>Keep caps on (they're recyclable too!)</li>
                    <li>Place in your recycling bin</li>
                </ol>
            </div>

            <div class="info-section tips">
                <h2>💡 Tips</h2>
                <p>Look for the recycling symbol with numbers 1-7. Types 1 (PET) and 2 (HDPE) are most commonly
                    recycled. Avoid black plastic as it's harder to sort.</p>
            </div>

            <div class="info-section impact">
                <h2>🌍 Environmental Impact</h2>
                <p>Recycling one ton of plastic saves approximately 5,774 kWh of energy and prevents 16.3 barrels of oil
                    from being used. Every bottle counts!</p>
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
    name: 'PlasticPage',
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
