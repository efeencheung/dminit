<template>
    <div id="app">
        <vue-progress-bar></vue-progress-bar>
        <div class="page-header navbar clearfix">
            <div class="page-header-inner ">
                <div class="page-logo">
                    <a href="javascript:;" class="text-logo font-red">
                        <span class="logo-default">格天内容管理系统</span>
                    </a>
                </div>
                <div class="top-menu">
                    <ul class="nav navbar-nav pull-right">
                        <li class="dropdown dropdown-user">
                            <a href="javascript:;" class="dropdown-toggle">
                                <img class="img-circle" v-if="loggedUser.avatar" v-bind:src="'/picture/'+loggedUser.avatar.id+'/cache?filter=admin_list_avatar'">
                                <img class="img-circle" v-else src="./assets/img/avatar-default.png">
                                <span class="username username-hide-on-mobile">{{ loggedUser.showname }}</span>
                            </a>
                        </li>
                        <li class="dropdown dropdown-quick-sidebar-toggler">
                            <a href="javascript:;" class="dropdown-toggle" v-on:click="logout"><i class="icon-logout"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class=page-container>
			<sidebar></sidebar>
            <router-view></router-view>
        </div>
        <div class=page-footer>
            <div class=page-footer-inner>2013 - 2017 &copy; 北京电马网络科技中心</div>
            <div class=scroll-to-top>
                <i class=icon-arrow-up></i>
            </div>
        </div>
    </div>
</template>

<script>
import Sidebar from './components/Sidebar.vue'

export default {
    name: 'app',
    data: function() {
        return {}
    },
    computed: {
        logged: function() {
            return this.$store.state.logged
        },
        loggedUser: function() {
            return this.$store.state.loggedUser
        }
    },
    mounted: function() {
        this.$http.get('/loggeduser').then(response=>{
            let user = response.body.data
            if (user === null) {
                user = {}
            }
            this.$store.commit('login', user)
        }, response=>{})
    },
    methods: {
        logout: function() {
            this.$Progress.start()
            this.$http.get('/logout').then(response => {
                this.$Progress.finish()
            }, response => {
                this.$Progress.finish()
            })
        }
    },
    components: {
        'sidebar': Sidebar
    }
}
</script>
