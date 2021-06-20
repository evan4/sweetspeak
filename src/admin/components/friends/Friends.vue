<template>
  <div>
    <div class="header-top">
      <h1 class="h1">Список друзей</h1>
    </div>

    <div v-if="friendsRequests.length">
      <h2>Запросы в друзья</h2>
      <ul class="list-group">
        <li class="list-group-item list-group-item-primary mb-2"
        v-for="request in friendsRequests" :key="request.id">
        <p style="margin: 0 0 2px 0;">{{request.user}}</p>
        <small class="pb-1">Отправлен {{ dateFormat(request.created_at) }}</small>
        <b-button-group>
          <b-button variant="success" class="p-2" @click="approveRequest(request.id)">Принять</b-button>
          <b-button variant="danger" class="p-2" @click="rejectRequest(request.id)">Отклонить</b-button>
        </b-button-group>
        </li>
      </ul>
    </div>
    

    <b-table striped hover
    :sort-by.sync="sortBy"
    :sort-desc.sync="sortDesc"
    :current-page="currentPage"
    :items="friends" :fields="fields"
    :busy="loading"
    responsive="sm">
     <template #table-busy>
        <div class="text-center my-2">
          <b-spinner class="align-middle"></b-spinner>
          <strong>Идет загрузка...</strong>
        </div>
      </template>
      <!-- A custom formatted column -->
      <template v-slot:cell(user)="data">
         <a target="_blabk" :href="`/authors/${data.item.requester}`">{{ data.value }}</a>
      </template>
      <!-- A virtual column -->
       <template v-slot:cell(delete)="data">
        <font-awesome-icon icon="trash" class="text-danger" 
         role="button" @click="deleteFriend(data.item.id)"/>
      </template>
    </b-table>
    <b-pagination  v-if="friendsCount > 10"
      v-model="currentPage"
      :total-rows="friendsCount"
      :per-page="perPage"
      class="mt-4"
      aria-controls="my-table"
    ></b-pagination>
    <p v-if="!friendsCount && !loading">У вас нет друзей</p>
  </div>
</template>
<script>
import Vue from "vue";
import { mapGetters } from 'vuex';

export default {
    data() {
      return {
        sortBy: 'requester',
        currentPage: 1,
        sortDesc: false,
        fields: [
          {
            key: 'user',
            label: 'Имя',
            sortable: true
          },
          {
            key: 'delete',
            label: 'Удалить',
            thStyle: 'width: 50px',
            tdClass: 'text-center'
          },
        ],
        loading: true,
      };
  },
  created() {
    this.$store.dispatch( 'friends/getFriends', {
      page: 1,
      sortDesc: false,
      sortBy: 'requester'
    } )
    .then(
      result => {
        this.loading = false;
        this.$store.dispatch( 'friends/getFriendsRequests')
      },
      error => {
        this.loading = false;
      }
    );

  },
  methods: {
    dateFormat(date) {
      return new Date(date).toLocaleDateString();
    },
    approveRequest(id){
      const requestId = !Number.isNaN(Number(id)) ? +id : null;
      let formData = new FormData();
        formData.append('id', requestId);
       if(requestId){
        Vue.http.post('friends/accept', formData)
          .then((response) => {
            return response.json();
          })
          .then(response => {
            this.$store.dispatch( 'friends/getFriends', {
                page: 1,
                sortDesc: false,
                sortBy: 'requester'
              } )
              .then(
                result => {
                  this.$store.dispatch( 'friends/getFriends', {
                    page: 1,
                    sortDesc: false,
                    sortBy: 'requester'
                  } )
                  .then(
                    result => {
                      this.loading = false;
                      this.$store.dispatch( 'friends/getFriendsRequests')
                    },
                    error => {
                      this.loading = false;
                      this.$store.dispatch('openAlert',{
                          alertType : 'danger',
                          alertMsg : 'Произошла ошибка. Попробуйте еще раз'
                        }, { root: true });
                        reject(response)
                    }
                  );
                },
                error => {
                  this.loading = false;
                }
              );
          }, response => {
            this.$store.dispatch('openAlert',{
              alertType : 'danger',
              alertMsg : 'Произошла ошибка. Попробуйте еще раз'
            }, { root: true });
            reject(response)
          });
      }
    },
    rejectRequest(id){
      const requestId = !Number.isNaN(Number(id)) ? +id : null;

      if(requestId){
        Vue.http.delete(`friends/${requestId}`)
          .then((response) => {
            return response.json();
          })
          .then(response => {
               this.$store.dispatch( 'friends/getFriendsRequests')
          }, response => {
            this.$store.dispatch('openAlert',{
              alertType : 'danger',
              alertMsg : 'Произошла ошибка. Попробуйте еще раз'
            }, { root: true });
            reject(response)
          });
      }
    },
    deleteFriend(id, photo){
      this.loading = true;
      this.$store.dispatch( 'friends/deleteFriend', {
        id,
        photo
      } )
      .then(
        result => {
          
          this.$store.dispatch( 'friends/getFriends', {
            page: this.currentPage,
            sortDesc: this.sortDesc,
            sortBy: this.sortBy
          })
          this.loading = false;
          
        },
        error => {
          console.log(error);
          this.$store.dispatch('openAlert',{
              alertType : 'danger',
              alertMsg : 'Произошла ошибка. Попробуйте еще раз'
            }, { root: true });
           this.loading = false;
        }
      );
    },
    closeAlert(){
      this.$store.dispatch( 'closeAlert')
    }
  },
  computed: {
    ...mapGetters( 'friends', [ 'friends', 'friendsCount', 'friendsRequests', 'perPage' ] ),
  },
  watch: {
    currentPage: function (newPage, oldPage) {
      this.loading = true;
      this.$store.dispatch( 'friends/getFriends', {
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
      this.$store.dispatch( 'friends/getFriends', {
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
      this.$store.dispatch( 'friends/getFriends', {
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