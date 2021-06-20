<template>
  <div>
    <div class="header-top">
      <h1 class="h1">Редактирование категории</h1>
    </div>

    <b-form @submit.prevent="onSubmit">

       <b-form-group>
        <b-img :src="photo" fluid v-if="photo"></b-img>
      </b-form-group>
      <b-form-group>
        <b-form-file v-model="formData.photo" placeholder="Выберете файл"></b-form-file>
      </b-form-group>

      <div class="row">
        <div class="col-lg-4">
          <b-form-group id="input-name" label="Название" label-for="name">
            <b-form-input v-model="formData.name" class="form__input" required></b-form-input>
          </b-form-group>
        </div>
        <div class="col-lg-4">
          <b-form-group id="input-slug" label="url" label-for="slug">
            <b-form-input
              id="slug"
              v-model="formData.slug"
              class="form__input"
              aria-describedby="slughelp"
              required
            ></b-form-input>
            <small
              id="slughelp"
              class="form-text text-muted"
            >генерируется автоматически, если поле не заполнено.</small>
          </b-form-group>
        </div>
        <div class="col-lg-4" v-if="formData.parent">
          <b-form-group id="input-category" label="Родительская категория" label-for="category">
            <b-form-select
              id="status"
              v-model="formData.parent"
              :options="parents"
              class="form__input"
            ></b-form-select>
          </b-form-group>
        </div>
      </div>
      <b-form-group id="input-title" label="title" label-for="title">
        <b-form-input
          id="title"
          v-model="formData.title"
          class="form__input"
          minlength="5"
          maxlength="255"
          placeholder="Введите title"
        ></b-form-input>
        <small class="form-text text-muted"
            >генерируется автоматически, если поле не заполнено.</small>
      </b-form-group>
      <b-form-group id="input-description" label="description" label-for="description">
        <b-form-textarea
            id="input-description"
            class="form__textarea"
            v-model="formData.description"
        ></b-form-textarea>
        <small class="form-text text-muted"
          >генерируется автоматически, если поле не заполнено.</small>
      </b-form-group>
      <b-button
        type="submit"
        :disabled="submit"
        class="button button_red form-comment__button button_submit"
      >
        <b-spinner small type="grow" v-show="submit"></b-spinner>
        <span>Сохранить</span>
      </b-button>
    </b-form>
  </div>
</template>
<script>

export default {
  data() {
    return {
      formData: null,
      photo: '',
      parents: [],
      parent: null,
      submit: false
    };
  },
  created() {
    let { id, name, slug, parent, photo, title, description } = this.$store.getters[
      "categories/getCurrentCategory"
    ](+this.$route.params.id);
    if(photo){
      this.photo = `/bundles/categories/${photo}`;
    }
    
    if (parent) {
      this.parents = this.$store.state.categories.parents;
      this.parent = parent;
    }
    this.formData = {
      id,
      name,
      slug,
      title,
      description,
      parent,
      photo: null
    };
  },
  methods: {
    onSubmit() {
      const { id, name, slug, title, description, parent, photo } = this.formData;

      let formData = new FormData();
      formData.append("id", id);
      formData.append("name", name);
      formData.append("slug", slug);
      formData.append("title", title);
      formData.append("description", description);
      formData.append("photo", photo);
      if (parent && parent !== this.parent) {
        formData.append("parent", parent);
      }
      this.submit = true;
      this.$store.dispatch("categories/updateCategory", formData).then(
        result => {
          this.submit = false;
        },
        error => {
          this.submit = false;
        }
      );
    }
  },
};
</script>