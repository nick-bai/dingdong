import http from '@/utils/request'

export default {
  list: {
    url: '/customer/index',
    name: '获取访客',
    get: async function(data = {}) {
      return await http.get(this.url, data)
    }
  },
  serviceLog: {
    url: '/customer/serviceLog',
    name: '获取访客',
    get: async function(data = {}) {
      return await http.get(this.url, data)
    }
  },
  getChatLog: {
    url: '/customer/getChatLog',
    name: '获取聊天记录',
    get: async function(data = {}) {
      return await http.get(this.url, data)
    }
  }
}
