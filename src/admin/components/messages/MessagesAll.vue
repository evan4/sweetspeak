<template>
  <div>
    <div class="top-main">
      <h1 class="h1">Сообщения</h1>
    </div>

    <b-table
      striped
      hover
      :sort-by.sync="sortBy"
      :sort-desc.sync="sortDesc"
      :current-page="currentPage"
      :items="messages"
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
      <template v-slot:cell(body)="data">
          {{ data.value }}
      </template>
      <template v-slot:cell(created_at)="data">{{ dateFormat(data.value) }}</template>

      <template v-slot:cell(status)="data">
        <font-awesome-icon icon="envelope" v-if="+data.value === 0" />
      </template>
      <!-- A virtual column -->
      <template v-slot:cell(delete)="data">
        <font-awesome-icon
          icon="trash"
          class="text-danger"
          role="button"
          @click="deleteMessage(data.item.id)"
        />
      </template>
    </b-table>
    <b-pagination
      v-if="messagesCount > 10"
      v-model="currentPage"
      :total-rows="messagesCount"
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
          key: "author",
          label: "Автор",
          sortable: true
        },
        {
          key: "users",
          label: "Кому",
          sortable: true
        },
        {
          key: "body",
          label: "текст",
          sortable: true
        },
        {
          key: "created_at",
          label: "Дата",
          sortable: true
        },
        {
          key: "status",
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
    this.$store
      .dispatch("messages/getAllMessages", {
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
    deleteMessage(id, photo) {
      this.$store
        .dispatch("messages/deleteMessage", {
          id,
          photo
        })
        .then(
          result => {
            this.$store.dispatch("messages/getAllMessages", {
              page: this.currentPage,
              sortDesc: this.sortDesc,
              sortBy: this.sortBy
            });
          },
          error => {
            console.log(error);
          }
        );
    },
    closeAlert(){
      this.$store.dispatch( 'closeAlert')
    }
  },
  computed: {
    ...mapGetters("messages", ["messages", "messagesCount", "perPage"])
  },
  watch: {
    currentPage: function(newPage, oldPage) {
      this.$store
        .dispatch("messages/getAllMessages", {
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
      console.log("sort " + newDescValue);
      this.$store
        .dispatch("messages/getAllMessages", {
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
      this.$store
        .dispatch("messages/getAllMessages", {
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