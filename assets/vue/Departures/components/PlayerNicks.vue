<template>
  <div v-if="playerNicks.length" class="nicks-section">
    <div class="section-header">
      <span class="section-icon">@</span>
      <span class="section-title">Frühere Nicks</span>
    </div>
    <div class="nicks-list">
      <span v-for="nick in playerNicks" :key="nick.nick" class="nick-tag">{{ nick.nick }}</span>
    </div>
  </div>
</template>

<script setup>
defineProps({
  playerNicks: { type: Array, default: () => [] }
})
</script>

<style scoped>
.nicks-section {
  padding: 0 1.25rem;
  margin-top: 1.25rem;
}

.section-header {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  margin-bottom: 0.6rem;
}

.section-icon {
  font-size: 0.75rem;
  font-weight: 700;
  color: #2563eb;
  opacity: 0.8;
  line-height: 1;
}

.section-title {
  font-size: 0.7rem;
  text-transform: uppercase;
  letter-spacing: 0.07em;
  color: #cbd5e1;
}

.nicks-list {
  display: flex;
  flex-wrap: wrap;
  gap: 0.4rem;
}

.nick-tag {
  font-family: 'Courier New', Courier, monospace;
  font-size: 0.8rem;
  color: #f8fafc;
  background: rgba(37, 99, 235, 0.12);
  border: 1px solid rgba(37, 99, 235, 0.25);
  border-left: 3px solid #2563eb;
  padding: 0.2rem 0.6rem 0.2rem 0.5rem;
  border-radius: 4px;
}
</style>
