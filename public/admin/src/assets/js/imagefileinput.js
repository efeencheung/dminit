class ImageFileInput {
    constructor(element, resize) {
        this.el = document.querySelector(element);
        this.label = this.el.nextElementSibling;
        this.preview = this.el.previousElementSibling;

        /* 添加事件 */
        this.el.addEventListener('change', e => {
            /* 显示上传文件的原始文件名 */
            let filename = '';
            filename = e.target.value.split( '\\' ).pop();
            this.label.innerHTML = filename;

            /* 图片压缩 */
            if (this.el.files != null && this.el.files[0] != null) {
                let mime_type = this.el.files[0].type;
                /* Gif 格式不压缩 */
                if (mime_type == "image/gif") {
                    return;
                }

                /* 限制文件格式 */
                if (mime_type.substr(0, 5) != 'image') {
                    this.label.innerHTML = '文件格式错误';
                    return;
                }

                let reader = new FileReader();
                reader.onload = e => {
                    let imgTmp = new Image();
                    imgTmp.src = e.target.result;

                    imgTmp.onload = () => {
                        let cvs = document.createElement('canvas');
                        let width, height;
                        if (imgTmp.naturalWidth > imgTmp.naturalHeight && imgTmp.naturalWidth > resize) {
                            width = resize;
                            height = width * imgTmp.naturalHeight/imgTmp.naturalWidth;
                        } else if( imgTmp.naturalWidth <= imgTmp.naturalHeight && imgTmp.naturalHeight > resize) {
                            height = resize;
                            width = height * imgTmp.naturalWidth/imgTmp.naturalHeight;
                        } else {
                            width = imgTmp.naturalWidth;
                            height = imgTmp.naturalHeight;
                        }
                        cvs.width = width;
                        cvs.height = height;
                        let ctx = cvs.getContext("2d").drawImage(imgTmp, 0, 0, width, height);
                        let newImageData = cvs.toDataURL(mime_type);

                        this.preview.src = newImageData;
                        this.preview.style.display = 'block';
                    }
                }
                reader.readAsDataURL(this.el.files[0]);
            }
        });
    }
}

export default ImageFileInput;
