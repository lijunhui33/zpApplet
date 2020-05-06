import Vue from 'vue'
import App from './App'
import navigationCustom from "./components/struggler-navigationCustom/navigation-custom.vue"

Vue.config.productionTip = false
Vue.component("navigationCustom",navigationCustom)

App.mpType = 'app'

const app = new Vue({
    ...App
})
app.$mount()