
import Vue from 'vue'
import router from '../router';

const articles = {
  namespaced: true,
  state: {
    currentArticleId: 0,
    articles: [],
    articlesCount: 0,
    limit: 10,
    offset: 1,
  },
  getters: {
    articles(state) {

      return state.articles;

    },
    articlesCount(state) {
      return state.articlesCount;
    },
    perPage(state) {
      return state.limit;
    },
    currentArticle: state => id => {
      return state.articles.find(article => +article.id === id);
    },
    currentArticleId(state) {
      return state.currentArticleId;
    },
  },
  mutations: {
    getArticles(state, payload) {

      state.articles = payload.data;
      state.articlesCount = +payload.total;

    },
    currentArticleId(state, payload) {
      const article = state.articles.find(article => +article.id === payload);
      state.currentArticleId =  article.id
    }
  },
  actions: {
    currentArticleId({ dispatch, commit, state }, payload) {
      commit('currentArticleId', payload)
    },
    // получение списка пользователей
    getArticles({ dispatch, commit, state }, payload) {
      return new Promise((resolve, reject) => {
        let {page, sortDesc, sortBy} = payload;
        let formData = new FormData();
        formData.append('page', page);
        formData.append('sortDesc', sortDesc);
        formData.append('sortBy', sortBy);

        Vue.http.post('articles', formData)
          .then((response) => {
            return response.json();
          })
          .then(response => {
            commit('getArticles', response);
            resolve()
          }, response => {
            dispatch('openAlert',{
              alertType : 'danger',
              alertMsg : 'Произошла ошибка. Попробуйте еще раз'
            }, { root: true })
            reject(response)
          });
      })
    },
    createArticle({ dispatch, commit, state }, payload) {
      return new Promise((resolve, reject) => {
        
         Vue.http.post('articles/create', payload)
          .then((response) => {
            return response.json();
          })
          .then(response => {
            console.log(response);
            if(response.error){
              reject(response)
              dispatch('openAlert',{
                alertType : 'danger',
                alertMsg : response.error
              }, { root: true })
            }else{
              resolve(response.success)
              dispatch('openAlert',{
                alertType : 'success',
                alertMsg : 'Статья успешно создана и ожидает модерации'
              }, { root: true })
              router.push( '/articles' );
            }
            
          }, response => {
            reject(response)
            dispatch('openAlert',{
              alertType : 'danger',
              alertMsg : 'Произошла ошибка. Попробуйте еще раз'
            }, { root: true })
          });
        });
    },
    updateArticle({ dispatch, commit, state }, payload) {
      return new Promise((resolve, reject) => {
       Vue.http.post('articles/update', payload)
        .then((response) => {
          return response.json();
        })
        .then(response => {
          if(response.hasOwnProperty('error')){
            dispatch('openAlert',{
              alertType : 'danger',
              alertMsg : 'Произошла ошибка. Попробуйте еще раз'
            }, { root: true });
            reject();
          }else{
            dispatch('openAlert',{
              alertType : 'success',
              alertMsg : 'Статья успешно обновлена'
            }, { root: true })
            router.push( '/articles' );
            resolve()
          }
          
        }, response => {
          dispatch('openAlert',{
            alertType : 'danger',
            alertMsg : 'Произошла ошибка. Попробуйте еще раз'
          }, { root: true });
          reject();
        });
      });
    },
    updateStatus({ dispatch, commit, state }, payload) {
      
        const articleId = !Number.isNaN(Number(payload.id)) ? +payload.id : null;

        if (articleId) {
          let formData = new FormData();
          formData.append('id', articleId);
          formData.append('moderated', payload.moderated);
          formData.append('author_id', Vue.prototype.$id);

          Vue.http.post('articles/updateStatus', formData)
            .then((response) => {
              return response.json();
            })
            .then(response => {
              if(response.res){
                dispatch( 'openAlert', {
                  alertType : 'success',
                  alertMsg : 'Статус статьи успешно изменен'
                }, { root: true });
                router.push( '/articles' );
              }else{
                dispatch('openAlert',{
                  alertType : 'danger',
                  alertMsg : 'Произошла ошибка. Попробуйте еще раз'
                }, { root: true });
                
              }
              
            }, response => {
              dispatch('openAlert',{
                alertType : 'danger',
                alertMsg : 'Произошла ошибка. Попробуйте еще раз'
              }, { root: true });
              
            });
        }
    },
    deleteArticle({ dispatch, commit, state }, payload) {
      
      return new Promise((resolve, reject) => {
        const articleId = !Number.isNaN(Number(payload.id)) ? +payload.id : null;

        if (articleId) {
          let formData = new FormData();
          formData.append('id', articleId);
          formData.append('photo', payload.photo);
          formData.append('author_id', Vue.prototype.$id);
          Vue.http.post('articles/delete', formData)
            .then((response) => {
              return response.json();
            })
            .then(response => {
              if(response.res){
                dispatch( 'openAlert', {
                  alertType : 'success',
                  alertMsg : 'Статья успешно удалена'
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

export default articles;
