<template>
  <div class="player-nicks-container">
    <hr class="divider"/>
    <div class="player-nicks">
      <div v-for="(nick, index) in playerNicks" :key="index" class="nick-item">
        <span class="nick-text">{{ nick.nick }}</span>
      </div>
    </div>
    <hr class="divider"/>
  </div>
</template>

<script setup>
const props = defineProps(['playerNicks'])
</script>

<style scoped>
.player-nicks-container {
  margin: 1rem 0;
}

.divider {
  border: none;
  border-top: 1px solid rgba(148, 163, 184, 0.2);
  margin: 0.75rem 0;
}

.player-nicks {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  padding: 0.5rem 0;
}

.nick-item {
  background: rgba(100, 116, 139, 0.1);
  padding: 0.35rem 0.75rem;
  border-radius: 5px;
}

.nick-text {
  font-size: 0.85rem;
  font-style: italic;
  color: var(--text-secondary);
}
</style>
