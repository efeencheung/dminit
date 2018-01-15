<template>
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="page-bar">
                <ul class="page-breadcrumb">
                    <li>
                        <i class="fa fa-dashboard"></i> 
                        <router-link to="/">控制台</router-link>
                    </li>
                    <li><i class="fa fa-angle-right"></i></li>
                    <li>
                        <a href="javascript:;">用户管理</a>
                    </li>
                    <li><i class="fa fa-angle-right"></i></li>
                    <li>
                        <span>用户列表</span>
                    </li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="icon-layers font-green"></i>
                                <span class="caption-subject font-green sbold uppercase">用户列表</span>
                            </div>
                            <div class="actions">
                                <form action="/user/" method="get" v-on:submit.prevent="search">
                                    <div class="portlet-input input-inline input-medium">
                                        <div class="input-group">
                                            <input v-model="params.keywords" type="text" name="content"
                                            class="form-control input-sm" value="" placeholder="请输入部分或完整的用户名">
                                            <span class="input-group-btn">
                                                <button class="btn btn-primary btn-sm" type="submit">查找</button>
                                            </span>
                                        </div>
                                    </div>
                                    <router-link to="/video/add" class="btn btn-sm green">添加新用户</router-link>
                                </form>
                            </div>
                        </div>
						<div class="portlet-body">
							<div class="table-scrollable">
								<table class="table table-hover table-light">
									<thead>
										<tr>
											<th style="width: 10%;">头像</th>
											<th>用户名</th>
											<th>显示名称</th>
											<th>用户组</th>
											<th style="width: 10%;">操作</th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="entity in entities">
                                            <td>
                                                <img class="img-circle" v-if="entity.avatar" v-bind:src="'/picture/'+entity.avatar.id+'/cache?filter=admin_list_avatar'" alt="">
                                                <img class="img-circle" style="height: 36px;" v-else src="../../assets/img/avatar-default.png">
                                            </td>
											<td>{{ entity.username }}</td>
											<td>{{ entity.showname }}</td>                            
											<td>{{ entity.rolename }} </td>                            
											<td>
                                                <router-link :to="{ name: 'UserEdit', params: { id: entity.id }}">编辑</router-link>
												<a title="删除" href="javascript:;" v-on:click="deleteConfirm(entity.id)">删除</a>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
                        </div>
                        <paginate
                            :page-count="pageCount"
                            :page-range="3"
                            :margin-pages="2"
                            :click-handler="pageTo"
                            :prev-text="'上一页'"
                            :next-text="'下一页'"
                            :container-class="'pagination'"
                            :page-class="'page-item'">
                        </paginate>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Paginate from 'vuejs-paginate'
import Vex from 'vex-js'

export default {
    name: 'userlist',
    components: {
        'paginate': Paginate
    },
    data: function(){
        return {
            entities: [],
            pageCount: 1,
            params: { page: 1, keywords:null, orderBy: null, direction: null }
        }
    },
	created: function(){
        this.update()
	},
    methods: {
        pageTo: function(p){
            this.params.page = p
            this.update()
        },
        search: function(){
            this.update()
        },
        update: function(){
            this.$Progress.start()
            let paramArr = []
            for(let key in this.params) {
                if (this.params[key]) {
                    paramArr.push(key+'='+this.params[key]);
                }
            }
            let paramStr = paramArr.join('&')

            this.$http.get('/user/?'+paramStr).then(response=>{
                this.entities = response.body.entities
                this.pageCount = response.body.pageCount
                this.$Progress.finish()
            }, response=>{})
        },
        deleteConfirm: function(id) {
            Vex.dialog.confirm({
                message: '确定删除该用户吗?',
                callback: value => {
                    if (value) {
                        this.$http.delete('/user/'+id).then(response=>{
                            this.$toasted.success(response.body._msg, {duration: 3000})
                            this.update()
                        }, response=>{})
                    } else {}
                }
            })
        }
    }
}
</script>
