import {createRouter,createWebHistory}
from 'vue-router'

import LoginPage from
'../pages/LoginPage.vue'

import DashboardPage from
'../pages/DashboardPage.vue'

const routes=[

{
path:'/supplier/login',
component:LoginPage
},

{
path:'/supplier/dashboard',
component:DashboardPage
}

]

export default createRouter({

history:createWebHistory(),
routes

})