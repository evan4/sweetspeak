<template>
  <div>
    <div class="top-main">
      <h1 class="h1">Список статей</h1>

      <router-link class="fa-lg flex-center" to="/articles/create" 
      v-if="userAuthorizedInfo && userAuthorizedInfo.confirm == 1">
        <font-awesome-icon icon="plus-circle" class="text-prinary" />
      </router-link>
    </div>

    <b-table
      striped 
      hover
      :sort-by.sync="sortBy"
      :sort-desc.sync="sortDesc"
      :current-page="currentPage"
      :items="articles"
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
      <template v-slot:cell(title)="data">
        <router-link class="text-secondary" :to="`/articles/${data.item.id}`">
          {{ data.value }}
          <font-awesome-icon
            icon="comment"
            class="fa-lg ml-2 text-secondary"
            v-if="+data.item.comments_count > 0"
          />
        </router-link>
      </template>
      <template v-slot:cell(category)="data">{{ data.item.category }} {{ data.item.subcategory }}</template>
      <template v-slot:cell(date)="data">{{ dateFormat(data.value) }}</template>

      <template v-slot:cell(moderated)="data">
        <font-awesome-icon icon="check" class="text-info" v-if="+data.value == 1" />
        <font-awesome-icon icon="eye-slash" title="скрыта" v-if="+data.value == 2" />
      </template>
      <!-- A virtual column -->
      <template v-slot:cell(delete)="data">
        <font-awesome-icon
          icon="trash"
          class="text-danger"
          role="button"
          @click="deleteArticle(data.item.id, data.item.photo)"
        />
      </template>
    </b-table>
    <b-pagination
      v-if="articlesCount > 10"
      v-model="currentPage"
      :total-rows="articlesCount"
      :per-page="perPage"
      class="mt-4"
      aria-controls="my-table"
    ></b-pagination>
  </div>
</template>
<script>

import Vue from "vue";
import { mapGetters } from "vuex";

export default {
  data() {
    return {
      sortBy: "date",
      currentPage: 1,
      sortDesc: true,
      fields: [
        {
          key: "title",
          label: "Заголовок",
          sortable: true
        },
        {
          key: "category",
          label: "Категория",
        },
        {
          key: "author",
          label: "Автор",
          sortable: true
        },
        {
          key: "date",
          label: "Дата",
          sortable: true
        },
        {
          key: "moderated",
          label: "Статус",
          sortable: true,
          tdClass: 'text-center'
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
    this.role = Vue.prototype.$role;
    this.$store
      .dispatch("articles/getArticles", {
        page: 1,
        sortDesc: true,
        sortBy: "date"
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
    deleteArticle(id, photo) {
      this.loading = true;
      this.$store
        .dispatch("articles/deleteArticle", {
          id,
          photo
        })
        .then(
          result => {
            this.$store.dispatch("articles/getArticles", {
              page: this.currentPage,
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
    ...mapGetters("users", ["userAuthorizedInfo"]),
    ...mapGetters("articles", ["articles", "articlesCount", "perPage"])
  },
  watch: {
    currentPage: function(newPage, oldPage) {
      this.loading = true;
      this.$store
        .dispatch("articles/getArticles", {
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
        .dispatch("articles/getArticles", {
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
        .dispatch("articles/getArticles", {
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