
import Vue from 'vue'

const complaints = {
  namespaced: true,
  state: {
    complaints: [],
    complaintsCount: 0,
    limit: 10,
    offset: 1,
  },
  getters: {
    complaints(state) {

      return state.complaints;

    },
    complaintsCount(state) {
      return state.complaintsCount;
    },
    perPage(state) {
      return state.limit;
    },
    currentComplaint: state => id => {
      return state.complaints.find(complaint => +complaint.id === id);
    }
  },
  mutations: {
    getComplaints(state, payload) {

      state.complaints = payload.data;
      state.complaintsCount = +payload.total;

    },
  },
  actions: {
    // получение списка пользователей
    getComplaints({ dispatch, commit, state }, payload) {
      return new Promise((resolve, reject) => {
        let {page, sortDesc, sortBy} = payload;
        let formData = new FormData();
        formData.append('page', page);
        formData.append('sortDesc', sortDesc);
        formData.append('sortBy', sortBy);

        Vue.http.post('complaints', formData)
          .then((response) => {
            return response.json();
          })
          .then(response => {
            commit('getComplaints', response);
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
    deleteComplaint({ dispatch, commit, state }, payload) {
      
      return new Promise((resolve, reject) => {
        const commentId = !Number.isNaN(Number(payload)) ? +payload : null;

        if (commentId) {
          Vue.http.delete(`complaints/${commentId}`)
            .then((response) => {
              return response.json();
            })
            .then(response => {
              if(response.res){
                dispatch( 'openAlert', {
                  alertType : 'success',
                  alertMsg : 'Жалоба успешно удалена'
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

export default complaints;
