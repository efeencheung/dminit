<template>
    <div>
        <img class="picture-preview inputfile-preview" v-bind:src="previewImgPath" v-bind:style="{ display: previewDisplay?'block':'none' }"/>
        <input v-bind:id="fileId" class="inputfile" type="file" v-bind:name="name" accept="image/*" v-on:change="change" />
        <label v-bind:for="fileId">选择图片</label>
    </div>
</template>

<script>
export default {
    name: 'image-input',
    props: {
        name: {
            type: String,
            required: true,
        }, 
        resize: {
            type: Number,
            required: false,
            default: 960 
        },
        previewImgPath: {
            type: String,
            required: false,
            default: require('../../assets/img/default-image.jpg')
        },
        previewDisplay: {
            type: Boolean,
            required: false,
            default: false
        }
    },
    data: function() {
        return {}
    },
    computed: {
        fileId: function() {
            return 'picture-file-' + new Date().getTime()
        }
    },
    methods: {
        change: function(e) {
            let el = this.$el.querySelector('input.inputfile')
            let label = el.nextElementSibling
            let preview = el.previousElementSibling

            /* 显示上传文件的原始文件名 */
            let filename = '';
            filename = e.target.value.split( '\\' ).pop();
            label.innerHTML = filename;

            if (el.files != null && el.files[0] != null) {
                let mime_type = el.files[0].type;
                /* Gif 格式不压缩 */
                if (mime_type == "image/gif") {
                    return;
                }

                /* 限制文件格式 */
                if (mime_type.substr(0, 5) != 'image') {
                    label.innerHTML = '文件格式错误';
                    return;
                }

                let reader = new FileReader();
                reader.readAsDataURL(el.files[0]);
                reader.onload = e => {
                    let imgTmp = new Image();
                    imgTmp.src = e.target.result;

                    imgTmp.onload = () => {
                        let cvs = document.createElement('canvas');
                        let width, height;
                        if (imgTmp.naturalWidth > imgTmp.naturalHeight && imgTmp.naturalWidth > this.resize) {
                            width = this.resize;
                            height = width * imgTmp.naturalHeight/imgTmp.naturalWidth;
                        } else if( imgTmp.naturalWidth <= imgTmp.naturalHeight && imgTmp.naturalHeight > this.resize) {
                            height = this.resize;
                            width = height * imgTmp.naturalWidth/imgTmp.naturalHeight;
                        } else {
                            width = imgTmp.naturalWidth;
                            height = imgTmp.naturalHeight;
                        }
                        cvs.width = width;
                        cvs.height = height;
                        let ctx = cvs.getContext("2d").drawImage(imgTmp, 0, 0, width, height);
                        let newImageData = cvs.toDataURL(mime_type);

                        preview.src = newImageData
                        preview.style.display = 'block'
                    }
                }
            }
        }
    }
}
</script>
