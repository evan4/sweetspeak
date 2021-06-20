import Vue from 'vue';
import VueResource from 'vue-resource';
import BootstrapVue from 'bootstrap-vue';
import './libs/ckeditor.js';

import { library } from '@fortawesome/fontawesome-svg-core'
import { faTrash, faCheck, faComment, faEdit, faUsers, faSitemap, faMailBulk,
  faNewspaper,faChevronCircleLeft, faThumbsDown, faEnvelope, faSignOutAlt, faEyeSlash,
  faPlusCircle } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

library.add(faTrash, faCheck, faComment, faEdit, faUsers, faMailBulk,
  faSitemap, faNewspaper, faChevronCircleLeft, faThumbsDown, faEnvelope, faSignOutAlt,
  faEyeSlash,
  faPlusCircle)

import vSelect from 'vue-select'

Vue.component('v-select', vSelect)

Vue.component('font-awesome-icon', FontAwesomeIcon)
Vue.use( BootstrapVue );
Vue.use(VueResource);

import App from './App.vue';
import router from './router.js';
import store from './store.js';

Vue.config.productionTip = false;
Vue.prototype.$userEmail = document.querySelector("meta[name='user-email']").getAttribute('content');
Vue.http.options.root = "/api/";
Vue.http.interceptors.push(function(request) {

  // modify headers
  /* request.headers.set('X-CSRF-TOKEN', 'TOKEN');
  request.headers.set('Authorization', 'Bearer TOKEN'); */

});

let formData = new FormData();
formData.append('email', Vue.prototype.$userEmail);
fetch("/currentUser", {  
  method: 'post',
  body: formData
})
.then((response) => {
  return response.json();
})
.then( (data) => {
  //Vue.http.headers.common['Authorization'] = `${data.token_type} ${data.access_token}`;
  Vue.prototype.$role = data.user.status;
  Vue.prototype.$id = data.user.id;
  Vue.prototype.$name = data.user.name;
  
  new Vue({
    router,
    store,
    render: (h) => h(App),
  }).$mount('#app');
  
})  
.catch(function (error) {  
  console.log('Request failed', error);  
});
