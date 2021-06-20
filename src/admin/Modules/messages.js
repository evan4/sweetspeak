
import Vue from 'vue'
import router from '../router';

const messages = {
  namespaced: true,
  state: {
    messages: [],
    messagesCount: 0,
    limit: 10,
    offset: 1,
  },
  getters: {
    messages(state) {

        return state.messages;

    },
    messagesCount(state) {
        return state.messagesCount;
    },
    perPage(state) {
        return state.limit;
    },
    currentMessage: state => id => {
        return state.messages.find(message => +message.id === id);
    }
  },
  mutations: {
    getMessages(state, payload) {

      state.messages = payload.data;
      state.messagesCount = +payload.total;

    },
  },
  actions: {
    // получение списка пользователей
    getMessages({ dispatch, commit }, payload) {
      return new Promise((resolve, reject) => {
        let {page, sortDesc, sortBy} = payload;
        let formData = new FormData();
        formData.append('page', page);
        formData.append('sortDesc', sortDesc);
        formData.append('sortBy', sortBy);

        Vue.http.post('messages', formData)
          .then((response) => {
            return response.json();
          })
          .then(response => {
            commit('getMessages', response);
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
    getAllMessages({ dispatch, commit }, payload) {
      return new Promise((resolve, reject) => {
        let {page, sortDesc, sortBy} = payload;
        let formData = new FormData();
        formData.append('page', page);
        formData.append('sortDesc', sortDesc);
        formData.append('sortBy', sortBy);

        Vue.http.post('getAllMessages', formData)
          .then((response) => {
            return response.json();
          })
          .then(response => {
            commit('getMessages', response);
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
    createMessage({ dispatch, commit, state }, payload) {
      return new Promise((resolve, reject) => {
        
         Vue.http.post('messages/create', payload)
          .then((response) => {
            return response.json();
          })
          .then(response => {
            console.log(response);
            resolve(response.success)
            dispatch('openAlert',{
              alertType : 'success',
              alertMsg : 'Сообщение отправлено'
            }, { root: true })
            router.push( '/messages' );
          }, response => {
            reject(response)
            dispatch('openAlert',{
              alertType : 'danger',
              alertMsg : 'Произошла ошибка. Попробуйте еще раз'
            }, { root: true })
          });
        });
    },
    deleteMessage({ dispatch, commit, state }, payload) {
      
      return new Promise((resolve, reject) => {
        const messageId = !Number.isNaN(Number(payload.id)) ? +payload.id : null;

        if (messageId) {
          Vue.http.delete(`messages/${messageId}`)
            .then((response) => {
              return response.json();
            })
            .then(response => {
              if(response.res){
                dispatch( 'openAlert', {
                  alertType : 'success',
                  alertMsg : 'Сообщение успешно удалена'
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

export default messages;
