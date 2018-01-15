<template>
    <div id="dashboard">
    </div>
</template>

<script>
export default {
    name: 'dashboard',
    created: function () {
        this.$router.push('/user/list')
    }
}
</script>
