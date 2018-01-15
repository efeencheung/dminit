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
                        <router-link to="/picture/list">图片资讯</router-link>
                    </li>
                    <li><i class="fa fa-angle-right"></i></li>
                    <li>
                        <span>照片列表</span>
                    </li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="icon-layers font-green"></i>
                                <span class="caption-subject font-green sbold uppercase">照片列表</span>
                            </div>
                            <div class="actions">
                                <input type="file" name="file" id="upload-file" class="upload-file" accept="image/*" />
                                <label for="upload-file" class="btn-sm">上传图片</label>
                            </div>
                        </div>
						<div class="portlet-body clearfix">
                            <div class="row">
                                <div class="col-xs-3 picture-item" v-for="photo in photos" v-on:click="showPicture(photo.webPath)">
                                    <img class="picture-list" v-bind:src="'/picture/'+photo.id+'/cache?filter=admin_photo_list'" alt="">
                                    <div class="picture-actions">
                                        <div class="btn-group btn-group btn-group-justified">
                                            <a href="javascript:;" class="btn green" v-bind:data-description="photo.description" 
                                                v-bind:data-pid="photo.id" v-on:click.stop="descriptionPicture">添加描述</a>
                                            <a href="javascript:;" class="btn red" v-on:click.stop="deletePicture(photo.id)">删除图片</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-picture-wrapper" style="display: none;">
            <div class="modal-picture">
                <img class="preview" v-bind:src="modalPicturePath" alt=""/>
            </div>
        </div>
        <div class="modal-description-wrapper" style="display:none;">
            <div class="modal-description portlet light">
                <div class="portlet-title">
                    <div class="caption">
                        <span class="caption-subject font-green sbold uppercase">添加/修改照片描述</span>
                    </div>
                    <div class="actions">
                    </div>
                </div>
                <div class="portlet-body clearfix">
                    <form class="form" v-bind:action="'/picture/'+pid" method="put" v-on:submit.prevent="submit">
                        <div class="form-group">
                            <div>
                                <textarea class="form-control" name="entity[description]" rows="5" placeholder="输入照片描述" 
                                    v-model="description"></textarea>
                            </div>
                        </div>
                        <div class="form-actions">
                            <input type="hidden" name="_method" value="PUT" />
                            <button class="btn green" type="submit">提交</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import 'vex-js/dist/css/vex.css' 
import Vex from 'vex-js' 
import dataUrlToBlob from '../../assets/js/dataurltoblob'

export default {
    name: 'picturephoto',
    data: function() {
        return {
            photos: null,
            modalPicturePath: null,
            description: null,
            pid: null
        }
    },
    mounted: function() {
        this.update()

        let id = this.$route.params.id
        let uploadFile = document.getElementById('upload-file')
        uploadFile.addEventListener('change', e => {
            let filename = e.target.value.split( '\\' ).pop()
            if (uploadFile.files != null && uploadFile.files[0] != null) {
                let mime_type = uploadFile.files[0].type
                /* Gif 格式不压缩 */
                if (mime_type == "image/gif") {
                    return
                }

                /* 限制文件格式 */
                if (mime_type.substr(0, 5) != 'image') {
                    this.$toasted.error('文件格式错误', {duration: 3000})
                    return
                }

                let reader = new FileReader()
                reader.onload = e => {
                    let imgTmp = new Image()
                    imgTmp.src = e.target.result

                    imgTmp.onload = () => {
                        let cvs = document.createElement('canvas')
                        let width, height
                        if (imgTmp.naturalWidth > imgTmp.naturalHeight && imgTmp.naturalWidth > 960) {
                            width = 960
                            height = width * imgTmp.naturalHeight/imgTmp.naturalWidth
                        } else if( imgTmp.naturalWidth <= imgTmp.naturalHeight && imgTmp.naturalHeight > 960) {
                            height = 960 
                            width = height * imgTmp.naturalWidth/imgTmp.naturalHeight
                        } else {
                            width = imgTmp.naturalWidth
                            height = imgTmp.naturalHeight
                        }
                        cvs.width = width
                        cvs.height = height
                        let ctx = cvs.getContext("2d").drawImage(imgTmp, 0, 0, width, height)
                        let newImageData = cvs.toDataURL(mime_type)

                        let file = dataUrlToBlob(newImageData)
                        let formData = new FormData()
                        formData.append('file', file, filename)

                        this.$http.post('/article/'+id+'/picture', formData, {
                            before: () => {
                                this.$Progress.start()
                            },
                            emulateJSON: true
                        }).then(response=>{
                            this.update()
                            this.$toasted.success(response.body._msg, {duration: 3000})
                            this.$Progress.finish()
                        }, response=>{
                            this.$toasted.error(response.body._msg, {duration: 3000})
                            this.$Progress.fail()
                        })
                    }
                }
                reader.readAsDataURL(uploadFile.files[0]);
            }
        })
    },
    methods: {
        update: function(){
            let id = this.$route.params.id
            this.$http.get('/article/'+id+'/picture', {
                before: () => {
                    this.$Progress.start()
                }
            }).then(response=>{
                this.photos = response.body.photos
                this.$Progress.finish()
            }, response=>{})
        },
        showPicture: function(path) {
            Vex.closeAll()
            this.modalPicturePath = path

            setTimeout(() => {
                let modalPicture = document.querySelector('.modal-picture-wrapper .modal-picture')

                this.modalPictureVex = Vex.open({
                    overlayCloseOnClick: true,
                    className: 'vex-theme-picture',
                    showCloseButton: false,
                    beforeClose: function () {
                        document.querySelector('.modal-picture-wrapper').append(modalPicture)
                    }
                }) 

                this.modalPictureVex.contentEl.append(modalPicture)
            })           
        },
        descriptionPicture: function(e) {
            let el = e.target
            this.pid = el.dataset.pid
            this.description = el.dataset.description

            Vex.closeAll()

            setTimeout(() => {
                let modalDescription = document.querySelector('.modal-description-wrapper .modal-description')

                let modalDescriptionVex = Vex.open({
                    overlayCloseOnClick: true,
                    showCloseButton: false,
                    className: 'vex-theme-form',
                    beforeClose: function () {
                        document.querySelector('.modal-description-wrapper').append(modalDescription)
                    }
                }) 

                modalDescriptionVex.contentEl.append(modalDescription)
            })           
        },
        deletePicture: function(pid) {
            let id = this.$route.params.id
            this.$http.delete('/article/'+id+'/picture/'+pid, {
                before: () => {
                    this.$Progress.start()
                }
            }).then(response=>{
                this.$toasted.success(response.body._msg, {duration: 3000})
                this.$Progress.finish()
                this.update()
            }, response=>{})
        },
        submit: function(e) {
            let form = e.target
            let action = form.getAttribute('action')
            let data = new FormData(form)

            this.$http.post(action, data, {
                before: function() {
                    this.$Progress.start()
                },
            }).then(response=>{
                Vex.closeAll()
                this.$toasted.success(response.body._msg, {duration: 3000})
                this.$Progress.finish()
                this.update()
            }, response=>{
                Vex.closeAll()
                this.$toasted.error(response.body._msg, {duration: 3000})
                this.$Progress.fail()
            })
        }
    }
}
</script>
