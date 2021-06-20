<template>
  <div>
    <div class="top-main">
      <h1 class="h1">Список категорий</h1>
      <router-link class="fa-lg flex-center" to="/categories/create">
        <font-awesome-icon icon="plus-circle" class="text-prinary" />
      </router-link>
    </div>

    <b-table
      striped
      hover
      :sort-by.sync="sortBy"
      :sort-desc.sync="sortDesc"
      :per-page="perPage"
      :current-page="currentPage"
      :items="categories"
      :fields="fields"
      :busy="loading"
      responsive="sm"
    >
      <!-- A custom formatted column -->
      <template #table-busy>
        <div class="text-center my-2">
          <b-spinner class="align-middle"></b-spinner>
          <strong>Идет загрузка...</strong>
        </div>
      </template>
      <template v-slot:cell(category)="data">{{ parent(data.item) }}</template>
      <template v-slot:cell(subcategory)="data">{{ category(data.item) }}</template>

      <template v-slot:cell(edit)="data">
        <router-link :to="`/categories/${data.item.id}`">
          <font-awesome-icon icon="edit" class="text-primary" role="button" />
        </router-link>
      </template>
      <template v-slot:cell(delete)="data">
        <font-awesome-icon
          icon="trash"
          class="text-danger"
          role="button"
          @click="deleteCategory(data.item.id)"
        />
      </template>
    </b-table>
    <b-pagination
      v-if="categoriesCount > 10"
      v-model="currentPage"
      :total-rows="categoriesCount"
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
      sortBy: "id",
      currentPage: 1,
      sortDesc: false,
      fields: [
        {
          key: "category",
          label: "Категория"
        },
        {
          key: "subcategory",
          label: "Подкатегория"
        },
        {
          key: "edit",
          label: "Редактировать",
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
    this.$store
      .dispatch("categories/getCategories", {
        page: 1,
        sortDesc: false,
        sortBy: "id"
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
    deleteCategory(id) {
       this.loading = true;
      this.$store.dispatch("categories/deleteCategory", id).then(
        result => {
          this.$store.dispatch("categories/getCategories", {
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
    },
    parent(item) {
      if (+item.parent > 0) {
        const parent = this.categories.find(items => {
          if (+items.parent === 0 && items.id === item.parent) {
            return items;
          }
        });

        return parent.name;
      } else {
        return item.name;
      }
    },
    category(item) {
      if (+item.parent > 0) {
        return item.name;
      } else {
        return "";
      }
    }
  },
  computed: {
    ...mapGetters("categories", ["categories", "categoriesCount", "perPage"])
  }
};
</script>