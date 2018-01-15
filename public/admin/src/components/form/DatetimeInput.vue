<template>
    <div class="input-group input-large">
        <input type="text" v-bind:name="name" class="form-control" v-bind:value="value" v-bind:placeholder="placeholder"/>
        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
    </div>
</template>

<script>
import 'flatpickr/dist/themes/light.css'
import Flatpickr from 'flatpickr'
import Mandarin from 'flatpickr/dist/l10n/zh'

export default {
    props: {
        name: {
            type: String,
            required: true
        }, 
        value: {
            type: String,
            required: false
        }, 
        placeholder: {
            type: String,
            required: false,
            default: ''
        }, 
    },
    mounted: function() {
        /* 初始化DateTime控件 */
        let now = new Date()
        let datetimeInput = this.$el.querySelector("input")
        new Flatpickr(datetimeInput, {
            locale: Mandarin.zh,
            defaultDate: now,
            enableTime: true
        })
    }
}
    
</script>
