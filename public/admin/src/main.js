import Vue from 'vue'
import Vuex from 'vuex'
import VueResource from 'vue-resource'
import router from './router'
import Toasted from 'vue-toasted';
import VueProgressBar from 'vue-progressbar'
import App from './App.vue'
import 'bootstrap/dist/css/bootstrap.css'
import 'font-awesome/css/font-awesome.css'
import 'simple-line-icons/css/simple-line-icons.css'
import './assets/css/components.css'
import './assets/css/layout.css'
import './assets/css/themes/light2.css'
import './assets/css/custom.css'

/* Vuex 配置 */
Vue.use(Vuex)
const store = new Vuex.Store({
    state: {
        loggedUser: {},
        logged: false
    },
    mutations: {
        login: function(state, user) {
            state.loggedUser = user
            state.logged = true
        },
        logout: function(state) {
            state.loggedUser = {}
            state.logged = false
        }
    }
})

/* 全局提示 */
Vue.use(Toasted)

/* 进度条 */
Vue.use(VueProgressBar, {
    color: '#3fd5c0',
    failedColor: '#a94442',
    height: '5px'
})

/* http 请求 */
Vue.use(VueResource)
Vue.http.interceptors.push(function(request, next) {
    next(function(response) {
        if (response.status == 401) {
            Vue.prototype.$Progress.finish()
            store.commit('logout')
            router.push('/login')
        } else if (response.status == 403) {
            Vue.prototype.$Progress.finish()
            router.push('/deny')
        }
    });
});

new Vue({
    el: '#app',
    router,
    store,
    template: '<App/>',
    components: { App }
})
