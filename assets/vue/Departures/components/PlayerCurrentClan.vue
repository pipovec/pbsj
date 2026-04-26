<template>
  <Transition name="fade">
    <div v-if="clanName" class="current-clan">
      Spieler ist derzeit im Clan: <strong>{{ clanName }}</strong> ({{ clanAbbr }})
    </div>
  </Transition>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const props = defineProps({
  clanId: { type: Number, required: true }
})

const clanName = ref('')
const clanAbbr = ref('')

onMounted(async () => {
  try {
    const res = await fetch(`https://api2.fpcstat.cz/api/clans/${props.clanId}`, {
      headers: { Accept: 'application/json' }
    })
    const clan = await res.json()
    clanName.value = clan.name
    clanAbbr.value = clan.abbreviation
  } catch {}
})
</script>

<style scoped>
.current-clan {
  margin: 0.5rem 1.25rem;
  padding: 0.4rem 0.75rem;
  background: rgba(239, 68, 68, 0.2);
  border: 1px solid rgba(239, 68, 68, 0.5);
  border-radius: 6px;
  font-size: 0.9rem;
  color: #fca5a5;
  text-align: center;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.4s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
