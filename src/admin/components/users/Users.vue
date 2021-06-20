<template>
  <div>
    <div class="header-top">
      <h1 class="h1">Пользователи</h1>
    </div>
    <b-table striped hover 
    :sort-by.sync="sortBy"
    :sort-desc.sync="sortDesc"
    :current-page="currentPage"
    :items="users" :fields="fields"
    :busy="loading"
    responsive="sm">
     <template #table-busy>
        <div class="text-center my-2">
          <b-spinner class="align-middle"></b-spinner>
          <strong>Идет загрузка...</strong>
        </div>
      </template>
      <!-- A custom formatted column -->
      <template v-slot:cell(name)="data">
        <router-link class="" 
        :to="`/users/${data.item.id}`">{{ data.value }}</router-link>
      </template>
      <!-- A virtual column -->
       <template v-slot:cell(delete)="data" v-if="role === 'admin' || this.role === 'superadmin'">
        <font-awesome-icon icon="trash" class="text-danger" 
         role="button" @click="deleteUser(data.item.id, data.item.photo)"/>
      </template>
    </b-table>
    <b-pagination  v-if="usersCount > 10"
      v-model="currentPage"
      :total-rows="usersCount"
      :per-page="perPage"
      class="mt-4"
      aria-controls="my-table"
    ></b-pagination>
  </div>
</template>
<script>
import Vue from "vue";
import { mapGetters } from 'vuex';

export default {
    data() {
      return {
        sortBy: 'name',
        currentPage: 1,
        sortDesc: false,
        fields: [
          {
            key: 'name',
            label: 'Имя',
            sortable: true,
          },
          {
            key: 'email',
            sortable: true
          },
          {
            key: 'status',
            label: 'Роль',
          },
          {
            key: 'delete',
            label: 'Действие',
            tdClass: 'text-center'
          },
        ],
        loading: true,
        role: "user",
      };
  },
  created() {
    this.role = Vue.prototype.$role;
    this.$store.dispatch( 'users/getUsers', {
      page: 1,
      sortDesc: false,
      sortBy: 'name'
    } )
    .then(
      result => {
        this.loading = false;
      },
      error => {
        this.loading = false;
      }
    );
  },
  methods: {
    deleteUser(id, photo){
      this.loading = true;
      this.$store.dispatch( 'users/deleteUser', {
        id,
        photo
      } )
      .then(
        result => {
          
          this.$store.dispatch( 'users/getUsers', {
            page: this.currentPage,
            sortDesc: this.sortDesc,
            sortBy: this.sortBy
          })
          this.loading = false;
          
        },
        error => {
          console.log(error);
          this.loading = false;
        }
      );
    },
    closeAlert(){
      this.$store.dispatch( 'closeAlert')
    }
  },
  computed: {
    ...mapGetters( 'users', [ 'users', 'usersCount', 'perPage' ] ),
    ...mapGetters( [ 'alertType', 'alertMsg', 'showAlert' ] ),
  },
  watch: {
    currentPage: function (newPage, oldPage) {
      this.loading = true;
      this.$store.dispatch( 'users/getUsers', {
          page: newPage,
          sortDesc: this.sortDesc,
          sortBy:  this.sortBy
      })
        .then(
        result => {
          this.loading = false;
          
        },
        error => {
          this.loading = false;
        }
      );
    },
    sortDesc: function (newDescValue, old) {
      this.loading = true;
      this.$store.dispatch( 'users/getUsers', {
        page: this.currentPage,
        sortDesc:  newDescValue,
        sortBy: this.sortBy
      })
      .then(
      result => {
        this.loading = false;
        
      },
      error => {
        this.loading = false;
      }
    );
    },
    sortBy: function (newsortBy, old) {
      this.loading = true;
      this.$store.dispatch( 'users/getUsers', {
        page: this.currentPage,
        sortDesc: this.sortDesc,
        sortBy: newsortBy
      })
      .then(
      result => {
        this.loading = false;
        
      },
      error => {
        this.loading = false;
      }
    );
    },
  }
}
</script>