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
                        <a href="javascript:;">图文资讯管理</a>
                    </li>
                    <li><i class="fa fa-angle-right"></i></li>
                    <li>
                        <span>图文资讯列表</span>
                    </li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="icon-layers font-green"></i>
                                <span class="caption-subject font-green sbold uppercase">图文资讯列表</span>
                            </div>
                            <div class="actions">
                                <form action="/article/" method="get" v-on:submit.prevent="search">
                                    <div class="portlet-input input-inline input-medium">
                                        <div class="input-group">
                                            <input v-model="params.keywords" type="text" name="content"
                                            class="form-control input-sm" value="" placeholder="请输入部分或完整的图文资讯名">
                                            <span class="input-group-btn">
                                                <button class="btn btn-primary btn-sm" type="submit">查找</button>
                                            </span>
                                        </div>
                                    </div>
                                    <router-link to="/article/add" class="btn btn-sm green">添加新图文资讯</router-link>
                                </form>
                            </div>
                        </div>
						<div class="portlet-body">
							<div class="table-scrollable">
								<table class="table table-hover table-light">
									<thead>
										<tr>
											<th style="width: 10%;">封面图片</th>
											<th>标题</th>
											<th>作者</th>
											<th>发布时间</th>
											<th style="width: 10%;">操作</th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="entity in entities">
                                            <td>
                                                <img v-bind:src="'/picture/'+entity.picture.id+'/cache?filter=admin_list_thumb'" />
                                            </td>
											<td>{{ entity.title }}</td>
											<td>{{ entity.author }}</td>                            
											<td>{{ entity.publishedAt }} </td>                            
											<td>
                                                <router-link :to="{ name: 'ArticleEdit', params: { id: entity.id }}">编辑</router-link>
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
import Vex from 'vex-js'
import Paginate from 'vuejs-paginate'

export default {
    name: 'articlelist',
    components: {
        'paginate': Paginate
    },
    data: function(){
        return {
            entities: [],
            pageCount: 1,
            params: { page: 1, type: 1, orderBy: null, direction: null, keywords: null }
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

            this.$http.get('/article/?'+paramStr).then(response=>{
                this.entities = response.body.entities
                this.pageCount = response.body.pageCount
                this.$Progress.finish()
            }, response=>{})
        },
        deleteConfirm: function(id) {
            Vex.dialog.confirm({
                message: '确定删除该图文资讯吗?',
                callback: value => {
                    if (value) {
                        this.$http.delete('/article/'+id).then(response=>{
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
