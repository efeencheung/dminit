<template>
    <div class="login">
        <div class="logo">
            <span class="text-logo">格天内容管理系统</span>
        </div>
        <div class="content">
            <form class="login-form" action="/login" method="post" v-on:submit.prevent="submit">
                <div class="form-title">
                    <span class="form-title"><strong>欢迎！</strong> 请登录后操作</span>
                </div>
                <div class="form-group">
                    <input class="form-control" type="text" autocomplete="new-password" placeholder="请输入用户名" name="_username" /> 
                </div>
                <div class="form-group">
                    <input class="form-control" type="password" autocomplete="new-password" placeholder="请输入密码" name="_password" /> 
                </div>
                <div class="form-actions">
                    <button id="submit-btn" type="submit" class="btn red btn-block uppercase">登录</button>
                </div>
            </form>
        </div>
        <div class="copyright hide"> 2013-2017 © 北京电马网络科技中心. </div>
    </div>
</template>

<script>
export default {
    name: 'login',
    data: function() {
        return {}
    },
    methods: {
        submit: function(e) {
            let form = e.target
            let action = form.getAttribute('action')
            let data = new FormData(form)
            let submitBtn = form.querySelector('button[type=submit]')
            this.$http.post(action, data, {
                before: function(){
                    this.$Progress.start()
                    submitBtn.setAttribute("disabled", "disabled")
                },
                emulateJSON: true
            }).then(response => {
                submitBtn.removeAttribute("disabled")

                this.$http.get('/loggeduser').then(response=>{
                    let user = response.body.data
                    this.$store.commit('login', user)
                }, response=>{})

                this.$toasted.success(response.body._msg, {duration: 3000})
                this.$Progress.finish()
                this.$router.push('/')
            }, response => {
                submitBtn.removeAttribute("disabled")
                this.$toasted.error(response.body._msg, {duration: 3000})
                this.$Progress.fail()
            })
        }
    }
}
</script>

<style>
.login {
    background-color: #5c97bd; 
    width: 100%;
    height: 100%;
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    z-index: 99;
}

.login span.text-logo {
    font-family: "microsoft yahei";
    color: #edf4f8 !important; 
}

.login .logo {
    margin: 0 auto;
    margin-top: 100px;
    padding: 15px;
    font-size: 24px;
    text-align: center; 
}

.login .content {
    width: 400px;
    margin: 40px auto 40px auto; 
}

.login .form-title {
    margin-bottom: 20px; 
    color: #edf4f8;
    font-size: 19px;
    font-weight: 400 !important; 
}

.login .content .form-control {
    border: none;
    background-color: #6ba3c8;
    border: 1px solid #6ba3c8;
    height: 43px;
    color: #d9ecf9; 
}

.login .content .form-control:focus, .login .content .form-control:active {
    border: 1px solid #83b8db; 
}

.login .content .form-control::-moz-placeholder {
    color: #d9ecf9;
    opacity: 1; 
}

.login .content .form-control:-ms-input-placeholder {
    color: #d9ecf9; 
}

.login .content .form-control::-webkit-input-placeholder {
    color: #d9ecf9; 
}

.login .content .form-title {
    font-weight: 300;
    margin-bottom: 25px; 
}

.login .content .form-actions {
    clear: both;
    border: 0px;
    padding: 0px 30px 25px 30px;
    margin-left: -30px;
    margin-right: -30px; 
}

.form-actions .forget-password-block {
    padding-top: 7px; 
}

.login .forget-password {
    font-size: 14px; 
}

.login .content .form-actions .checkbox {
    margin-top: 8px;
    display: inline-block; 
}

.login .content .form-actions .btn {
    margin-top: 1px; 
}

.login .btn {
    background-color: #5995bb;
    border: 1px solid #72a9cc;
    color: #8fc4e5;
    font-weight: 600;
    padding: 10px 25px !important; 
}

.login .btn:hover {
    border: 1px solid #90bbd7;
    background-color: #5995bb;
    color: #8fc4e5; 
}

.login .btn-default {
    background-color: #5995bb;
    border: 1px solid #72a9cc;
    color: #8fc4e5;
    font-weight: 600;
    padding: 10px 25px !important; 
}

.login .btn-default:hover {
    border: 1px solid #90bbd7;
    background-color: #5995bb;
    color: #8fc4e5; 
}

.login .content .forget-password {
    color: #d7eaf7;
    font-size: 15px; 
}

.login .content .rememberme {
    margin-top: 8px; 
}

.login .content .check {
    color: #c9dce9 !important; 
}

.login .content .create-account {
    text-align: center;
    margin-top: 20px; 
}

.login .content .create-account p a {
    font-weight: 300;
    font-size: 16px;
    color: #ffffff; 
}

.login .content .create-account a {
    display: inline-block;
    margin-top: 5px; 
}

.login .copyright {
    text-align: center;
    margin: 10px auto 30px 0;
    padding: 10px;
    color: #c9dce9;
    font-size: 13px; 
}
</style>

