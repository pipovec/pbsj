<template>
  <div class="chat-wrapper">
    <!-- Header + Formular -->
    <div class="chat-toolbar">
      <div class="toolbar-top">
        <h1 class="toolbar-title">Pinnwand</h1>
        <span class="total-count">{{ total !== null ? total + ' Nachrichten' : '…' }}</span>
      </div>

      <form class="chat-form" @submit.prevent="sendMessage()">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="chat-nick">Nick (wie im Spiel)</label>
            <div class="nick-wrap">
              <input
                id="chat-nick"
                v-model="nick"
                type="text"
                class="form-input"
                :class="{ 'input-error': nickError }"
                placeholder="Dein WoT-Nick"
                maxlength="100"
                autocomplete="off"
              />
              <span v-if="nickError" class="field-error">{{ nickError }}</span>
            </div>
          </div>

          <div class="form-group form-group--grow">
            <label class="form-label" for="chat-text">Nachricht</label>
            <input
              id="chat-text"
              v-model="text"
              type="text"
              class="form-input"
              :class="{ 'input-error': textError }"
              placeholder="Hinterlasse eine Nachricht für die Nachwelt…"
              maxlength="1000"
              :disabled="sending"
            />
            <span v-if="textError" class="field-error">{{ textError }}</span>
          </div>

          <button type="submit" class="send-btn" :disabled="sending || !nick.trim() || !text.trim()">
            {{ sending ? 'Wird gesendet…' : 'Senden' }}
          </button>
        </div>

        <div v-if="sendError" class="send-error">{{ sendError }}</div>
        <div v-if="sendSuccess" class="send-success">Nachricht wurde gesendet.</div>
      </form>
    </div>

    <!-- Laden -->
    <div v-if="loading" class="state-msg">Nachrichten werden geladen…</div>

    <!-- Nachrichten -->
    <div v-else-if="messages.length" class="messages-list">
      <div v-for="msg in messages" :key="msg.id" class="message-card">
        <div class="message-header">
          <span class="message-nick">{{ msg.name }}</span>
          <span class="message-date">{{ formatDate(msg.createdAt) }}</span>
        </div>
        <p class="message-text">{{ msg.text }}</p>

        <div class="message-actions">
          <button class="reply-toggle" @click="toggleReplies(msg)">
            <span v-if="expandedId === msg.id">▲ Verbergen</span>
            <span v-else-if="msg.replyCount > 0">▼ Antworten ({{ replyCounts[msg.id] ?? msg.replyCount }})</span>
            <span v-else>💬 Antworten</span>
          </button>
        </div>

        <!-- Antworten -->
        <div v-if="expandedId === msg.id" class="replies-section">
          <div v-if="repliesLoading[msg.id]" class="state-msg state-msg--sm">Wird geladen…</div>
          <div v-else-if="replies[msg.id]?.length" class="replies-list">
            <div v-for="reply in replies[msg.id]" :key="reply.id" class="reply-card">
              <div class="message-header">
                <span class="message-nick">{{ reply.name }}</span>
                <span class="message-date">{{ formatDate(reply.createdAt) }}</span>
              </div>
              <p class="message-text">{{ reply.text }}</p>
            </div>
          </div>

          <form class="reply-form" @submit.prevent="sendMessage(msg.id)">
            <div class="form-row">
              <input
                v-model="replyNicks[msg.id]"
                type="text"
                class="form-input form-input--sm reply-nick"
                placeholder="Dein Nick"
                maxlength="100"
                autocomplete="off"
                :disabled="replySending[msg.id]"
              />
              <div class="form-group form-group--grow">
                <input
                  v-model="replyTexts[msg.id]"
                  type="text"
                  class="form-input form-input--sm"
                  placeholder="Antwort schreiben…"
                  maxlength="1000"
                  :disabled="replySending[msg.id]"
                />
              </div>
              <button
                type="submit"
                class="send-btn send-btn--sm"
                :disabled="replySending[msg.id] || !replyNicks[msg.id]?.trim() || !replyTexts[msg.id]?.trim()"
              >
                {{ replySending[msg.id] ? '…' : 'Antworten' }}
              </button>
            </div>
            <div v-if="replyErrors[msg.id]" class="send-error">{{ replyErrors[msg.id] }}</div>
          </form>
        </div>
      </div>
    </div>

    <div v-else class="state-msg">Keine Nachrichten vorhanden.</div>

    <!-- Seitennavigation -->
    <div v-if="pages > 1" class="pagination">
      <button class="page-btn" :disabled="currentPage <= 1" @click="goToPage(currentPage - 1)">← Zurück</button>
      <span class="page-info">{{ currentPage }} / {{ pages }}</span>
      <button class="page-btn" :disabled="currentPage >= pages" @click="goToPage(currentPage + 1)">Weiter →</button>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'

const SITE_ID = 'pbsj'
const LIMIT = 5

const nick = ref('')
const text = ref('')
const sending = ref(false)
const sendError = ref('')
const sendSuccess = ref(false)
const nickError = ref('')
const textError = ref('')

const messages = ref([])
const loading = ref(false)
const total = ref(null)
const currentPage = ref(1)
const pages = ref(1)

const expandedId = ref(null)
const replies = reactive({})
const repliesLoading = reactive({})
const replyTexts = reactive({})
const replyNicks = reactive({})
const replySending = reactive({})
const replyErrors = reactive({})
const replyCounts = reactive({})

onMounted(() => {
  const saved = localStorage.getItem('chat-nick')
  if (saved) nick.value = saved
  fetchMessages()
})

function watchNick(val) {
  localStorage.setItem('chat-nick', val)
  nickError.value = ''
}

async function fetchMessages() {
  loading.value = true
  try {
    const params = new URLSearchParams({ siteId: SITE_ID, page: currentPage.value, limit: LIMIT })
    const res = await fetch(`/api/chat/messages?${params}`)
    const data = await res.json()
    messages.value = data.data
    total.value = data.total
    pages.value = data.pages
  } catch {
    messages.value = []
  } finally {
    loading.value = false
  }
}

async function sendMessage(parentId) {
  const isReply = !!parentId

  if (!isReply) {
    nickError.value = ''
    textError.value = ''
    sendError.value = ''
    sendSuccess.value = false
    if (!nick.value.trim()) { nickError.value = 'Nick eingeben'; return }
    if (!text.value.trim()) { textError.value = 'Nachricht eingeben'; return }
    watchNick(nick.value)
  } else {
    replyErrors[parentId] = ''
    if (!replyNicks[parentId]?.trim()) { replyErrors[parentId] = 'Nick eingeben'; return }
    if (!replyTexts[parentId]?.trim()) return
  }

  const bodyNick = isReply ? replyNicks[parentId].trim() : nick.value.trim()
  const bodyText = isReply ? replyTexts[parentId].trim() : text.value.trim()

  if (isReply) replySending[parentId] = true
  else sending.value = true

  try {
    const res = await fetch('/api/chat/messages', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        siteId: SITE_ID,
        name: bodyNick,
        text: bodyText,
        ...(parentId && { parentId }),
      }),
    })

    const data = await res.json()

    if (!res.ok) {
      const err = data.error ?? 'Fehler beim Senden'
      if (isReply) replyErrors[parentId] = err
      else sendError.value = err
      return
    }

    if (isReply) {
      replyTexts[parentId] = ''
      replyNicks[parentId] = ''
      await loadReplies(parentId)
      replyCounts[parentId] = replies[parentId]?.length ?? 0
    } else {
      text.value = ''
      sendSuccess.value = true
      currentPage.value = 1
      await fetchMessages()
      setTimeout(() => { sendSuccess.value = false }, 4000)
    }
  } catch {
    const err = 'Verbindungsfehler'
    if (isReply) replyErrors[parentId] = err
    else sendError.value = err
  } finally {
    if (isReply) replySending[parentId] = false
    else sending.value = false
  }
}

async function toggleReplies(msg) {
  if (expandedId.value === msg.id) {
    expandedId.value = null
    return
  }
  expandedId.value = msg.id
  if (!replies[msg.id]) await loadReplies(msg.id)
}

async function loadReplies(id) {
  repliesLoading[id] = true
  try {
    const res = await fetch(`/api/chat/replies/${id}`)
    replies[id] = await res.json()
    replyCounts[id] = replies[id].length
  } catch {
    replies[id] = []
  } finally {
    repliesLoading[id] = false
  }
}

function goToPage(page) {
  currentPage.value = page
  expandedId.value = null
  window.scrollTo({ top: 0, behavior: 'smooth' })
  fetchMessages()
}

function formatDate(iso) {
  return new Date(iso).toLocaleString('de-DE', {
    day: '2-digit', month: '2-digit', year: 'numeric',
    hour: '2-digit', minute: '2-digit',
  })
}
</script>


