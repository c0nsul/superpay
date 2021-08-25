require('./bootstrap');
import Vue from 'vue';

Vue.component('dashboard-payments', require('./components/DashboardPayments.vue').default);

const app = new Vue({
    el: '#app',
});
