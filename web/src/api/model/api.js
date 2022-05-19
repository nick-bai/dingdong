import http from '@/utils/request'

export default {
  getNowService: {
    url: '/api/getNowService',
    name: '获取当前服务的客户',
    get: async function(data = {}) {
      return await http.get(this.url, data)
    }
  },
  getChatLog: {
    url: '/api/getChatLog',
    name: '获取客服聊天记录',
    get: async function(data = {}) {
      return await http.get(this.url, data)
    }
  },
  getCustomerInfo: {
    url: '/api/getCustomerInfo',
    name: '获取访客信息',
    get: async function(data = {}) {
      return await http.get(this.url, data)
    }
  },
  upload: {
    url: '/upload/uploadImg',
    name: '上传文件',
    post: async function(data = {}) {
      return await http.post(this.url, data)
    }
  },
  home: {
    url: '/index/index',
    name: '首页统计',
    get: async function(data = {}) {
      return await http.get(this.url, data)
    }
  }
}
