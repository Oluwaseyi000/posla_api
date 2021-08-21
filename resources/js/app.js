require('./bootstrap');

let r = Echo.channel('chat')
  .listen('.ChatMessageSent', (e) => {
      console.log(e);
    // this.messages.push({
    //   message: e.message.message,
    //   user: e.user
    // });
  });
  console.log(12, r);
