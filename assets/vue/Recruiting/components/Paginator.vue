<template>
  <div class="pagination-container">
    <div class="pagination-wrapper">
      <div class="pagination-info">
        <span class="pagination-text">
          Seite {{ currentPageNumber }} von {{ totalPages }}
        </span>
        <span class="pagination-total">
          ({{ store.players.totalItems || 0 }} Spieler gesamt)
        </span>
      </div>

      <div class="pagination-controls">
        <button
            @click="goToPage(1)"
            type="button"
            class="pagination-button"
            :disabled="currentPageNumber === 1"
        >
          <span class="button-icon">⟪</span>
          <span class="button-text">Erste</span>
        </button>

        <button
            @click="goToPage(currentPageNumber - 1)"
            type="button"
            class="pagination-button"
            :disabled="currentPageNumber === 1"
        >
          <span class="button-icon">‹</span>
          <span class="button-text">Zurück</span>
        </button>

        <div class="pagination-pages">
          <button
              v-for="page in visiblePages"
              :key="page"
              @click="goToPage(page)"
              type="button"
              class="pagination-page-button"
              :class="{ 'active': page === currentPageNumber }"
          >
            {{ page }}
          </button>
        </div>

        <button
            type="button"
            @click="goToPage(currentPageNumber + 1)"
            class="pagination-button"
            :disabled="currentPageNumber === totalPages"
        >
          <span class="button-text">Weiter</span>
          <span class="button-icon">›</span>
        </button>

        <button
            type="button"
            @click="goToPage(totalPages)"
            class="pagination-button"
            :disabled="currentPageNumber === totalPages"
        >
          <span class="button-text">Letzte</span>
          <span class="button-icon">⟫</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { recruitingStore } from "../store";
import { computed } from "vue";

const store = recruitingStore();

const currentPageNumber = computed(() => {
  return store.currentPage || 1;
});

const totalPages = computed(() => {
  if (!store.players.totalItems) return 1;

  // Use actual items per page from API response (member array length)
  const actualItemsPerPage = store.players.member?.length || 30;

  return Math.ceil(store.players.totalItems / actualItemsPerPage);
});

const visiblePages = computed(() => {
  const current = currentPageNumber.value;
  const total = totalPages.value;
  const pages = [];

  let start = Math.max(1, current - 2);
  let end = Math.min(total, current + 2);

  if (end - start < 4) {
    if (start === 1) {
      end = Math.min(total, start + 4);
    } else if (end === total) {
      start = Math.max(1, end - 4);
    }
  }

  for (let i = start; i <= end; i++) {
    pages.push(i);
  }

  return pages;
});

function goToPage(page) {
  store.setPage(page);
}
</script>
