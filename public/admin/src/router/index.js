import Vue from 'vue'
import Router from 'vue-router'

import Dashboard from '../components/Dashboard.vue'
import Deny from '../components/Deny.vue'
import Login from '../components/Login.vue'
import ArticleAdd from '../components/content/ArticleAdd.vue'
import ArticleEdit from '../components/content/ArticleEdit.vue'
import ArticleList from '../components/content/ArticleList.vue'
import PictureAdd from '../components/content/PictureAdd.vue'
import PictureEdit from '../components/content/PictureEdit.vue'
import PictureList from '../components/content/PictureList.vue'
import PicturePhoto from '../components/content/PicturePhoto.vue'
import TagList from '../components/content/TagList.vue'
import UserAdd from '../components/user/UserAdd.vue'
import UserEdit from '../components/user/UserEdit.vue'
import UserList from '../components/user/UserList.vue'
import VideoAdd from '../components/content/VideoAdd.vue'
import VideoEdit from '../components/content/VideoEdit.vue'
import VideoList from '../components/content/VideoList.vue'

Vue.use(Router)

export default new Router({
    routes: [
        {
            path: '/',
            name: 'Dashboard',
            component: Dashboard
        },
        {
            path: '/login',
            name: 'Login',
            component: Login
        },
        {
            path: '/deny',
            name: 'Deny',
            component: Deny
        },
        {
            path: '/article/add',
            name: 'ArticleAdd',
            component: ArticleAdd
        },
        {
            path: '/article/:id/edit',
            name: 'ArticleEdit',
            component: ArticleEdit
        },
        {
            path: '/article/list',
            name: 'ArticleList',
            component: ArticleList
        },
        {
            path: '/picture/add',
            name: 'PictureAdd',
            component: PictureAdd
        },
        {
            path: '/picture/:id/edit',
            name: 'PictureEdit',
            component: PictureEdit
        },
        {
            path: '/picture/list',
            name: 'PictureList',
            component: PictureList
        },
        {
            path: '/picture/:id/photo',
            name: 'PicturePhoto',
            component: PicturePhoto
        },
        {
            path: '/tag/list',
            name: 'TagList',
            component: TagList
        },
        {
            path: '/user/add',
            name: 'UserAdd',
            component: UserAdd
        },
        {
            path: '/user/:id/edit',
            name: 'UserEdit',
            component: UserEdit
        },
        {
            path: '/user/list',
            name: 'UserList',
            component: UserList
        },
        {
            path: '/video/add',
            name: 'VideoAdd',
            component: VideoAdd
        },
        {
            path: '/video/:id/edit',
            name: 'VideoEdit',
            component: VideoEdit
        },
        {
            path: '/video/list',
            name: 'VideoList',
            component: VideoList
        }
    ]
})
