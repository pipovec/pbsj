<template>
  <div>
    <table class="recruiting-table">
      <thead>
        <tr>
          <th v-for="header in headName" :key="header">{{ header }}</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="player in store.players['member']"
          :key="player.account_id"
          class="player-row"
          @click="openPlayerModal(player)"
        >
          <td class="td-nickname">{{ player.nickname }}</td>
          <td>{{ player.battles_all || '-' }}</td>
          <td>{{ player.winrate ? parseFloat(player.winrate).toFixed(2) + '%' : '-' }}</td>
          <td :style="{ color: wn8color(player.wn8), fontWeight: 700 }">{{ parseFloat(player.wn8).toFixed(0) }}</td>
          <td>{{ player.global_rating }}</td>
          <td>{{ player.avg_xp || '-' }}</td>
          <td>{{ player.tier_10 }}</td>
          <td>{{ player.tier_8 }}</td>
          <td>{{ player.tier_6 }}</td>
          <td>{{ player.battles_7_days || '-' }}</td>
          <td>{{ player.battles_14_days || '-' }}</td>
          <td>{{ player.battles_30_days || '-' }}</td>
          <td v-html="isTrue(player.combat_officer)"></td>
          <td v-html="isTrue(player.personnel_officer)"></td>
          <td v-html="isTrue(player.tank_1)"></td>
          <td v-html="isTrue(player.tank_2)"></td>
          <td v-html="isTrue(player.tank_3)"></td>
        </tr>
      </tbody>
    </table>
  </div>

  <DepartureModal
    v-if="selectedPlayer"
    v-model="showModal"
    :player="selectedPlayer"
  />
</template>

<script setup>
import { recruitingStore } from "../store";
import { ref, defineAsyncComponent } from "vue";

const DepartureModal = defineAsyncComponent(() => import('../../Departures/components/DepartureModal.vue'))

const store = recruitingStore();
const showModal = ref(false);
const selectedPlayer = ref(null);

const headName = [
  'Nickname', 'Battles', 'Winrate', 'WN8', 'Global Rating',
  'Avg XP', 'Tier X', 'Tier 8', 'Tier 6',
  'Batt. 7d', 'Batt. 14d', 'Batt. 30d',
  'Combat Off.', 'Personnel Off.', 'Obj.279r', 'Obj.907', 'Chieftain'
]

function openPlayerModal(player) {
  selectedPlayer.value = {
    account_id: player.account_id,
    nickname: player.nickname,
    wn8: parseFloat(player.wn8) || 0,
    emblems_large: null,
    abbreviation: null,
    name: null,
  }
  showModal.value = true
}

function isTrue(value) {
  return value
    ? '<img src="/success-green-check-mark-icon.svg" width="16" alt="success" class="tank"/>'
    : '<img src="/checkbox-cross-red-icon.svg" width="16" alt="cross mark" class="tank"/>';
}

function wn8color(wn8) {
  const v = parseFloat(wn8)
  if (v < 450) return 'grey'
  if (v < 650) return '#e50000'
  if (v < 850) return '#cd3333'
  if (v < 1050) return '#d77900'
  if (v < 1250) return '#d7b600'
  if (v < 1400) return '#6d9251'
  if (v < 1600) return '#4c762e'
  if (v < 2000) return '#46a892'
  if (v < 2450) return '#4a92b7'
  if (v < 2850) return '#83579d'
  return '#5a3175'
}
</script>

<style>
.player-row {
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.player-row:hover {
  background-color: rgba(100, 116, 139, 0.1);
}

.td-nickname {
  font-weight: 600;
}
</style>
