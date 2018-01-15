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
                        <router-link to="/article/list">图文资讯</router-link>
                    </li>
                    <li><i class="fa fa-angle-right"></i></li>
                    <li>
                        <span>图文资讯创建</span>
                    </li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class=" icon-plus font-green"></i>
                                <span class="caption-subject font-green sbold uppercase">图文资讯创建</span>
                            </div>
                            <div class="actions">
                                <router-link to="/article/list" class="btn btn-sm green">返回图文资讯列表</router-link>
                            </div>
                        </div>
                        <div class="portlet-body form">
                            <form method="post" action="/article/create" class="form-horizontal" enctype="multipart/form-data"
                                v-on:submit.prevent="submit">
                                <div class="form-body">
                                    <div class="form-group">
                                        <div class="col-md-9">
                                            <input type="text" name="entity[title]" class="form-control" placeholder="请输入图文资讯标题"/>
                                        </div>
                                        <div class="col-md-3">
                                            <span class="help-block" style="margin-top: 7px;"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-9">
                                            <input type="text" name="entity[author]" class="form-control" placeholder="请输入作者(选填)"/>
                                        </div>
                                        <div class="col-md-3">
                                            <span class="help-block" style="margin-top: 7px;"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-9">
                                            <textarea name="entity[summary]" class="form-control" rows="6" placeholder="请输入内容摘要(选填)"></textarea>
                                        </div>
                                        <div class="col-md-3">
                                            <span class="help-block" style="margin-top: 7px;"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-9">
                                            <quill-editor v-bind:value="entity.content" name="entity[content]"></quill-editor>
                                        </div>
                                        <div class="col-md-3">
                                            <span class="help-block" style="margin-top: 7px;">
                                            插入的图片有可能会90度翻转，这种情况下需要把图片保存成PNG格式。
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-9">
                                            <label class="dm-checkbox" v-for="tag in tags">
                                                <input class="dm-checkbox-input" type="checkbox" name="entity[tags][]" v-bind:value="tag.id">
                                                <span class="dm-checkbox-span"></span> {{ tag.name }}
                                            </label>
                                        </div>
                                        <div class="col-md-3">
                                            <span class="help-block" style="margin-top: 7px;">可选择一个或多个所属标签</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-9">
                                            <div>
                                                <image-input name="entity[picture]"></image-input>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <span class="help-block" style="margin-top: 7px;">请选择一个封面图片</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-9">
                                            <datetime-input name="entity[publishedAt]" placeholder="请选择发布时间"></datetime-input>
                                        </div>
                                        <div class="col-md-3">
                                            <span class="help-block" style="margin-top: 7px;">
                                                文章定时发布设置，默认值为及时发布
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <div class="row">
                                            <div class="col-md-10">
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
import DatetimeInput from '../../components/form/DatetimeInput.vue'
import QuillEditor from '../../components/form/QuillEditor.vue'
import ImageInput from '../../components/form/ImageInput.vue'
import dataUrlToBlob from '../../assets/js/dataurltoblob'

export default {
    name: 'articleadd',
    components: {
        'quill-editor': QuillEditor,
        'datetime-input': DatetimeInput,
        'image-input': ImageInput
    },
    data: function(){
        return {
            entity: {},
            tags: []
        }
    },
    mounted: function() {
        this.$http.get('/tag/').then(response=>{
            this.tags = response.body.entities
            this.$Progress.finish()
        }, response=>{})
    },
    methods: {
        submit: function(e) {
            let form = e.target
            let action = form.getAttribute('action')
            let data = new FormData(form)
            let submitBtn = form.querySelector('button[type=submit]');
            if (data.get('entity[picture]').size > 0) {
                data.set('entity[picture]', dataUrlToBlob(form.querySelector('.picture-preview').src))
            }
            this.$http.post(action, data, {
                before: function(){
                    this.$Progress.start()
                    submitBtn.setAttribute("disabled", "disabled")
                },
                emulateJSON: true
            }).then(response => {
                submitBtn.removeAttribute("disabled");
                this.$toasted.success(response.body._msg, {duration: 3000})
                this.$Progress.finish()
                this.$router.push('/article/list')
            }, response => {
                submitBtn.removeAttribute("disabled")
                this.$toasted.error(response.body._msg, {duration: 3000})
                this.$Progress.fail()
            })
        }
    }
}
</script>
