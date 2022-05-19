import http from '@/utils/request'

export default {
  list: {
    url: '/kefu/index',
    name: '客服列表',
    get: async function(data = {}) {
      return await http.get(this.url, data)
    }
  },
  add: {
    url: '/kefu/add',
    name: '新增客服',
    post: async function(data = {}) {
      return await http.post(this.url, data)
    }
  },
  edit: {
    url: '/kefu/edit',
    name: '编辑',
    post: async function(data = {}) {
      return await http.post(this.url, data)
    }
  },
  del: {
    url: '/kefu/del',
    name: '删除客服',
    get: async function(data = {}) {
      return await http.get(this.url, data)
    }
  }
}
