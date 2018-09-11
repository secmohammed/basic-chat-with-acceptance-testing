import Bus from './bus';
Echo.join('chat')
    .here(users => {
        Bus.$emit('users-joined', users);
    })
    .joining(user => {
        Bus.$emit('user-joined', user);
    })
    .leaving(user => {
        Bus.$emit('user-left', user);
    })
    .listen('Chat.MessageCreated', e => Bus.$emit('messages.added', e.message));
