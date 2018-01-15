class FileInput {
    constructor(element) {
        this.el = document.querySelector(element);
        this.label = this.el.nextElementSibling;

        /* add event */
        this.el.addEventListener('change', e => {
            let filename = '';
            filename = e.target.value.split( '\\' ).pop();

            this.label.innerHTML = filename;
        });
    }
}

export default FileInput;
