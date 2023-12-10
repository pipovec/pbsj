<template>
  <div class="bg-gray-600 static grid text-white py-3 px-2 w-full">
    <div class="lg:w-1/2 w-full lg:mx-auto grid grid-cols-5 justify-center">
      <div class="grid">
        <button
            v-if="store.players['hydra:view']['hydra:first']"
            @click="store.getPlayersByUrl(store.players['hydra:view']['hydra:first'])"
            type="button"
            :class="buttonClass"
        >
          First
        </button>
      </div>
      <div class="grid">
        <button
            v-if="store.players['hydra:view']['hydra:previous']"
            @click="store.getPlayersByUrl(store.players['hydra:view']['hydra:previous'])"
            type="button"
            :class="buttonClass"
        >
            Previous
        </button>
      </div>
      <div class="px-4 py-2 mr-4 font-bold">
        page: {{getPage(store.players['hydra:view']['@id'])}} / {{getPage(store.players['hydra:view']['hydra:last'])}}
      </div>
      <div class="grid">
        <button
            v-if="store.players['hydra:view']['hydra:next']"
            type="button"
            @click="store.getPlayersByUrl(store.players['hydra:view']['hydra:next'])"
            :class="buttonClass"
        >
          Next
        </button>
      </div>
      <div class="grid">
        <button
            type="button"
            v-if="store.players['hydra:view']['hydra:last']"
            @click="store.getPlayersByUrl(store.players['hydra:view']['hydra:last'])"
            :class="buttonClass"
        >
          Last
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { departuresStore } from "../store";
const store = departuresStore();

const buttonClass = 'rounded-md border border-white border-solid mx-2';

function getPage(path) {
  const params = new URL(store.baseUrl+path)
  return params.searchParams.get('page');
}

</script>
