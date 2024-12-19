<template>
  <div class="container py-5">
    <div class="row">
      <div class="col-12">
        <div v-for="chatGroup in chatGroups" :key="chatGroup.id" class="card mb-4">
          <div class="card-header">
            <h3>{{ chatGroup.name }}</h3>
          </div>
          <div class="card-body chat-messages" style="max-height: 300px; overflow-y: auto;">
            <div v-for="message in chatGroup.messages" :key="message.id" class="mb-3">
              <strong>{{ message.user.name }}:</strong> {{ message.message }}
            </div>
          </div>
          <div class="card-footer">
            <input v-model="newMessage" @keyup.enter="sendMessage(chatGroup.id)" 
                   class="form-control" placeholder="Type a message...">
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ['user', 'chatGroups'],
  data() {
    return {
      newMessage: ''
    };
  },
  methods: {
    sendMessage(chatGroupId) {
      if (this.newMessage.trim() === '') return;

      const message = {
        id: Date.now(), // Temporary ID
        user: this.user,
        message: this.newMessage
      };

      const group = this.chatGroups.find(group => group.id === chatGroupId);
      if (group) {
        group.messages.push(message);
        this.newMessage = '';
        this.scrollToBottom(group.id);
      }

      axios.post('/chat/message', {
        chat_group_id: chatGroupId,
        message: message.message
      }).then(response => {
        // Optionally handle the response, e.g., updating the message ID if needed
      }).catch(error => {
        console.error('Message send failed:', error);
      });
    },
    scrollToBottom(chatGroupId) {
      this.$nextTick(() => {
        const chatMessages = this.$el.querySelector(`.chat-messages[data-group-id="${chatGroupId}"]`);
        if (chatMessages) {
          chatMessages.scrollTop = chatMessages.scrollHeight;
        }
      });
    }
  },
  mounted() {
    window.Echo.channel('chat')
      .listen('MessageSent', (e) => {
        const group = this.chatGroups.find(group => group.id === e.message.chat_group_id);
        if (group) {
          group.messages.push(e.message);
          this.scrollToBottom(group.id);
        }
      });
  }
}
</script>

<style>
.chat-messages {
  background-color: #f8f9fa;
  padding: 1rem;
  border-radius: 5px;
}
</style>
