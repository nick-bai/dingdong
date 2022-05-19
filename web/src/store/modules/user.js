import { getToken, setToken, removeToken } from '@/utils/auth'
import router, { resetRouter } from '@/router'
import http from '@/utils/request'

const getDefaultState = () => {
  return {
    token: getToken(),
    name: '',
    avatar: '',
    userId: '',
    roleId: '',
    socket: ''
  }
}

const state = getDefaultState()

const mutations = {
  RESET_STATE: (state) => {
    Object.assign(state, getDefaultState())
  },
  SET_TOKEN: (state, token) => {
    state.token = token
  },
  SET_NAME: (state, name) => {
    state.name = name
  },
  SET_AVATAR: (state, avatar) => {
    state.avatar = avatar
  },
  SET_ROLE: (state, role) => {
    state.roleId = role
  },
  SET_USER_ID: (state, userId) => {
    state.userId = userId
  },
  SET_SOCKET: (state, socket) => {
    state.socket = socket
  }
}

const actions = {
  login({ commit }, res) {
    return new Promise((resolve, reject) => {
      commit('SET_TOKEN', res.data.token)
      commit('SET_ROLE', res.data.userInfo.role_id)
      commit('SET_USER_ID', res.data.userInfo.userId)
      localStorage.setItem('kefu_info', JSON.stringify(res.data.userInfo))
      setToken(res.data.token)
      resolve()
    })
  },

  // get user info
  getInfo({ commit }) {
    return new Promise((resolve, reject) => {
      http
        .get('/kefu/getMenuList')
        .then((response) => {
          const { data } = response

          if (!data) {
            return reject('验证失败，请重新登录!')
          }

          const { name, avatar, socket } = data

          commit('SET_NAME', name)
          commit('SET_AVATAR', avatar)
          commit('SET_SOCKET', socket)
          resolve(data)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },

  // user logout
  logout({ commit, state }) {
    // commit('DEL_TOKEN')
    commit('SET_NAME', '')
    removeToken() // must remove  token  first
    resetRouter()
    router.push({
      path: '/login',
      query: {
        redirect: '/'
      }
    })
    // return new Promise((resolve, reject) => {

    // })
  },

  // remove token
  resetToken({ commit }) {
    return new Promise((resolve) => {
      removeToken() // must remove  token  first
      commit('RESET_STATE')
      resolve()
    })
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
