<template>
  <Transition>
    <div v-if="clanData.name" class="current-clan">
      Spieler ist derzeit im Clan: {{ clanData.name }} ({{ clanData.abbreviation }})
    </div>
  </Transition>
</template>

<script setup>
import {onMounted, ref} from "vue";

const props = defineProps(['accountId'])
const clanId = ref(null)
const clanData = ref({})

onMounted(async () => {
  getPlayerClanId()
      .then(response => {
        return response.json()
      })
      .then(data => {
        clanId.value = data.data[props.accountId].clan_id;
        if (clanId.value) {
          getClanDetails()
              .then(resp => {
                return resp.json()
              })
              .then(data => {
                clanData.value = data
              })
        }
      })
})

async function getClanDetails() {
  return await fetch('https://api2.fpcstat.cz/api/clans/' + clanId.value)
}

async function getPlayerClanId() {
  const WotApi = 'https://api.worldoftanks.eu/wot/account/info/?';

  const params = new URLSearchParams();
  params.append('application_id', '883ff6ceefb13177357ffea34d6fb06f');
  params.append('fields', 'clan_id');
  params.append('account_id', props.accountId);

  return await fetch(WotApi + params);
}
</script>

<style scoped>
.current-clan {
  width: 100%;
  padding: 0.75rem;
  text-align: center;
  background: rgba(239, 68, 68, 0.2);
  border: 2px solid rgba(239, 68, 68, 0.5);
  border-radius: 5px;
  color: var(--text-primary);
  font-weight: 600;
  margin: 1rem 0;
  transition: opacity 0.5s ease;
}

.v-enter-active,
.v-leave-active {
  transition: opacity 0.5s ease;
}

.v-enter-from,
.v-leave-to {
  opacity: 0;
}
</style>
