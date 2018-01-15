<template>
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="page-bar">
                <ul class="page-breadcrumb">
                    <li>
                        <i class="fa fa-dashboard"></i> <a href="/admin/dashboard/">控制台</a>
                    </li>
                    <li><i class="fa fa-angle-right"></i></li>
                    <li>
                        <router-link to="/user/list">用户</router-link>
                    </li>
                    <li><i class="fa fa-angle-right"></i></li>
                    <li>
                        <span>用户创建</span>
                    </li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class=" icon-plus font-green"></i>
                                <span class="caption-subject font-green sbold uppercase">用户创建</span>
                            </div>
                            <div class="actions">
                                <router-link to="/user/list" class="btn btn-sm green">返回用户列表</router-link>
                            </div>
                        </div>
                        <div class="portlet-body form">
                            <form method="post" action="/user/create" class="form-horizontal" enctype="multipart/form-data"
                                v-on:submit.prevent="submit">
                                <div class="form-body">
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">
                                            用户名 <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-5">
                                            <input type="text" name="username" class="form-control" placeholder="请输入用户名"
                                                readonly v-model="entity.username"/>
                                        </div>
                                        <div class="col-md-5">
                                            <span class="help-block" style="margin-top: 7px;">用户名由4-18位字母和数字组成</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">
                                            密码
                                        </label>
                                        <div class="col-md-5">
                                            <input type="password" name="entity[password]" class="form-control" placeholder="请输入密码"/>
                                        </div>
                                        <div class="col-md-5">
                                            <span class="help-block" style="margin-top: 7px;"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">
                                            确认密码
                                        </label>
                                        <div class="col-md-5">
                                            <input type="password" name="confirm_password" class="form-control" placeholder="再次输入密码"/>
                                        </div>
                                        <div class="col-md-5">
                                            <span class="help-block" style="margin-top: 7px;"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">
                                            显示名称 <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-5">
                                            <input type="text" name="entity[showname]" class="form-control" placeholder="请输入显示名称" 
                                                required="required" v-model="entity.showname"/>
                                        </div>
                                        <div class="col-md-5">
                                            <span class="help-block" style="margin-top: 7px;"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">
                                            用户角色 <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-5" style="padding-top: 7px;">
                                            <label class="dm-checkbox" v-for="(rolename, role) in roles">
                                                <input class="dm-checkbox-input" type="checkbox" name="entity[role][]" 
                                                    v-model="entity.roles" v-bind:value="role">
                                                <span class="dm-checkbox-span"></span> {{ rolename }}
                                            </label>
                                        </div>
                                        <div class="col-md-5">
                                            <span class="help-block" style="margin-top: 7px;">至少选择一个用户角色</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">
                                            用户头像 <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-5">
                                            <img id="img-compressed" v-bind:src="entity.avatar.webPath" 
                                                style="display:block;" class="inputfile-preview" />
                                            <input type="file" name="file" id="file-1" class="inputfile" accept="image/*" />
                                            <label for="file-1" class="">选择图片</label>
                                        </div>
                                        <div class="col-md-5">
                                            <span class="help-block" style="margin-top: 7px;"></span>
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <div class="row">
                                            <div class="col-md-10 col-md-offset-2">
                                                <input type="hidden" name="_method" value="PUT">
                                                <button class="btn green" type="submit">提交</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import '../../assets/css/form.css'
import ImageFileInput from '../../assets/js/imagefileinput'
import dataUrlToBlob from '../../assets/js/dataurltoblob'

export default {
    name: 'useredit',
    data: function(){
        return {
            entity: {
                avatar: { webPath: '' }
            },
            roles: {
                'ROLE_ADMIN': '管理员',
                'ROLE_USER': '普通用户'
            } 
        }
    },
    mounted: function() {
        let id  = this.$route.params.id
		this.$http.get('/user/'+id).then(response=>{
            this.entity= response.body.data
            this.$Progress.finish()
		}, response=>{})

        let fileinput = new ImageFileInput('#file-1', 960)
    },
    methods: {
        submit: function(e) {
            let form = e.target
            let action = form.getAttribute('action')
            let data = new FormData(form)
            let submitBtn = form.querySelector('button[type=submit]');
            if (data.get('file').size > 0) {
                data.set('file', dataUrlToBlob(document.getElementById('img-compressed').src))
            }

            let id  = this.$route.params.id
            this.$http.post('/user/'+id, data, {
                before: function(){
                    this.$Progress.start()
                    submitBtn.setAttribute("disabled", "disabled")
                },
                emulateJSON: true
            }).then(response => {
                submitBtn.removeAttribute("disabled");
                this.$toasted.success(response.body._msg, {duration: 3000})
                this.$Progress.finish()
                this.$router.push('/user/list')
            }, response => {
                submitBtn.removeAttribute("disabled")
                this.$toasted.error(response.body._msg, {duration: 3000})
                this.$Progress.fail()
            })
        }
    }
}
</script>
