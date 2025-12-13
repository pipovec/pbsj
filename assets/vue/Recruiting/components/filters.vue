<template>
  <button class="filter-button" @click="openFilter = !openFilter" :class="{ 'active': openFilter }">
    <svg class="filter-icon"
         viewBox="0 0 1024 1024"
         version="1.1"
         xmlns="http://www.w3.org/2000/svg">
      <path d="M859.02 234.524l0.808-0.756 0.749-0.813c27.047-29.356 33.876-70.34 17.823-106.957-15.942-36.366-50.416-58.957-89.968-58.957H163.604c-38.83 0-73.043 22.012-89.29 57.444-16.361 35.683-10.632 76.301 14.949 106.004l0.97 1.126 280.311 266.85 2.032 312.074c0.107 16.502 13.517 29.805 29.995 29.805l0.2-0.001c16.568-0.107 29.912-13.626 29.804-30.194l-2.198-337.564-296.478-282.241c-9.526-11.758-11.426-26.933-5.044-40.851 6.446-14.059 19.437-22.452 34.75-22.452h624.828c15.6 0 28.69 8.616 35.017 23.047 6.31 14.391 3.924 29.831-6.354 41.497l-304.13 284.832 1.302 458.63c0.047 16.54 13.469 29.916 29.998 29.915h0.087c16.568-0.047 29.962-13.517 29.915-30.085L573.04 502.36l285.98-267.836z" fill="currentColor" />
    </svg>
    <span class="filter-text">Filter</span>
  </button>
  <transition name="filters">
    <div class="filters-panel" v-if="openFilter">
      <div class="filters-panel__header">
        <h3>Filtermöglichkeiten</h3>
        <button class="close-button" @click="openFilter = false">✕</button>
      </div>

      <div class="filters-panel__section">
        <label class="filter-label">WN8 Bereich</label>
        <div class="filter-range">
          <div class="filter-input-group">
            <label for="wnFrom" class="input-label">Von:</label>
            <input type="number" id="wnFrom" v-model="store.filterWnFrom" @change="reloadPlayers()" placeholder="0">
          </div>
          <span class="range-separator">-</span>
          <div class="filter-input-group">
            <label for="wnTo" class="input-label">Bis:</label>
            <input type="number" id="wnTo" v-model="store.filterWnTo" @change="reloadPlayers()" placeholder="10000">
          </div>
        </div>
      </div>

      <div class="filters-panel__section">
        <label for="nickname" class="filter-label">Spielername</label>
        <input type="text" id="nickname" class="filter-text-input" v-model="store.filterNickName" v-on:input="reloadPlayers()" placeholder="Name eingeben...">
      </div>

      <div class="filters-panel__section">
        <label class="filter-label">Spezielle Panzer</label>
        <div class="filter-checkboxes">
          <label class="checkbox-label">
            <input type="checkbox" name="tank_1" v-model="store.tank_1" v-on:change="reloadPlayers()">
            <span class="checkbox-text">Obj. 279r</span>
          </label>
          <label class="checkbox-label">
            <input type="checkbox" name="tank_2" v-model="store.tank_2" v-on:change="reloadPlayers()">
            <span class="checkbox-text">Obj. 907</span>
          </label>
          <label class="checkbox-label">
            <input type="checkbox" name="tank_3" v-model="store.tank_3" v-on:change="reloadPlayers()">
            <span class="checkbox-text">T95/FV4201 Chieftain</span>
          </label>
        </div>
      </div>

      <div class="filters-panel__footer">
        <button class="reset-button" @click="resetFilters()">
          <span class="reset-icon">↻</span>
          <span>Filter zurücksetzen</span>
        </button>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { recruitingStore } from "../store";
import {ref} from "vue";

const openFilter = ref(false)

const store = recruitingStore()

function reloadPlayers() {
  store.getPlayers()
}

function resetFilters() {
  store.resetFilters()
  openFilter.value = false
}

</script>

<style>
.filters-enter-active,
.filters-leave-active {
  transition: opacity 0.5s;
}
.filters-enter-from,
.filters-leave-to {
  opacity: 0;
}
</style>
