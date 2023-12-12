<template>
  <div class="w-full p-2 rounded border">
   <div class="w-full bg-white p-4 rounded">
     <div
         class="text-2xl font-black px-4 py-2 rounded text-white"
         :style="backgroundColor(playerDetail.wn8)"
     >{{ playerDetail.nickname }}</div>
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
   </div>
  </div>
</template>

<script setup>
import {onMounted, ref} from "vue";
import axios from "axios";
import { departuresStore } from "../store";
import roles from "../../mixins/roles";
import {wn8color} from "wn8-color";

const store = departuresStore()

const props = defineProps(['playerDetail'])
const playerClans = ref([])
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
});

</script>
