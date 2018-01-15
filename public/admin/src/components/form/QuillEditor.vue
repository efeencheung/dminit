<template>
    <div class="editor-container" v-bind:class="{ fullscreen: fullscreen }"> 
        <div class="toolbar">
            <select class="font-size ql-size ql-select">
                <option value="small">小号</option>
                <option selected>字号</option>
                <option value="large">中号</option>
                <option value="huge">大号</option>
            </select>
            <button type="button" class="ql-clean btn btn-default btn-sm"><i class="fa fa-eraser"></i></button>
            <button class="ql-blockquote btn btn-default btn-sm" type="button"><i class="fa fa-quote-right"></i></button>
            <div class="btn-group">
                <button class="ql-bold btn btn-default btn-sm"><i class="fa fa-bold"></i></button>
                <button class="ql-italic btn btn-default btn-sm"><i class="fa fa-italic"></i></button>
                <button class="ql-underline btn btn-default btn-sm"><i class="fa fa-underline"></i></button>
            </div>
            <div class="btn-group">
                <button v-bind:value="colors.hex" v-bind:style="{ color: colors.hex }" 
                    type="button" class="ql-color btn btn-default btn-sm">
                    <i class="fa fa-font"></i>
                </button>
                <button class="color-picker btn btn-default btn-sm" type="button">
                    <i class="fa fa-caret-down"></i>
                </button>
            </div>
            <div class="btn-group">
                <button class="ql-list btn btn-default btn-sm" type="button" value="ordered">
                    <i class="fa fa-list-ol"></i>
                </button>
                <button class="ql-list btn btn-default btn-sm" type="button" value="bullet">
                    <i class="fa fa-list-ul"></i>
                </button>
            </div>
            <div class="btn-group">
                <button class="ql-align btn btn-default btn-sm" type="button" value="center">
                    <i class="fa fa-align-center"></i>
                </button>
                <button class="ql-align btn btn-default btn-sm" type="button">
                    <i class="fa fa-align-left"></i>
                </button>
                <button class="ql-align btn btn-default btn-sm" type="button" value="right">
                    <i class="fa fa-align-right"></i>
                </button>
                <button class="ql-align btn btn-default btn-sm" type="button" value="justify">
                    <i class="fa fa-align-justify"></i>
                </button>
            </div>
            <div class="btn-group">
                <button class="ql-link btn btn-default btn-sm" type="button">
                    <i class="fa fa-link"></i>
                </button>
                <button class="ql-image btn btn-default btn-sm" type="button">
                    <i class="fa fa-picture-o"></i>
                </button>
                <button class="ql-video btn btn-default btn-sm" type="button">
                    <i class="fa fa-film"></i>
                </button>
            </div>
            <button class="fullscreen-toggle-btn btn btn-default btn-sm" type="button">
                <i v-if="fullscreen" class="fa fa-compress"></i>
                <i v-else="fullscreen" class="fa fa-expand"></i>
            </button>
            <div class="color-picker-container">
                <compact-picker v-model="colors" />
            </div>
        </div>
        <div class="editor"></div>
        <input v-bind:name="name" type="hidden" v-bind:value="cvalue">
    </div>
</template>

<script>
import 'quill/dist/quill.core.css' 
import Quill from 'quill'
import Delta from 'quill-delta'
import Vex from 'vex-js' 
import Drop from 'tether-drop' 
import { Compact } from 'vue-color'
import Screenfull from 'screenfull';

export default {
    props: ['value', 'name'],
    data: function() {
        return {
            fullscreen: false,
            colors: {
                hex: '#ff0000',
                hsl: { h: 0, s: 1, l: 0.5, a: 1 },
                hsv: { h: 0, s: 1, v: 1, a: 1 },
                rgba: { r: 255, g: 0, b: 0, a: 1 },
                a: 1
            },
            cvalue: this.value,
        }
    },
    mounted: function() {
        let editor = this.$el.querySelector('.editor')
        let toolbar = this.$el.querySelector('.toolbar')
        const quill = new Quill(editor, {
            modules: {
                toolbar: { 
                    container: toolbar,
                    handlers: {
                        link: function(value) {
                            Vex.dialog.prompt({
                                message: '请输入一个有效的 Url 地址',
                                callback: function (href) {
                                    quill.format('link', href);
                                }
                            })
                        },
                        video: function() {
                            Vex.dialog.prompt({
                                message: '请输入一个有效的视频地址',
                                callback: function (src) {
                                    if (!src) {
                                       return false 
                                    }
                                    var range = quill.getSelection(true)
                                    quill.updateContents(new Delta()
                                        .retain(range.index)
                                        .delete(range.length)
                                        .insert({ video: src }, { width: 914, height: 534 })
                                    , 'user')
                                }
                            })
                        },
                        image: function() {
                            let fileInput = document.createElement('input')
                            fileInput.setAttribute('type', 'file')
                            fileInput.setAttribute('accept', 'image/png, image/gif, image/jpeg, image/bmp, image/x-icon');
                            fileInput.classList.add('ql-image')
                            fileInput.style.display='none'
                            this.container.appendChild(fileInput)
                            fileInput.click()
                            fileInput.addEventListener('change', () => {
                                if (fileInput.files != null && fileInput.files[0] != null) {
                                    var mime_type = fileInput.files[0].type
                                    var reader = new FileReader()
                                    reader.onload = (e) => {
                                        var imgTemp = new Image()
                                        imgTemp.src = e.target.result

                                        imgTemp.onload = function(){
                                            var cvs = document.createElement('canvas')
                                            var width, height
                                            if (imgTemp.naturalWidth > imgTemp.naturalHeight && imgTemp.naturalWidth > 1280) {
                                                width = 1280
                                                height = width * imgTemp.naturalHeight/imgTemp.naturalWidth
                                            } else if( imgTemp.naturalWidth <= imgTemp.naturalHeight && imgTemp.naturalHeight > 1280) {
                                                height = 1280
                                                width = height * imgTemp.naturalWidth/imgTemp.naturalHeight
                                            } else {
                                                width = imgTemp.naturalWidth
                                                height = imgTemp.naturalHeight
                                            }
                                            cvs.width = width
                                            cvs.height = height
                                            var ctx = cvs.getContext("2d").drawImage(imgTemp, 0, 0, width, height)
                                            var newImageData = cvs.toDataURL(mime_type)

                                            var widthDisplay = '100%'
                                            if (mime_type == "image/gif") {
                                                widthDisplay = width
                                            }
                                            var range = quill.getSelection(true)
                                            quill.updateContents(new Delta()
                                                .retain(range.index)
                                                .delete(range.length)
                                                .insert({ image: newImageData }, { width: widthDisplay })
                                            , 'user')
                                            fileInput.value = ""
                                        }
                                    }
                                    reader.readAsDataURL(fileInput.files[0])
                                }
                            })
                        }
                    }
                }
            }
        })

        let fullscreenButton = toolbar.querySelector('.fullscreen-toggle-btn')
        fullscreenButton.addEventListener('click', () => {
            let container = this.$el
            if (this.fullscreen) {
                container.style.height = '300px'
                this.fullscreen = false
            } else {
                container.style.height = 'auto'
                this.fullscreen = true
            }
        })

        this.$watch('value', () => {
            quill.clipboard.dangerouslyPasteHTML(this.value)
        })

        quill.on('text-change', (delta, oldDelta, source) => {
            this.cvalue = editor.querySelector('.ql-editor').innerHTML
        });

        let drop = new Drop({
            target: toolbar.querySelector('.color-picker'),
            content: toolbar.querySelector('.color-picker-container'),
            position: 'bottom left',
            openOn: 'click',
            hoverOpenDelay: 0,
            hoverCloseDelay: 50
        })
    },
    components: {
        'compact-picker': Compact
    },
    name: 'quill-editor'
}
</script>

<style>
.toolbar {
    padding: 5px;
    border: 1px solid #c2cad8;
    background-color: #f5f5f5;
}

.editor {
    border: 1px solid #c2cad8;
    border-top: 0;
}

.toolbar .btn-sm, .toolbar .btn-group-sm > .btn {
    line-height: 1.6;
} 

.drop-element {
    opacity: 0;
}

.drop-element.drop-open-transitionend {
    display: block;
}

.drop-element.drop-after-open {
    opacity: 1;
}

button#color-picker {
    padding: 5px !important;
}

.ql-editor blockquote {
    padding: 0;
    margin: 0;
    font-size: 17.5px;
    border-left: 4px solid #ccc;
    margin-bottom: 5px;
    margin-top: 5px;
    padding-left: 16px;
}

.ql-select {
    background-color: #fff;
    padding: 5px 20px 4px 12px;
    vertical-align: middle;
    border-color: #ccc;
}

.editor-container {
    height: 300px;
}

.editor-container .ql-container {
    height: 256px;
    font-size: 16px;
}

.editor-container.fullscreen {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 1050;
    width: 100% !important;
    background: #fff;
}

.editor-container.fullscreen .ql-editor {
    width: 786px;
    margin: 0 auto;
}

.editor-container.fullscreen .ql-container {
    height: 100%;
    padding-bottom: 50px;
}

.select-open, .drop-open {
    z-index: 1051;
}
</style>
