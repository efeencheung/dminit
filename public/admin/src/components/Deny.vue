<template>
    <div class="page-error row">
        <div class="col-md-12 page-code">
            <div class="number font-red"> 403 </div>
            <div class="details">
                <h3>Oops! 未授权的访问.</h3>
                <p> 您没有访问该资源的权限，请联系系统管理员.
                <br/>
                <a href="javascript:history.go(-2)"> 返回上一页 </a> . </p>
            </div>
        </div>
    </div>
</template>

<style>
.page-error {
    background-color: #fafafa; 
    width: 100%;
    height: 100%;
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    z-index: 99;
    font-family: "Open Sans", sans-serif;
}

.page-code {
    margin-top: 100px;
    text-align: center;
}

.page-code .number {
    position: relative;
    top: 35px;
    display: inline-block;
    letter-spacing: -10px;
    margin-top: 0px;
    margin-bottom: 10px;
    line-height: 128px;
    font-size: 128px;
    font-weight: 300;
    color: #7bbbd6;
    text-align: right;
}

.font-red {
    color: #e7505a !important;
}

.page-code .details {
    margin-left: 40px;
    display: inline-block;
    padding-top: 0px;
    text-align: left;
}

.page-code .details h3 {
    font-weight: 300;
}
</style>
