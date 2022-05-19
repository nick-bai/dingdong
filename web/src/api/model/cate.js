import http from '@/utils/request'

export default {
  list: {
    url: '/wordcate/index',
    name: '分类列表',
    get: async function(data = {}) {
      return await http.get(this.url, data)
    }
  },
  add: {
    url: '/wordcate/add',
    name: '新增分类',
    post: async function(data = {}) {
      return await http.post(this.url, data)
    }
  },
  del: {
    url: '/wordcate/del',
    name: '删除分类',
    get: async function(data = {}) {
      return await http.get(this.url, data)
    }
  }
}
