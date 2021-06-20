
import Vue from 'vue'
import router from '../router';

const users = {
  namespaced: true,
  state: {
    userAuthorizedInfo: null,
    users: [],
    currentuser: [],
    usersCount: 0,
    limit: 10,
    offset: 1,
  },
  getters: {
    users(state) {

      return state.users;

    },
    usersCount(state) {
      return state.usersCount;
    },
    perPage(state) {
      return state.limit;
    },
    userAuthorizedInfo(state) {
      return state.userAuthorizedInfo;
    },
    getCurrentUser(state) {
      return state.currentuser;
    },
  },
  mutations: {
    setUserAuthorizedInfo(state, payload) {
      state.userAuthorizedInfo = payload.user
    },
    getUsers(state, payload) {
      const {data, total} = payload;

      state.users = data;
      state.usersCount = +total;

    },
    setCurrentUser(state, payload) {
      
      state.currentuser = payload;
    }
  },
  actions: {
    getUserAuthorizedInfo({ dispatch, commit, state }, payload) {
      let formData = new FormData();
      formData.append('email', payload.email);
      Vue.http.post('currentUserDetailed', formData)
        .then((response) => {
          return response.json();
        })
        .then(response => {
          commit('setUserAuthorizedInfo', response);
        }, response => {
          dispatch('openAlert',{
            alertType : 'danger',
            alertMsg : 'Произошла ошибка. Попробуйте еще раз'
          }, { root: true })
        });
    },
    // получение списка пользователей
    getUsers({ dispatch, commit, state }, payload) {
      return new Promise((resolve, reject) => {
      let {page, sortDesc, sortBy} = payload;
      let formData = new FormData();
      formData.append('page', page);
      formData.append('sortDesc', sortDesc);
      formData.append('sortBy', sortBy);

      Vue.http.post('users', formData)
        .then((response) => {
          return response.json();
        })
        .then(response => {
          commit('getUsers', response);
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
    getCurrentUser({ dispatch, commit, state }, payload) {
      let formData = new FormData();
      formData.append('id', payload);
      Vue.http.post('users/getDetails', formData)
      .then((response) => {
        return response.json();
      })
      .then(response => {
        commit('setCurrentUser', response.user);
      }, response => {
        
      });
    },
    createUser({ dispatch, commit, state }, payload) {
      return new Promise((resolve, reject) => {
        const {name, email, password, role} = payload;
        
         Vue.http.post('users', { 
          name, email, password, role
        })
          .then((response) => {
            return response.json();
          })
          .then(response => {
            console.log(response);
            resolve(response.success)
            dispatch('openAlert',{
              alertType : 'success',
              alertMsg : 'Пользователь успешно создан'
            }, { root: true })
            router.push( '/dashboard/users' );
          }, response => {
            reject(response)
            dispatch('openAlert',{
              alertType : 'danger',
              alertMsg : 'Произошла ошибка. Попробуйте еще раз'
            }, { root: true })
          });
        });
    },
    updateUser({ dispatch, commit, state }, payload) {
      return new Promise((resolve, reject) => {
        let {data, redirect} = payload;

        let formData = new FormData();
        if (payload.redirect === undefined){
          redirect = '/users';
        }
        
        for (const key in data) {
          if (data.hasOwnProperty(key)) {
            const element = data[key];
            if(element){
              formData.append(key, element);
            }
          }
        }
        
       Vue.http.post('users/update', formData)
        .then((response) => {
          return response.json();
        })
        .then(response => {
          console.log(response);
          dispatch('openAlert',{
            alertType : 'success',
            alertMsg : 'Данные пользователя успешно обновлены'
          }, { root: true })
          router.push( redirect );
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
    sendUsersPhotos({ commit, state }, payload) {
      let formData = new FormData();
      formData.append('id', payload);
      return new Promise((resolve, reject) => {
      Vue.http.post('users/photos', formData)
        .then((response) => {
          return response.json();
        })
        .then(response => {
          console.log(response);
          resolve(response)
        }, response => {
          reject(response)
        });
      })
    },
    getUsersPhotos({ commit, state }, payload) {
      let formData = new FormData();
      formData.append('id', payload);
      return new Promise((resolve, reject) => {
      Vue.http.post('users/photos', formData)
        .then((response) => {
          return response.json();
        })
        .then(response => {
          console.log(response);
          resolve(response)
        }, response => {
          reject(response)
        });
      })
    },
    checkEmailUniqueness({ commit, state }, payload) {
      let formData = new FormData();
      formData.append('email', payload);
      return new Promise((resolve, reject) => {
        Vue.http.post('checkEmailUniqueness', formData)
        .then((response) => {
          return response.json();
        })
        .then(response => {
          resolve(response)
        }, response => {
          reject(response)
        });
      })
    },
    deleteUser({ dispatch, commit, state }, payload) {
      return new Promise((resolve, reject) => {
        const {id, photo} = payload;

        const userId = !Number.isNaN(Number(id)) ? +id : null;
        let formData = new FormData();
        formData.append('id', userId);
        formData.append('photo', photo);
        if (userId) {
          Vue.http.post('users/delete', formData)
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

export default users;
