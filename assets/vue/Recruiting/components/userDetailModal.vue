<template>
  <div class="modal-backdrop" @click.self="emit('close-user-detail')">
    <div class="modal-container">
      <div class="modal-content">
        <div class="modal-header" :style="backgroundColor(playerStats.wn8 || 0)">
          <div class="header-title">{{ user.name }}</div>
          <div class="last-battle" v-if="lastBattleTime !== 0">
            <span>Letzte Schlacht: </span>
            <span class="last-battle-value">{{ lastBattleTime }}</span>
          </div>
          <div v-if="lastBattleTime === null">
            <span>Dieser Spieler existiert nicht</span>
          </div>
        </div>

        <player-current-clan :accountId="user.account_id"/>

        <table class="stats-table" v-if="playerStats.battles">
          <thead>
          <tr>
            <th>Gefechte</th>
            <th>Siegrate</th>
            <th>Aufgeklärt</th>
            <th>Schaden</th>
            <th>Verteidigt</th>
            <th>Abschüsse</th>
          </tr>
          </thead>
          <tbody>
          <tr>
            <td>{{ playerStats.battles }}</td>
            <td>{{ winrate(playerStats.battles, playerStats.wins) }}%</td>
            <td>{{ avgValue(playerStats.battles, playerStats.spotted) }}</td>
            <td>{{ avgValue(playerStats.battles, playerStats.damage_dealt) }}</td>
            <td>{{ avgValue(playerStats.battles, playerStats.dropped_capture_points) }}</td>
            <td>{{ avgValue(playerStats.battles, playerStats.frags) }}</td>
          </tr>
          </tbody>
        </table>

        <div v-if="playerClans.length > 0" class="clans-section">
          <hr class="divider"/>
          <div class="clans-grid">
            <div v-for="(clan, index) in playerClans" :key="index" class="clan-item">
              <img :src="clan.emblem_small" :alt="clan.name" :title="clan.name" width="36" height="36">
              <span class="clan-abbr">{{ clan.abbreviation }}</span>
            </div>
          </div>
        </div>

        <player-nicks :playerNicks="playerNicks" v-if="playerNicks.length > 0"/>

        <player-links :player="user"/>

        <div class="modal-footer">
          <button @click="emit('close-user-detail')" class="close-button">
            Fenster schließen
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import {onMounted, ref, defineAsyncComponent} from "vue";
import axios from "axios";

const PlayerLinks = defineAsyncComponent(() => import('./playerLinks.vue'))
const PlayerNicks = defineAsyncComponent(() => import('./playerNicks.vue'))
const PlayerCurrentClan = defineAsyncComponent(() => import('./playerCurrentClan.vue'))

const emit = defineEmits(['close-user-detail'])
const props = defineProps({
  user: {
    type: Object,
    required: true
  }
})

const playerClans = ref([])
const lastBattleTime = ref(0)
const playerNicks = ref([])
const playerStats = ref({})

function winrate(battles, wins) {
  return ((wins / battles) * 100).toFixed(2)
}

function avgValue(battle, value) {
  return (value / battle).toFixed(2)
}

function backgroundColor(wn8) {
  return 'background-color: ' + wn8color(wn8)
}

function wn8color(wn8) {
  let color;
  if (wn8 < 450) {
    color = 'grey';
  } else if (wn8 >= 450 && wn8 < 650) {
    color = '#e50000';
  } else if (wn8 >= 650 && wn8 < 850) {
    color = '#cd3333';
  } else if (wn8 >= 850 && wn8 < 1050) {
    color = '#d77900';
  } else if (wn8 >= 1050 && wn8 < 1250) {
    color = '#d7B600';
  } else if (wn8 >= 1250 && wn8 < 1400) {
    color = '#6d9251';
  } else if (wn8 >= 1400 && wn8 < 1600) {
    color = '#4c762e';
  } else if (wn8 >= 1600 && wn8 < 2000) {
    color = '#46a892';
  } else if (wn8 >= 2000 && wn8 < 2450) {
    color = '#4a92b7';
  } else if (wn8 >= 2450 && wn8 < 2850) {
    color = '#83579d';
  } else if (wn8 >= 2850) {
    color = '#5a3175';
  }
  return color;
}

async function getPlayerStats() {
  const WotApi = 'https://api.worldoftanks.eu/wot/account/info/?';
  const params = new URLSearchParams();
  params.append('application_id', '883ff6ceefb13177357ffea34d6fb06f');
  params.append('fields', 'statistics.all,last_battle_time');
  params.append('account_id', props.user.account_id);

  return await fetch(WotApi + params);
}

function getDateFromTimestamp(timestamp) {
  return new Date(timestamp * 1000).toLocaleDateString('de-DE', {
    weekday: 'long',
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  });
}

onMounted(async () => {
  try {
    const response = await axios.get(
        `https://api2.fpcstat.cz/api/player_clans/${props.user.account_id}`
    );
    playerClans.value = response.data['hydra:member'] || [];
  } catch (error) {
    console.error('Error fetching player clans:', error);
  }

  try {
    const response = await axios.get(
        `https://api2.fpcstat.cz/api/player_nicks/${props.user.account_id}`
    );
    playerNicks.value = response.data['hydra:member'] || [];
  } catch (error) {
    console.error('Error fetching player nicks:', error);
  }

  try {
    getPlayerStats()
        .then(response => response.json())
        .then(data => {
          if (data.status === 'ok') {
            const accountData = Object.values(data.data)[0];
            if (accountData) {
              if (accountData.statistics && accountData.statistics.all) {
                playerStats.value = accountData.statistics.all;
              }
              if (accountData.last_battle_time === null) {
                lastBattleTime.value = 'Neexistuje'
              } else {
                lastBattleTime.value = getDateFromTimestamp(accountData.last_battle_time);
              }
            }
          }
        })
  } catch (error) {
    console.error('Error fetching player stats:', error);
  }
});
</script>

<style scoped>
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  backdrop-filter: blur(4px);
}

.modal-container {
  width: 90%;
  max-width: 800px;
  max-height: 90vh;
  overflow-y: auto;
  padding: 1rem;
}

.modal-content {
  background: #2d3748;
  border-radius: 10px;
  padding: 1.5rem;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
}

.modal-header {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  align-items: baseline;
  padding: 1rem 1.5rem;
  border-radius: 5px;
  margin-bottom: 1rem;
  color: white;
}

.header-title {
  font-size: 1.5rem;
  font-weight: 900;
}

.last-battle {
  font-size: 0.9rem;
}

.last-battle-value {
  font-weight: 700;
}

.stats-table {
  width: 100%;
  margin: 1rem 0;
  border-collapse: collapse;
}

.stats-table thead {
  background: linear-gradient(135deg, rgba(100, 116, 139, 0.3), rgba(71, 85, 105, 0.3));
}

.stats-table th {
  padding: 0.75rem;
  text-align: center;
  font-size: 0.9rem;
  font-style: italic;
  color: #f8fafc;
  border-right: 1px solid rgba(148, 163, 184, 0.2);
}

.stats-table th:last-child {
  border-right: none;
}

.stats-table td {
  padding: 0.75rem;
  text-align: center;
  font-weight: 700;
  color: #f8fafc;
  border-right: 1px solid rgba(148, 163, 184, 0.1);
}

.stats-table td:first-child {
  border-left: 1px solid rgba(148, 163, 184, 0.1);
}

.stats-table td:last-child {
  border-right: none;
}

.divider {
  border: none;
  border-top: 1px solid rgba(148, 163, 184, 0.2);
  margin: 1rem 0;
}

.clans-section {
  margin: 1rem 0;
}

.clans-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  margin: 1rem 0;
}

.clan-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.25rem;
}

.clan-abbr {
  font-size: 0.8rem;
  font-style: italic;
  color: #cbd5e1;
}

.modal-footer {
  display: flex;
  justify-content: center;
  margin-top: 1.5rem;
}

.close-button {
  background: linear-gradient(135deg, #64748b, #475569);
  color: #f8fafc;
  border: none;
  border-radius: 5px;
  padding: 0.75rem 2rem;
  font-weight: 700;
  font-size: 0.95rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.close-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 16px rgba(100, 116, 139, 0.3);
}

@media (max-width: 768px) {
  .modal-container {
    width: 95%;
  }

  .modal-content {
    padding: 1rem;
  }

  .header-title {
    font-size: 1.25rem;
  }

  .stats-table th,
  .stats-table td {
    padding: 0.5rem;
    font-size: 0.8rem;
  }
}
</style>
