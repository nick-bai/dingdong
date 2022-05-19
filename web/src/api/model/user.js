import http from '@/utils/request'

export default {
  login: {
    url: '/login',
    name: '登录获取TOKEN',
    post: async function(data = {}) {
      return await http.post(this.url, data)
    }
  },
  logout: {
    url: '/login/logout',
    name: '退出登录',
    get: async function(data = {}) {
      return await http.get(this.url, data)
    }
  },
  getInfo: {
    url: '/kefu/getUserInfo',
    name: '用户信息',
    get: async function(data = {}) {
      return await http.get(this.url, data)
    }
  },
  getSocket: {
    url: '/login/getSocket',
    name: '获取socket',
    get: async function(data = {}) {
      return await http.get(this.url, data)
    }
  },
  getChatLog: {
    url: '/login/getChatLog',
    name: '获取客服聊天记录',
    get: async function(data = {}) {
      return await http.get(this.url, data)
    }
  }
}
