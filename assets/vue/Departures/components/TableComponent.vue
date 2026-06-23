<template>
  <div class="dep-grid">
    <div
      v-for="(player, index) in store.players['member']"
      :key="index"
      class="dep-card"
      :style="wn8Style(player.wn8)"
      role="button"
      tabindex="0"
      :aria-label="`Spielerdetail ${player.nickname} öffnen`"
      data-umami-event="Click on player"
      @click="openPlayerDetail(player)"
      @keydown.enter.space.prevent="openPlayerDetail(player)"
    >
      <div class="dep-card-top">
        <div class="dep-emblem-wrap">
          <picture>
            <source :srcset="replaceFpcCdn(player.emblems_large)" type="image/webp">
            <img
              :src="replaceFpcCdn(player.emblems_large)"
              loading="lazy"
              :alt="player.name"
              :title="player.name"
              width="68" height="68"
              class="dep-emblem"
            />
          </picture>
          <div class="dep-clan-abbr">{{ player.abbreviation }}</div>
        </div>
        <div class="dep-nickname">{{ player.nickname }}</div>
      </div>
      <hr class="dep-divider" />
      <div class="dep-card-stats">
        <span>battles: <strong>{{ player.battles }}</strong></span>
        <span>wn8: <strong>{{ player.wn8.toFixed(0) }}</strong></span>
      </div>
    </div>
  </div>

  <DepartureModal
    v-if="selectedPlayer"
    v-model="showModal"
    :player="selectedPlayer"
  />
</template>

<script setup>
import { departuresStore } from "../store";
import { ref, defineAsyncComponent } from "vue";

const DepartureModal = defineAsyncComponent(() => import('./DepartureModal.vue'))

const store = departuresStore();
const showModal = ref(false)
const selectedPlayer = ref(null)
const cdnUrl = ref('https://cdn.fpcstat.cz')

function wn8Style(wn8) {
  return { 'background-color': wn8color(wn8) }
}

function replaceFpcCdn(original) {
  return original
    .replace('https://eu.wargaming.net/clans/media/clans/emblems', cdnUrl.value)
    .replace('.png', '.webp')
}

function openPlayerDetail(player) {
  if (!showModal.value) sendClick(player)
  selectedPlayer.value = player
  showModal.value = true
}

async function sendClick(player) {
  await fetch('https://api3.fpcstat.cz/api/site-click', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      accountId: player.account_id,
      domain: 'pbsj',
      site: 'departures',
      date: getTodayDate(),
      data: { nickname: player.nickname }
    })
  })
}

function getTodayDate() {
  const date = new Date();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return `${date.getFullYear()}-${month}-${day}`;
}

function wn8color(wn8) {
  if (wn8 < 450) return 'grey'
  if (wn8 < 650) return '#e50000'
  if (wn8 < 850) return '#cd3333'
  if (wn8 < 1050) return '#d77900'
  if (wn8 < 1250) return '#d7b600'
  if (wn8 < 1400) return '#6d9251'
  if (wn8 < 1600) return '#4c762e'
  if (wn8 < 2000) return '#46a892'
  if (wn8 < 2450) return '#4a92b7'
  if (wn8 < 2850) return '#83579d'
  return '#5a3175'
}
</script>

<style>
.dep-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 0.75rem;
}

.dep-card {
  border-radius: 10px;
  padding: 0.75rem;
  cursor: pointer;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  color: #fff;
}

.dep-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
}

.dep-card-top {
  display: grid;
  grid-template-columns: 72px 1fr;
  align-items: center;
  gap: 0.5rem;
}

.dep-emblem-wrap {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.2rem;
}

.dep-emblem {
  border-radius: 50%;
  display: block;
}

.dep-clan-abbr {
  font-size: 0.75rem;
  font-weight: 700;
  text-align: center;
}

.dep-nickname {
  font-size: 1.1rem;
  font-weight: 700;
  text-align: center;
  overflow-wrap: break-word;
  word-break: break-all;
  min-width: 0;
}

.dep-divider {
  border: none;
  border-top: 1px solid rgba(255, 255, 255, 0.25);
  margin: 0.5rem 0;
}

.dep-card-stats {
  display: flex;
  justify-content: space-between;
  font-size: 1.1rem;
  padding: 0 0.25rem;
}

@media (max-width: 640px) {
  .dep-grid {
    grid-template-columns: 1fr;
  }
}
</style>
