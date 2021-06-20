
import Vue from 'vue'
import router from '../router';

const friends = {
  namespaced: true,
  state: {
    friends: [],
    friendsRequests: [],
    friendsCount: 0,
    limit: 10,
    offset: 1,
  },
  getters: {
    friends(state) {

      return state.friends;

    },
    friendsCount(state) {
      return state.friendsCount;
    },
    perPage(state) {
      return state.limit;
    },
    friendsRequests(state){
      return state.friendsRequests
    }
  },
  mutations: {
    getFriends(state, payload) {
      const {data, total} = payload;

      state.friends = data;
      state.friendsCount = +total;

    },
    getFriendsRequest(state, payload) {
      const {data} = payload;

      state.friendsRequests = data;

    },
  },
  actions: {
    getFriends({ dispatch, commit, state }, payload) {
      return new Promise((resolve, reject) => {
        let {page, sortDesc, sortBy} = payload;
        let formData = new FormData();
        formData.append('page', page);
        formData.append('sortDesc', sortDesc);
        formData.append('sortBy', sortBy);

        Vue.http.post('friends', formData)
          .then((response) => {
            return response.json();
          })
          .then(response => {
            commit('getFriends', response);
            resolve()
          }, response => {
            dispatch('openAlert',{
              alertType : 'danger',
              alertMsg : 'Произошла ошибка. Попробуйте еще раз'
            }, { root: true });
            reject(response)
          });
      })
    },
    getFriendsRequests({ dispatch, commit, state }, payload) {
      Vue.http.post('friends/friendsRequests')
          .then((response) => {
            return response.json();
          })
          .then(response => {
            commit('getFriendsRequest', response);
          });
    },
    accesptFriendRequest({ dispatch, commit, state }, payload) {
      return new Promise((resolve, reject) => {
        const {id, photo} = payload;

        const userId = !Number.isNaN(Number(id)) ? +id : null;
        let formData = new FormData();
        formData.append('id', userId);
        if (userId) {
          Vue.http.post('friends/accept', formData)
              .then((response) => {
                return response.json();
              })
              .then(response => {
                dispatch('openAlert',{
                    alertType : 'success',
                    alertMsg : 'Пользователь успешно добавлен в друзья'
                }, { root: true })
                router.push( redirect );
                resolve(response)
              }, response => {
                dispatch('openAlert',{
                    alertType : 'danger',
                    alertMsg : 'Произошла ошибка. Попробуйте еще раз'
                }, { root: true })
                reject(response)
            })
          }
      })
    },
    deleteFriend({ dispatch, commit, state }, payload) {
      return new Promise((resolve, reject) => {
        const {id, photo} = payload;

        const userId = !Number.isNaN(Number(id)) ? +id : null;
        
        if (userId) {
          Vue.http.delete(`friends/{userId}`)
            .then((response) => {
              return response.json();
            })
            .then(response => {
              if(response.success){
                dispatch( 'openAlert', {
                  alertType : 'success',
                  alertMsg : 'Пользователь успешно удален из ваших друзей'
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

export default friends;
