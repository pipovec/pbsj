<template>
  <div class="w-full p-2 rounded border">
   <div class="w-full bg-white p-4 rounded">
     <div
         class="flex flex-row flex-wrap items-baseline justify-between text-2xl font-black px-4 py-2 rounded text-white"
         :style="backgroundColor(playerDetail.wn8)"
     >
       <div>{{ playerDetail.nickname }}</div>
       <div class="text-xs">
         <span>letzte kampfzeit: </span> <span class="font-bold">{{lastBattleTime}}</span>
       </div>
     </div>
     <table class="table table-auto w-full mt-2">
       <tr class="text-xs italic">
         <th>battles</th>
         <th>winrate</th>
         <th>spotted</th>
         <th>damage</th>
         <th>defend</th>
         <th>kills</th>
       </tr>
     <tr class="text-center font-extrabold">
      <td>{{ playerDetail.battles }}</td>
      <td>{{ winrate(playerDetail.battles ,playerDetail.wins) }}</td>
      <td>{{ avgValue(playerDetail.battles, playerDetail.spotted) }}</td>
      <td>{{ avgValue(playerDetail.battles, playerDetail.damage_dealt) }}</td>
      <td>{{ avgValue(playerDetail.battles, playerDetail.dropped_capture_points) }}</td>
      <td>{{ avgValue(playerDetail.battles, playerDetail.frags) }}</td>
    </tr>
     </table>
     <div v-if="playerClans">
       <hr />
       <div class="flex flex-row flex-wrap mt-4 gap-4">
         <picture v-for="(clan, index) in playerClans" :key="index">
           <img :src="clan.emblem_small" :alt="clan.name" :title="clan.name" width="36" height="36">
           <span class="text-xs italic">{{ clan.abbreviation }}</span>
         </picture>
       </div>
     </div>
     <div v-if="playerNicks">
       <hr />
       <div class="flex flex-wrap flex-row mt-4 gap-2">
         <div v-for="(nick, index) in playerNicks" :key="index">
           <span class="text-xs italic">{{ nick.nick }}</span>
         </div>
       </div>
     </div>
     <div>
       <PlayerLinks :player="playerDetail" />
     </div>
   </div>
  </div>
</template>

<script setup>
import {onMounted, ref} from "vue";
import axios from "axios";
import { departuresStore } from "../store";
import PlayerLinks from "./playerLinks.vue";

const store = departuresStore()

const props = defineProps(['playerDetail'])
const playerClans = ref([])
const lastBattleTime = ref('')
const playerNicks = ref([])


function winrate(battles, wins) {
  return ((wins/battles) * 100).toFixed(2)
}

function avgValue(battle, value) {
  return (value/battle).toFixed(2)
}


function backgroundColor(wn8) {
  return'background-color: ' + wn8color(wn8)
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

async function getLastBattleTime() {
  const WotApi = 'https://api.worldoftanks.eu/wot/account/info/?';

  const params = new URLSearchParams();
  params.append('application_id', '883ff6ceefb13177357ffea34d6fb06f');
  params.append('fields', 'last_battle_time');
  params.append('account_id', props.playerDetail.account_id);

  return await fetch(WotApi+ params);
}

function getDataFromTimeStamp(timestamp) {
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
        `${store.baseUrl}/api/player_clans/${props.playerDetail.account_id}`
    );
    playerClans.value = response.data['hydra:member'];
  } catch (error) {
    console.error('Error fetching player clans:', error);
  }

  try {
    const response = await axios.get(
        `${store.baseUrl}/api/player_nicks/${props.playerDetail.account_id}`
    );
    playerNicks.value = response.data['hydra:member'];
  } catch (error) {
    console.error('Error fetching player clans:', error);
  }

  try {
    getLastBattleTime()
        .then(response => {
          return response.json();
        })
        .then(data => {
          if (data.status === 'ok') {
             lastBattleTime.value = getDataFromTimeStamp(Object.values(data.data)[0].last_battle_time);
          }
    })
  } catch (error) {
    console.error('Error fetching last battle time:', error);
  }

});

</script>
