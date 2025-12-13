<template>
  <div class="player-links">
    <a :href="'https://lab-vole.cz/Player?name='+player.name" target="_blank">LAB-VOLE</a>
    <a :href="'https://wot-life.com/eu/player/' + player.name" target="_blank">WOT-LIFE</a>
    <a :href="'https://www.tomato.gg/stats/EU/' + player.name+'='+player.account_id" target="_blank">TOMATO.GG</a>
    <a :href="'https://worldoftanks.eu/cs/community/accounts/' + player.account_id + '-' + player.name + '/'"
       target="_blank">World of Tanks</a>
  </div>
</template>

<script setup>
const props = defineProps(['player'])
</script>

<style scoped>
.player-links {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-around;
  gap: 1rem;
  padding: 1rem 0;
}

.player-links a {
  color: var(--primary-gray);
  text-decoration: none;
  font-weight: 600;
  font-size: 0.95rem;
  transition: all 0.2s ease;
  padding: 0.5rem 1rem;
  border-radius: 5px;
  background: rgba(100, 116, 139, 0.1);
}

.player-links a:hover {
  color: var(--text-primary);
  background: rgba(100, 116, 139, 0.2);
  transform: translateY(-2px);
}
</style>
