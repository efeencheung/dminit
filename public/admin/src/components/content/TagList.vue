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
                        <a href="javascript:;">标签管理</a>
                    </li>
                    <li><i class="fa fa-angle-right"></i></li>
                    <li>
                        <span id="test">标签列表</span>
                    </li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="icon-layers font-green"></i>
                                <span class="caption-subject font-green sbold uppercase">标签列表</span>
                            </div>
                            <div class="actions">
                                <form action="/tag/create" method="post" v-on:submit.prevent="submit">
                                    <div class="portlet-input input-inline input-medium">
                                        <div class="input-group">
                                            <input type="text" name="entity[name]" required="required"
                                                class="form-control input-sm" placeholder="请输入标签名称">
                                            <span class="input-group-btn">
                                                <button class="btn btn-primary btn-sm" type="submit">添加</button>
                                            </span>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
						<div class="portlet-body">
							<div class="table-scrollable">
								<table class="table table-hover table-light">
									<thead>
										<tr>
											<th>标签名称</th>
											<th style="width: 10%;">操作</th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="entity in entities">
											<td>{{ entity.name }} </td>                            
											<td>
                                                <a title="编辑" href="javascript:;" v-on:click="edit(entity.id, entity.name)">编辑</a>
												<a title="删除" href="javascript:;" v-on:click="deleteConfirm(entity.id)">删除</a>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import 'vex-js/dist/css/vex.css'
import 'vex-js/dist/css/vex-theme-default.css'
import Vex from 'vex-js'
import Dialog from 'vex-dialog' 

Vex.registerPlugin(Dialog)
Vex.defaultOptions.className = 'vex-theme-default'
Vex.dialog.buttons.YES.text = '确定'
Vex.dialog.buttons.NO.text = '取消'

export default {
    name: 'taglist',
    data: function(){
        return {
            entities: [],
            name: null
        }
    },
	created: function(){
        this.update()
	},
    mounted: function(){
    },
    methods: {
        submit: function(e){
            let form = e.target
            let action = form.getAttribute('action')
            let data = new FormData(form)
            let submitBtn = form.querySelector('button[type=submit]');

            this.$http.post(action, data, {
                before: function(){
                    this.$Progress.start()
                    submitBtn.setAttribute("disabled", "disabled");
                },
                emulateJSON: true
            }).then(response => {
                submitBtn.removeAttribute("disabled");
                this.$toasted.success(response.body._msg, {duration: 3000})
                this.$Progress.finish()
                this.update()
            }, response => {
                submitBtn.removeAttribute("disabled");
                this.$toasted.error(response.body._msg, {duration: 3000})
                this.$Progress.fail()
            })
        },
        update: function(){
            this.$Progress.start()

            this.$http.get('/tag/').then(response=>{
                this.entities = response.body.entities
                this.$Progress.finish()
            }, response=>{})
        },
        edit: function(id, value){
            Vex.dialog.open({
                message: '标签名称编辑:',
                input: [
                    '<input name="name" type="text" value="'+value+'" placeholder="请输入标签名称" required="required" />',
                ].join(''),
                callback: data => {
                    if (!data) {
                        console.log('Cancelled')
                    } else {
                        console.log
                        this.$Progress.start()
                        this.$http.post('/tag/'+id, { 'entity[name]': data.name, '_method': 'PUT' }, {
                            emulateJSON: true
                        }).then(response=>{
                            this.$toasted.success(response.body._msg, {duration: 3000})
                            this.$Progress.finish()
                            this.update()
                        }, response=>{
                            this.$toasted.error(response.body._msg, {duration: 3000})
                            this.$Progress.fail()
                        })
                    }
                }
            })
        },
        deleteConfirm: function(id) {
            Vex.dialog.confirm({
                message: '确定删除该标签吗?',
                callback: value => {
                    if (value) {
                        this.$http.delete('/tag/'+id).then(response=>{
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
