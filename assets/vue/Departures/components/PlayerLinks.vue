<template>
  <div class="player-links">
    <a
      :href="`https://lab-vole.cz/Player?name=${player.nickname}`"
      target="_blank"
      rel="noopener noreferrer"
      class="link"
    >LAB-VOLE</a>
    <a
      :href="`https://wot-life.com/eu/player/${player.nickname}`"
      target="_blank"
      rel="noopener noreferrer"
      class="link"
    >WOT-LIFE</a>
    <a
      :href="`https://www.tomato.gg/stats/EU/${player.nickname}=${player.account_id}`"
      target="_blank"
      rel="noopener noreferrer"
      class="link"
    >TOMATO.GG</a>
  </div>
</template>

<script setup>
defineProps({
  player: { type: Object, required: true }
})
</script>

<style scoped>
.player-links {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-around;
  gap: 0.75rem;
  margin-top: 2rem;
  padding-top: 1.25rem;
  border-top: 1px solid rgba(255, 255, 255, 0.08);
}

.link {
  font-weight: 800;
  font-size: 1.05rem;
  color: var(--primary-color);
  text-decoration: none;
  letter-spacing: 0.06em;
  padding: 0.55rem 1.25rem;
  border-radius: 8px;
  background: rgba(37, 99, 235, 0.1);
  border: 1px solid rgba(37, 99, 235, 0.25);
  transition: background 0.2s ease, border-color 0.2s ease, color 0.2s ease;
}

.link:hover {
  background: rgba(37, 99, 235, 0.22);
  border-color: rgba(37, 99, 235, 0.5);
  color: #93c5fd;
}
</style>
