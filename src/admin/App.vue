<template>
  <div class="wrap">
    <b-alert
      :show="showAlert"
      dismissible
      :variant="alertType"
      @input="closeAlert"
    >{{alertMsg}}</b-alert>
    <div class="row">
    
    <!-- Sidebar -->
    <Sidebar/>
    <!-- End of Sidebar -->
    <div class="col-md-9">
    <!-- Content Wrapper -->
    <router-view></router-view>
    </div>
  </div>
  </div>
</template>
<script>
import Sidebar from './components/Sidebar';
import Vue from 'vue'
import { mapGetters } from 'vuex';

export default {
  data() {
      return {
        name: '',
      };
  },
  components: {
    Sidebar
  },
  created() {
    this.name = Vue.prototype.$name;
  },
  computed: {
    ...mapGetters( [ 'alertType', 'alertMsg', 'showAlert' ] ),
   getCurrentYear: function(){
     return new Date().getFullYear()
   }
  },
   methods: {
     closeAlert(){
      this.$store.dispatch( 'closeAlert')
    }
   }
}
</script>
<style>
  ul{
    font-size: 12px;
  }
  .header-top{
    display: flex;
    flex-flow: row nowrap;
    justify-content: space-between;
  }
  .header-top__btn{
    display: block;
    margin: auto 0;
  }
</style>
