import Vue from 'vue';
import VueRouter from 'vue-router';

import AccountApp from './components/AccountApp.vue';
import { createAccountRouter } from './router';

Vue.use(VueRouter);

const mountEl = document.getElementById('account-app');
if (mountEl) {
  const router = createAccountRouter();

  new Vue({
    router,
    render: (h) => h(AccountApp),
  }).$mount('#account-app');
}

