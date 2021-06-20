<template>
  <div>
    <div class="header-top">
      <h1 class="h1">Список жалоб</h1>
    </div>

    <b-table
      striped
      hover
      :sort-by.sync="sortBy"
      :sort-desc.sync="sortDesc"
      :current-page="currentPage"
      :items="complaints"
      :fields="fields"
      :busy="loading"
      responsive="sm"
    >
      <template #table-busy>
        <div class="text-center my-2">
          <b-spinner class="align-middle"></b-spinner>
          <strong>Идет загрузка...</strong>
        </div>
      </template>
      <!-- A custom formatted column -->
      <template v-slot:cell(author)="data">
        <a target="_blabk" :href="`/authors/${data.item.author_id}`">{{ data.value }}</a>
      </template>
      <template v-slot:cell(users)="data">
        <a target="_blabk" :href="`/authors/${data.item.id}`">{{ data.value }}</a>
      </template>
      
      <template v-slot:cell(created_at)="data">{{dateFormat(data.value)}}</template>
      
      <!-- A virtual column -->
      <template v-slot:cell(delete)="data">
        <font-awesome-icon
          icon="trash"
          class="text-danger"
          role="button"
          @click="deleteComplain(data.item.id)"
        />
      </template>
    </b-table>
    <b-pagination
      v-if="complaintsCount > 10"
      v-model="currentPage"
      :total-rows="complaintsCount"
      :per-page="perPage"
      class="mt-4"
      aria-controls="my-table"
    ></b-pagination>
  </div>
</template>
<script>
import { mapGetters } from "vuex";

export default {
  data() {
    return {
      sortBy: "created_at",
      currentPage: 1,
      sortDesc: true,
      fields: [
        {
          key: "message",
          label: "Жалоба"
        },
        {
          key: "author",
          label: "Автор",
          sortable: true
        },
        {
          key: "users",
          label: "Объект",
          sortable: true
        },
        {
          key: "created_at",
          label: "Дата",
          sortable: true
        },
        {
          key: "delete",
          label: "Удалить",
          tdClass: 'text-center'
        }
      ],
      loading: true
    };
  },
  created() {
    this.$store
      .dispatch("complaints/getComplaints", {
        page: 1,
        sortDesc: false,
        sortBy: "created_at"
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
  methods: {
    dateFormat(date) {
      return new Date(date).toLocaleDateString();
    },
    deleteComplain(id) {
      this.loading = true;
      this.$store.dispatch("complaints/deleteComplaint", id).then(
        result => {
          this.$store.dispatch("complaints/getComplaints", {
            page: this.currentPage,
            sortDesc: this.sortDesc,
            sortBy: this.sortBy
          });
          this.loading = false;
        },
        error => {
          console.log(error);
          this.loading = false;
        }
      );
    }
  },
  computed: {
    ...mapGetters("complaints", ["complaints", "complaintsCount", "perPage"])
  },
  watch: {
    currentPage: function(newPage, oldPage) {
      this.loading = true;
      this.$store
        .dispatch("complaints/getComplaints", {
          page: newPage,
          sortDesc: this.sortDesc,
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
    sortDesc: function(newDescValue, old) {
       this.loading = true;
      this.$store
        .dispatch("complaints/getComplaints", {
          page: this.currentPage,
          sortDesc: newDescValue,
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
    sortBy: function(newsortBy, old) {
       this.loading = true;
      this.$store
        .dispatch("complaints/getComplaints", {
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
    }
  }
};
</script>