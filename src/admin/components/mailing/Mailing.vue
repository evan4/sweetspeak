<template>
  <div>

    <div class="top-main">
      <h1 class="h1">Шаблоны писем</h1>
       <router-link class="fa-lg flex-center" to="/mailing/create">
        <font-awesome-icon icon="plus-circle" class="text-prinary" />
      </router-link>
    </div>

    <b-table
      striped
      hover
      :sort-by.sync="sortBy"
      :sort-desc.sync="sortDesc"
      :current-page="currentPage"
      :items="templates"
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
      <template v-slot:cell(title)="data">
        <router-link class="text-secondary" :to="`/mailing/${data.item.id}`">
          {{ data.value }}
        </router-link>
      </template>

      <!-- A virtual column -->
      <template v-slot:cell(delete)="data">
        <font-awesome-icon
          icon="trash"
          class="text-danger"
          role="button"
          @click="deleteTemplate(data.item.id)"
        />
      </template>
    </b-table>
    <b-pagination
      v-if="templatesCount > 10"
      v-model="currentPage"
      :total-rows="templatesCount"
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
      sortBy: "title",
      currentPage: 1,
      sortDesc: true,
      fields: [
        {
          key: "title",
          label: "Тема",
          sortable: true
        },
        {
          key: "delete",
          label: "Удалить",
          thStyle: 'width: 50px',
          tdClass: 'text-center'
        }
      ],
      loading: true
    };
  },
  created() {
    this.$store
      .dispatch("mailings/getTemplates", {
        page: 1,
        sortDesc: false,
        sortBy: "author"
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
    deleteTemplate(id, photo) {
      this.loading = true;
      this.$store
        .dispatch("mailings/deleteTemplate", {
          id,
          photo
        })
        .then(
          result => {
            this.$store.dispatch("mailings/getTemplates", {
              page: this.currentPage,
              sortDesc: this.sortDesc,
              sortBy: this.sortBy
            });
            this.loading = false;
          },
          error => {
            this.loading = false;
            console.log(error);
          }
        );
    },
    closeAlert(){
      this.$store.dispatch( 'closeAlert')
    }
  },
  computed: {
    ...mapGetters("mailings", ["templates", "templatesCount", "perPage"])
  },
  watch: {
    currentPage: function(newPage, oldPage) {
      this.loading = true;
      this.$store
        .dispatch("mailings/getTemplates", {
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
        .dispatch("mailings/getTemplates", {
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
        .dispatch("mailings/getTemplates", {
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