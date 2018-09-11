<template>
    <div class="chat">
        <chat-messages></chat-messages>
        <form action="#" class="chat__form">
            <textarea
            id="body"
            cols="30"
            rows="4"
            class="chat__form-input"
            @keydown="handleMessageInput"
            v-model.trim="body"
            >
            </textarea>
            <span class="chat__form-helptext">Hit Return to send or Shift + Return for a new line </span>
        </form>
    </div>
</template>
<script>
import Bus from '../../bus.js';
import moment from 'moment';
export default {
    data() {
        return {
            body: null,
            bodyBackedUp: null
        };
    },
    methods: {
        buildTempMessage() {
            let tempId = Date.now();
            return {
                id: tempId,
                body: this.body,
                created_at: moment()
                    .utc(0)
                    .format('YYYY-MM-DD HH:mm:ss'),
                selfOwned: true,
                user: {
                    name: Laravel.user.name
                }
            };
        },
        send() {
            if (!this.body) {
                return;
            }
            let tempMessage = this.buildTempMessage();
            Bus.$emit('messages.added', tempMessage);
            axios
                .post('/chat/messages', {
                    body: this.body
                })
                .catch(() => {
                    this.body = this.bodyBackedUp;
                    Bus.$emit('messages.removed', tempMessage);
                });
            this.body = null;
        },
        handleMessageInput(e) {
            this.bodyBackedUp = this.body;
            if (e.keyCode === 13 && !e.shiftKey) {
                e.preventDefault();
                this.send();
            }
        }
    }
};
</script>
<style lang="scss">
.chat {
    background-color: #fff;
    border: 1px solid #d3e0e9;
    border-radius: 3px;
    margin-bottom: 20px;

    &__form {
        border-top: 1px solid #d3e0e9;
        padding: 10px;

        &-input {
            width: 100%;
            border: 1px solid #d3e0e9;
            padding: 5px 10px;
            outline: none;
        }

        &-helptext {
            color: #aaa;
        }
    }
}
</style>
