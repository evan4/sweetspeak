
import Vue from 'vue'
import router from '../router';

const categories = {
  namespaced: true,
  state: {
    categories: [],
    parents: [],
    categoriesCount: 0,
    limit: 10,
    categoriesCount: 0,
  },
  getters: {
    categories(state) {

      return state.categories;

    },
    categoriesCount(state) {
      return state.categoriesCount;
    },
    perPage(state) {
      return state.limit;
    },
    getCurrentCategory: state => id => {
      return state.categories.find(category => +category.id === id);
    },
  },
  mutations: {
    getCategories(state, payload) {

      const {all, categories, subcategories } = payload;

      state.categories = all;
      state.categoriesCount = all.length;
      
      let parents = [];
      let i = 1;
      for (const key in categories) {
        if (categories.hasOwnProperty(key)) {
          parents.push(
            { value: i++, text: key }
          )
        }
      }
      state.parents = parents;
    },
  },
  actions: {
    // получение списка пользователей
    getCategories({ dispatch, commit, state }, payload) {
      return new Promise((resolve, reject) => {
        let {page, sortDesc, sortBy} = payload;
        let formData = new FormData();
        formData.append('page', page);
        formData.append('sortDesc', sortDesc);
        formData.append('sortBy', sortBy);

        Vue.http.post('categories', formData)
          .then((response) => {
            return response.json();
          })
          .then(response => {
            commit('getCategories', response);
            resolve(response)
          }, response => {
            dispatch('openAlert',{
              alertType : 'danger',
              alertMsg : 'Произошла ошибка. Попробуйте еще раз'
            }, { root: true })
            reject(response)
          });
      })
    },
    createCategory({ dispatch, commit, state }, payload) {
      return new Promise((resolve, reject) => {
  
         Vue.http.post('categories/create', payload)
          .then((response) => {
            return response.json();
          })
          .then(response => {
            console.log(response);
            resolve(response.success)
            dispatch('openAlert',{
              alertType : 'success',
              alertMsg : 'Категория успешно создана'
            }, { root: true })
            router.push( '/categories' );
          }, response => {
            reject(response)
            dispatch('openAlert',{
              alertType : 'danger',
              alertMsg : 'Произошла ошибка. Попробуйте еще раз'
            }, { root: true })
          });
        });
    },
    updateCategory({ dispatch, commit, state }, payload) {
      return new Promise((resolve, reject) => {
        Vue.http.post('categories/update', payload)
          .then((response) => {
            return response.json();
          })
          .then(response => {
            console.log(response);
            dispatch('openAlert',{
              alertType : 'success',
              alertMsg : 'Данные успешно обновлены'
            }, { root: true })
            router.push( '/categories' );
            resolve(response.success)
          }, response => {
            dispatch('openAlert',{
              alertType : 'danger',
              alertMsg : 'Произошла ошибка. Попробуйте еще раз'
            }, { root: true })
            reject(response)
          });
      });
    },
    deleteCategory({ dispatch, commit, state }, payload) {
      return new Promise((resolve, reject) => {
        const id = !Number.isNaN(Number(payload)) ? +payload : null;
      
        if (id) {
          Vue.http.delete(`categories/${id}`)
            .then((response) => {
              return response.json();
            })
            .then(response => {
              if(response.success){
                dispatch( 'openAlert', {
                  alertType : 'success',
                  alertMsg : 'Пользователь успешно удален'
                }, { root: true });
                resolve(response.success)
              }else{
                dispatch('openAlert',{
                  alertType : 'danger',
                  alertMsg : 'Произошла ошибка. Попробуйте еще раз'
                }, { root: true });
                reject(response)
              }
              
            }, response => {
              dispatch('openAlert',{
                alertType : 'danger',
                alertMsg : 'Произошла ошибка. Попробуйте еще раз'
              }, { root: true });
              reject(response)
            });
        }
      });
    }
  },
};

export default categories;
