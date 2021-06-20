
import Vue from 'vue'
import router from '../router';

const comments = {
  namespaced: true,
  state: {
    comments: [],
    commentsCount: 0,
    limit: 10,
    offset: 1,
  },
  getters: {
    comments(state) {

      return state.comments;

    },
    commentsCount(state) {
      return state.commentsCount;
    },
    perPage(state) {
      return state.limit;
    },
    currentComment: state => id => {
      return state.comments.find(comment => +comment.id === id);
    }
  },
  mutations: {
    getComments(state, payload) {

      state.comments = payload.data;
      state.commentsCount = +payload.total;

    },
  },
  actions: {
    // получение списка пользователей
    getComments({ dispatch, commit, state }, payload) {
      return new Promise((resolve, reject) => {
        let {page, sortDesc, sortBy} = payload;
        let formData = new FormData();
        formData.append('page', page);
        formData.append('sortDesc', sortDesc);
        formData.append('sortBy', sortBy);

        Vue.http.post('comments', formData)
          .then((response) => {
            return response.json();
          })
          .then(response => {
            commit('getComments', response);
            resolve()
          }, response => {
            dispatch('openAlert',{
              alertType : 'danger',
              alertMsg : 'Произошла ошибка. Попробуйте еще раз'
            }, { root: true });
            reject()
          });
      }); 
    },
    approveComment({ dispatch, commit, state }, payload) {
      return new Promise((resolve, reject) => {
        Vue.http.post('comments/approve', payload)
          .then((response) => {
            return response.json();
          })
          .then(response => {
            dispatch('openAlert',{
              alertType : 'success',
              alertMsg : 'Комментарий успешно одобрен'
            }, { root: true })
            router.push( '/comments' );
            resolve()
          }, response => {
            dispatch('openAlert',{
              alertType : 'danger',
              alertMsg : 'Произошла ошибка. Попробуйте еще раз'
            }, { root: true })
            reject()
          });
      });
    },
    deleteComment({ dispatch, commit, state }, payload) {
      
      return new Promise((resolve, reject) => {
        const commentId = !Number.isNaN(Number(payload)) ? +payload : null;

        if (commentId) {
          Vue.http.delete(`comments/${commentId}`)
            .then((response) => {
              return response.json();
            })
            .then(response => {
              if(response.res){
                dispatch( 'openAlert', {
                  alertType : 'success',
                  alertMsg : 'Комментраий успешно удален'
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

export default comments;
