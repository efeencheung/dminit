<template>
	<div id="sidebar" class="page-sidebar-wrapper">
		<div class="page-sidebar navbar-collapse collapse">
			<ul class="page-sidebar-menu page-header-fixed">
                <li v-for="(item, key) in items" class="nav-item" v-bind:data-key="key"
                    v-bind:class="{ active: item.active, start: key=='dashboard', last: key=='user', open: item.open }">
                    <router-link v-if="item.route" :to="item.route" class="nav-link nav-toggle" v-on:click="toggle">
                        <i v-bind:class="item.icon"></i> <span class="title"> {{ item.name }} </span>
                        <span v-bind:class="{ open: item.open, active: item.active, arrow:item.items }"></span>
					</router-link> 
                    <a v-else href="javascript:;" class="nav-link nav-toggle" v-on:click="toggle">
                        <i v-bind:class="item.icon"></i> <span class="title"> {{ item.name }} </span>
                        <span v-bind:class="{ open: item.open, active: item.active, arrow:item.items }"></span>
					</a> 
                    <ul v-if="item.items" class="sub-menu menu_level_1" v-bind:style="{ display: item.display }"> 
						<li v-for="(itm, idx) in item.items" class="nav-item" v-bind:class="{ start: idx==0, last: idx==item.items.length-1 }">
                            <router-link :to="{ path: itm.route }" class="nav-link nav-toggle">
                                <span class="title">{{ itm.name }}</span>
							</router-link> 
						</li>
					</ul>
				</li>
			</ul>
		</div>
	</div>      
</template>

<script>
export default {
    data: function() {
        return {
            items: {
                user: { 
                    name: "用户", 
                    route: null, 
                    icon: "icon-user",
                    active: false,
                    open: false,
                    display: "none",
                    items: [
                        { name: "用户列表", route: "/user/list" },
                        { name: "用户添加", route: "/user/add" }
                    ] 
                },
                content: { 
                    name: "内容", 
                    route: null, 
                    icon: "icon-note",
                    active: false,
                    open: false,
                    display: "none",
                    items: [
                        { name: "标签管理", route: "/tag/list" },
                        { name: "图文资讯", route: "/article/list" },
                        { name: "图片资讯", route: "/picture/list" },
                        { name: "视频资讯", route: "/video/list" },
                    ] 
                }
            }
        }
    },
    mounted: function(){
        this.updateActive();
        this.$router.afterEach(route => {
            this.updateActive();
        })
    },
    methods: {
        toggle: function(e){
            let menu = e.currentTarget.parentNode;
            let currentKey = menu.getAttribute("data-key");
            let subMenu = menu.querySelector('ul.sub-menu');
            if (!subMenu) {
                return;
            }

            for (let key in this.items) {
                if (currentKey == key && this.items[key].open == false) {
                    this.items[key].open = true;
                    this.items[key].display="block";
                } else {
                    this.items[key].open = false;
                    this.items[key].display="none";
                }
            }
        },
        updateActive: function() {
            let currentRoute = this.$route.path
            for (let key in this.items) {
                this.items[key].active = false
                if (this.items[key].route && this.items[key].route == currentRoute ) {
                    this.items[key].active = true
                } else if (!this.items[key].route) {
                    let children = this.items[key].items
                    for (var i = 0, len = children.length; i < len; i++) {
                        if (children[i].route == currentRoute) {
                            this.items[key].active = true
                        }
                    }
                }
            }
        }
    },
    name: 'sidebar'
}
</script>
