<template>
  <div class="col-md-3">
    <aside class="aside" v-if="userAuthorizedInfo">
      <div class="aside-user">
        <div class="aside-user-image">
          <img
            :src="photo"
            :alt="userAuthorizedInfo.name"
            class="aside-user-image__image image-responsive"
            v-if="userAuthorizedInfo"
          />
        </div>
        <p class="aside-user__name">{{name}}</p>
        <p class="aside-user__email">{{email}}</p>
         <router-link
            class="flex-center footer-form__submit button button_red"
            to="/profile/edit"
          >Редактировать</router-link>
      </div>
      <ul class="aside-menu">
        <li class="aside-menu__item">
          <router-link class="aside-menu__link" to="/messages">
            <i class="aside-menu__link_personal"></i>Личные сообщения
          </router-link>
        </li>
        <li class="aside-menu__item">
          <router-link class="aside-menu__link" to="/">
            <i class="aside-menu__link_profile"></i>Профиль
          </router-link>
        </li>
        <!-- <li class="aside-menu__item">
          <a href="#" class="aside-menu__link">
            <i class="aside-menu__link_file"></i>Анкета знакомств
          </a>
        </li> -->
        <li class="aside-menu__item">
          <router-link class="aside-menu__link" to="/friends">
            <i class="aside-menu__link_friends"></i>Мои друзья
          </router-link>
        </li>
        
        <li class="aside-menu__item" v-if="userAuthorizedInfo.confirm == 1">
           <router-link class="aside-menu__link"
            to="/articles/create">
               <i class="aside-menu__link_publish"></i>Создать публикацию
          </router-link>
        </li>
        <!-- <li class="aside-menu__item">
          <a href="#" class="aside-menu__link">
            <i class="aside-menu__link_pro"></i>Pro версия
          </a>
        </li> -->
        <li class="aside-menu__item" v-if="this.role != 'user' ">
          <router-link class="aside-menu__link" to="/users">
            <font-awesome-icon icon="users" role="button" />
            <span>Пользователи</span>
          </router-link>
        </li>
        <li class="nav-item" v-if="this.role === 'admin' || this.role === 'superadmin'">
          <router-link class="aside-menu__link" to="/categories">
            <font-awesome-icon icon="sitemap"/>
            <span>Категории</span>
          </router-link>
        </li>
        <li class="aside-menu__item">
          <router-link class="aside-menu__link" to="/articles">
          <font-awesome-icon icon="newspaper"/>
            <span>Статьи</span>
          </router-link>
        </li>
        <li class="nav-item" v-if="role != 'user' ">
          <router-link class="aside-menu__link" to="/comments">
            <font-awesome-icon icon="comment"/>
            <span>Комментарии</span>
          </router-link>
        </li>
        <li class="nav-item"  v-if="this.role === 'admin' || this.role === 'superadmin' ">
          <router-link class="aside-menu__link" to="/complaints">
            <font-awesome-icon icon="thumbs-down"/>
            <span>Жалобы</span>
          </router-link>
        </li>
        <li class="nav-item" v-if="this.role === 'superadmin'">
          <router-link class="aside-menu__link" to="/messages_all">
            <font-awesome-icon icon="envelope"/>
            <span>Все сообщения</span>
          </router-link>
        </li>
        <li class="nav-item" v-if="this.role === 'admin' || this.role === 'superadmin' ">
          <router-link class="aside-menu__link" to="/mailing">
            <font-awesome-icon icon="mail-bulk"/>
            <span>Рассылка</span>
          </router-link>
        </li>
        <li class="nav-item">
          <a href="/logout" class="aside-menu__link">
            <font-awesome-icon icon="sign-out-alt"/> Выйти
          </a>
        </li>
      </ul>
    </aside>
  </div>
</template>
<script>
import Vue from "vue";
import { mapGetters } from "vuex";

export default {
  data() {
    return {
      role: "",
      name: "",
      email: "",
      mainProps: {
        center: true,
        fluidGrow: true,
        blank: true,
        blankColor: "#bbb",
        class: "my-5"
      },
      showImages: false
    };
  },
  created() {
    this.role = Vue.prototype.$role;
    this.name = Vue.prototype.$name;
    this.email = Vue.prototype.$userEmail;
    this.$store.dispatch("users/getUserAuthorizedInfo", {
      email: Vue.prototype.$userEmail
    });
  },
  computed: {
    ...mapGetters("users", ["userAuthorizedInfo"]),
    photo: function() {
      if (this.userAuthorizedInfo && this.userAuthorizedInfo.photo) {
        return `/bundles/users/300_${this.userAuthorizedInfo.photo}`;
      }
      return "/bundles/img/no-photo.jpg";
    },
    createdAt: function() {
      return new Date(this.userAuthorizedInfo.created_at).toLocaleDateString();
    },
    gender: function() {
      return this.userAuthorizedInfo.gender === "m" ? "мужской" : "Женский";
    },
    target: function() {

      return this.userAuthorizedInfo.target === "m" ? "мужчину" : "женщину";
    },
    userphoto: {
      get() {
        return null;
      },
      set(newValue) {
        let photoUser = "";
        console.log(newValue);
        let formData = new FormData();
        formData.append("photo", newValue);

        Vue.http
          .post("users/addPhhoto", formData)
          .then(response => {
            return response.json();
          })
          .then(
            response => {
              if (response.success) {
                const { id, photo } = response;
                console.log(response);

                this.userAuthorizedInfo.photos.splice(
                  this.userAuthorizedInfo.photos.length,
                  1,
                  {
                    id,
                    photo
                  }
                );

                this.$store.dispatch(
                  "openAlert",
                  {
                    alertType: "success",
                    alertMsg: "Фото загружено"
                  },
                  { root: true }
                );
              }
            },
            response => {
              this.$store.dispatch(
                "openAlert",
                {
                  alertType: "danger",
                  alertMsg: "Произошла ошибка. Попробуйте еще раз"
                },
                { root: true }
              );
            }
          );
      }
    }
  },
  methods: {
    getImageUrl(imageId) {
      const { width } = this.mainProps;
      const image = this.userAuthorizedInfo.photos.find(
        item => item.id === imageId
      );
      return `/bundles/users/500_${image.photo}`;
    },
    deletePhoto(id, photo) {
      let formData = new FormData();

      formData.append("id", +id);
      formData.append("author_id", +this.userAuthorizedInfo.id);
      formData.append("photo", photo);
      console.log(photo);
      Vue.http
        .post("users/removePhhoto", formData)
        .then(response => {
          return response.json();
        })
        .then(
          response => {
            if (response.success) {
              const photoId = this.userAuthorizedInfo.photos.findIndex(
                item => +item.id === +id
              );

              this.$delete(this.userAuthorizedInfo.photos, photoId);

              this.$store.dispatch(
                "openAlert",
                {
                  alertType: "success",
                  alertMsg: "Фото загружено"
                },
                { root: true }
              );
              return photo;
            }
          },
          response => {
            this.$store.dispatch(
              "openAlert",
              {
                alertType: "danger",
                alertMsg: "Произошла ошибка. Попробуйте еще раз"
              },
              { root: true }
            );
          }
        );
    }
  }
};
</script>