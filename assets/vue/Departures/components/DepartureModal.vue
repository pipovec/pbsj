<template>
  <Teleport to="body">
    <Transition name="modal">
      <div
        v-if="modelValue"
        class="overlay"
        @click.self="$emit('update:modelValue', false)"
      >
        <div class="modal" role="dialog" aria-modal="true">

          <button class="close-btn" aria-label="Schließen" @click="$emit('update:modelValue', false)">✕</button>

          <!-- Hero -->
          <div class="modal-hero" :style="{ '--wn8-color': wn8Color(player.wn8) }">
            <div class="hero-emblem">
              <img
                v-if="emblemWebp"
                :src="emblemWebp"
                :alt="player.nickname"
                :title="player.nickname"
                width="72"
                height="72"
              />
              <div v-else class="emblem-placeholder" :style="{ background: wn8Color(player.wn8) }">
                {{ player.nickname[0].toUpperCase() }}
              </div>
            </div>
            <div class="hero-info">
              <div class="hero-nickname">{{ player.nickname }}</div>
              <div v-if="player.name" class="hero-clan">{{ player.name }} · {{ player.abbreviation }}</div>
              <div class="hero-meta">
                <span v-if="playerDeleted" class="badge badge-deleted">Spieler wurde gelöscht</span>
                <span v-else-if="lastBattleTime" class="badge badge-battle">⚔ {{ lastBattleTime }}</span>
                <span v-else class="badge badge-loading">Wird geladen…</span>
              </div>
            </div>
            <div class="hero-wn8" :style="{ background: wn8Color(player.wn8) }">
              <div class="wn8-value">{{ player.wn8.toFixed(0) }}</div>
              <div class="wn8-label">WN8</div>
            </div>
          </div>

          <PlayerCurrentClan v-if="currentClanId" :clanId="currentClanId" />

          <!-- Stats -->
          <div class="stats-grid">
            <div class="stat-card">
              <div class="stat-value">{{ wotStats ? wotStats.battles.toLocaleString() : '—' }}</div>
              <div class="stat-label">Gefechte</div>
            </div>
            <div class="stat-card">
              <div class="stat-value">{{ wotStats ? winrate(wotStats.battles, wotStats.wins) + '%' : '—' }}</div>
              <div class="stat-label">Siegrate</div>
            </div>
            <div class="stat-card">
              <div class="stat-value">{{ wotStats ? avg(wotStats.battles, wotStats.damage_dealt) : '—' }}</div>
              <div class="stat-label">Schaden</div>
            </div>
            <div class="stat-card">
              <div class="stat-value">{{ wotStats ? avg(wotStats.battles, wotStats.frags) : '—' }}</div>
              <div class="stat-label">Abschüsse</div>
            </div>
            <div class="stat-card">
              <div class="stat-value">{{ wotStats ? avg(wotStats.battles, wotStats.spotted) : '—' }}</div>
              <div class="stat-label">Aufgeklärt</div>
            </div>
            <div class="stat-card">
              <div class="stat-value">{{ wotStats ? avg(wotStats.battles, wotStats.dropped_capture_points) : '—' }}</div>
              <div class="stat-label">Verteidigt</div>
            </div>
          </div>

          <!-- Clan history -->
          <div v-if="playerClans.length" class="section">
            <div class="section-header">
              <span class="section-icon">⚑</span>
              <span class="section-label">Klan-Geschichte</span>
            </div>
            <div class="clans-scroll">
              <div v-for="clan in playerClans" :key="clan.abbreviation" class="clan-chip" :title="clan.name">
                <img :src="clan.emblem_small" :alt="clan.name" width="28" height="28" class="clan-emblem" />
                <span class="clan-abbr">{{ clan.abbreviation }}</span>
              </div>
            </div>
          </div>

          <PlayerNicks :playerNicks="playerNicks" />

          <div class="section">
            <PlayerLinks :player="player" />
          </div>

        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch, defineAsyncComponent } from 'vue'

const PlayerCurrentClan = defineAsyncComponent(() => import('./PlayerCurrentClan.vue'))
const PlayerNicks = defineAsyncComponent(() => import('./PlayerNicks.vue'))
const PlayerLinks = defineAsyncComponent(() => import('./PlayerLinks.vue'))

const CDN_URL = 'https://cdn.fpcstat.cz'
const API2_URL = 'https://api2.fpcstat.cz'
const WOT_APP_ID = '883ff6ceefb13177357ffea34d6fb06f'

const props = defineProps({
  modelValue: { type: Boolean, required: true },
  player: { type: Object, required: true }
})

defineEmits(['update:modelValue'])

const playerClans = ref([])
const playerNicks = ref([])
const lastBattleTime = ref('')
const playerDeleted = ref(false)
const currentClanId = ref(null)
const wotStats = ref(null)

const emblemWebp = computed(() => {
  if (!props.player.emblems_large) return null
  return props.player.emblems_large
    .replace('https://eu.wargaming.net/clans/media/clans/emblems', CDN_URL)
    .replace('.png', '.webp')
})

function wn8Color(wn8) {
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

function winrate(battles, wins) {
  return ((wins / battles) * 100).toFixed(2)
}

function avg(battles, value) {
  return (value / battles).toFixed(2)
}

function formatTimestamp(timestamp) {
  return new Date(timestamp * 1000).toLocaleDateString('de-DE', {
    weekday: 'long',
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  })
}

watch(() => props.modelValue, async (open) => {
  if (!open) return

  playerClans.value = []
  playerNicks.value = []
  lastBattleTime.value = ''
  playerDeleted.value = false
  currentClanId.value = null
  wotStats.value = null

  const accountId = props.player.account_id
  const jsonHeaders = { Accept: 'application/json' }

  const [clansResult, nicksResult, wotResult] = await Promise.allSettled([
    fetch(`${API2_URL}/api/player_clans/${accountId}`, { headers: jsonHeaders }).then(r => r.json()),
    fetch(`${API2_URL}/api/player_nicks/${accountId}`, { headers: jsonHeaders }).then(r => r.json()),
    fetch(`https://api.worldoftanks.eu/wot/account/info/?application_id=${WOT_APP_ID}&fields=last_battle_time,clan_id,statistics.all&account_id=${accountId}`).then(r => r.json()),
  ])

  if (clansResult.status === 'fulfilled') {
    playerClans.value = Array.isArray(clansResult.value) ? clansResult.value : []
  }
  if (nicksResult.status === 'fulfilled') {
    playerNicks.value = Array.isArray(nicksResult.value) ? nicksResult.value : []
  }
  if (wotResult.status === 'fulfilled' && wotResult.value.status === 'ok') {
    const playerData = Object.values(wotResult.value.data)[0]
    if (playerData === null) {
      playerDeleted.value = true
    } else {
      if (playerData.last_battle_time) lastBattleTime.value = formatTimestamp(playerData.last_battle_time)
      if (playerData.clan_id) currentClanId.value = playerData.clan_id
      if (playerData.statistics?.all) wotStats.value = playerData.statistics.all
    }
  }
}, { immediate: true })
</script>

<style scoped>
.overlay {
  --card-bg: #1e293b;
  --text-primary: #f8fafc;
  --text-secondary: #cbd5e1;
  --primary-color: #2563eb;

  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.75);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 100;
  padding: 1rem;
  backdrop-filter: blur(4px);
}

.modal {
  position: relative;
  width: 100%;
  max-width: 620px;
  max-height: 90vh;
  overflow-y: auto;
  background: var(--card-bg);
  border-radius: 20px;
  border: 1px solid rgba(255, 255, 255, 0.08);
  box-shadow: 0 32px 80px rgba(0, 0, 0, 0.7);
  padding: 0 0 1.25rem;
}

.modal::-webkit-scrollbar { width: 4px; }
.modal::-webkit-scrollbar-track { background: transparent; }
.modal::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 2px; }

.close-btn {
  position: absolute;
  top: 0.85rem;
  right: 0.85rem;
  width: 32px;
  height: 32px;
  border-radius: 8px;
  border: none;
  background: rgba(0,0,0,0.3);
  color: rgba(255,255,255,0.7);
  font-size: 0.85rem;
  cursor: pointer;
  z-index: 1;
  transition: background 0.2s ease, color 0.2s ease;
}
.close-btn:hover {
  background: rgba(0,0,0,0.5);
  color: #fff;
}

.modal-hero {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.5rem 3.75rem 1.25rem 1.25rem;
  background: linear-gradient(135deg, rgba(255,255,255,0.04) 0%, transparent 100%);
  border-bottom: 1px solid rgba(255,255,255,0.07);
}

.hero-emblem img,
.emblem-placeholder {
  width: 72px;
  height: 72px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  font-size: 1.8rem;
  font-weight: 900;
  color: #fff;
}

.hero-info {
  flex: 1;
  min-width: 0;
}

.hero-nickname {
  font-size: 1.4rem;
  font-weight: 900;
  color: var(--text-primary);
  overflow-wrap: break-word;
  word-break: break-all;
  line-height: 1.2;
}

.hero-clan {
  font-size: 0.85rem;
  color: var(--text-secondary);
  margin-top: 0.2rem;
}

.hero-meta {
  margin-top: 0.5rem;
}

.badge {
  display: inline-block;
  font-size: 0.78rem;
  font-weight: 600;
  padding: 0.2rem 0.6rem;
  border-radius: 20px;
}

.badge-battle {
  background: rgba(255,255,255,0.08);
  color: var(--text-secondary);
}

.badge-deleted {
  background: rgba(239, 68, 68, 0.2);
  color: #fca5a5;
  border: 1px solid rgba(239, 68, 68, 0.4);
}

.badge-loading {
  background: rgba(255,255,255,0.05);
  color: var(--text-secondary);
  font-style: italic;
}

.hero-wn8 {
  flex-shrink: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  width: 64px;
  height: 64px;
  border-radius: 14px;
  color: #fff;
}

.wn8-value {
  font-size: 1.05rem;
  font-weight: 900;
  line-height: 1;
}

.wn8-label {
  font-size: 0.65rem;
  font-weight: 600;
  opacity: 0.8;
  margin-top: 0.2rem;
  letter-spacing: 0.05em;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1px;
  background: rgba(255,255,255,0.06);
  margin: 1.25rem 1.25rem 0;
  border-radius: 12px;
  overflow: hidden;
}

.stat-card {
  background: var(--card-bg);
  padding: 0.75rem 0.5rem;
  text-align: center;
}

.stat-value {
  font-size: 1.15rem;
  font-weight: 800;
  color: var(--text-primary);
  line-height: 1;
}

.stat-label {
  font-size: 0.7rem;
  color: var(--text-secondary);
  margin-top: 0.3rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.section {
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
  color: var(--primary-color);
  opacity: 0.8;
  line-height: 1;
}

.section-label {
  font-size: 0.7rem;
  text-transform: uppercase;
  letter-spacing: 0.07em;
  color: var(--text-secondary);
}

.clans-scroll {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
  gap: 0.5rem;
}

.clan-chip {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.45rem;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 8px;
  padding: 0.4rem 0.5rem;
  transition: background 0.2s ease, border-color 0.2s ease;
  cursor: default;
}

.clan-chip:hover {
  background: rgba(255, 255, 255, 0.09);
  border-color: rgba(255, 255, 255, 0.2);
}

.clan-emblem {
  border-radius: 4px;
  display: block;
  flex-shrink: 0;
}

.clan-abbr {
  font-size: 0.82rem;
  font-weight: 700;
  color: var(--text-primary);
  letter-spacing: 0.03em;
}

.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.25s ease;
}

.modal-enter-active .modal,
.modal-leave-active .modal {
  transition: transform 0.25s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-from .modal,
.modal-leave-to .modal {
  transform: scale(0.94) translateY(12px);
}

@media (max-width: 480px) {
  .modal-hero {
    flex-wrap: wrap;
  }

  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
</style>
