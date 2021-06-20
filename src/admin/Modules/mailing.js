
import Vue from 'vue'
import router from '../router';

const mailing = {
  namespaced: true,
  state: {
    templates: [],
    templatesCount: 0,
    currentTemplate: [],
    limit: 10,
    offset: 1,
  },
  getters: {
    templates(state) {

        return state.templates;

    },
    templatesCount(state) {
        return state.templatesCount;
    },
    perPage(state) {
        return state.limit;
    },
    getCurrentTemplate(state){
      return state.currentTemplate;
    },
    currentTemplate: state => id => {
        return state.templates.find(template => +template.id === id);
    }
  },
  mutations: {
    getTemplates(state, payload) {

      state.templates = payload.data;
      state.templatesCount = +payload.total;

    },
    setCurrentTemplate(state, payload) {
      
      state.currentTemplate = state.templates.find(template => +template.id === payload);;
    }
  },
  actions: {
    // получение списка пользователей
    getTemplates({ dispatch, commit }, payload) {
      return new Promise((resolve, reject) => {
        let {page, sortDesc, sortBy} = payload;
        let formData = new FormData();
        formData.append('page', page);
        formData.append('sortDesc', sortDesc);
        formData.append('sortBy', sortBy);

        Vue.http.post('mailings', formData)
          .then((response) => {
            return response.json();
          })
          .then(response => {
            commit('getTemplates', response);
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
    getCurrentTemplate({ dispatch, commit, state }, payload){
      commit('setCurrentTemplate', payload);
    },
    createTemplate({ dispatch, commit, state }, payload) {
      return new Promise((resolve, reject) => {
        
         Vue.http.post('mailings/create', payload)
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
            router.push( '/mailing' );
          }, response => {
            reject(response)
            dispatch('openAlert',{
              alertType : 'danger',
              alertMsg : 'Произошла ошибка. Попробуйте еще раз'
            }, { root: true })
          });
        });
    },
    updateTemplate({ dispatch, commit, state }, payload) {
        return new Promise((resolve, reject) => {
          
           Vue.http.post('mailings/update', payload)
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
              router.push( '/mailing' );
            }, response => {
              reject(response)
              dispatch('openAlert',{
                alertType : 'danger',
                alertMsg : 'Произошла ошибка. Попробуйте еще раз'
              }, { root: true })
            });
          });
      },
    deleteTemplate({ dispatch, commit, state }, payload) {
      
      return new Promise((resolve, reject) => {
        const messageId = !Number.isNaN(Number(payload.id)) ? +payload.id : null;

        if (messageId) {
          Vue.http.delete(`mailings/${messageId}`)
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

export default mailing;
