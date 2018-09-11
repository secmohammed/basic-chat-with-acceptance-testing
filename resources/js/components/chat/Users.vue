<template>
    <div class="users" v-if="users.length">
        <div class="users__header">{{ users.length }}  {{ pluralize('user' , users.length) }} online</div>
        <div class="users__user" v-for="user in users">
            <a href="#">{{ user.name }}</a>
        </div>
    </div>
</template>
<script>
import Bus from '../../bus.js';
import pluralize from 'pluralize';
export default {
    data() {
        return {
            users: []
        };
    },
    methods: {
        pluralize: pluralize
    },
    mounted() {
        Bus.$on('users-joined', users => {
            this.users = users;
        })
            .$on('user-joined', user => {
                this.users.unshift(user);
            })
            .$on('user-left', user => {
                this.users = this.users.filter(u => u.id !== user.id);
            });
    }
};
</script>
<style lang="scss">
.users {
    background-color: #fff;
    border: 1px solid #d3e0e9;
    border-radius: 3px;
    &__header {
        padding: 15px;
        font-weight: 800;
        margin: 0;
    }
    &__user {
        padding: 0 15px;
        &:last-child {
            padding-bottom: 15px;
        }
    }
}
</style>
