import http from '@/utils/request'

export default {
  list: {
    url: '/word/index',
    name: '内容列表',
    get: async function(data = {}) {
      return await http.get(this.url, data)
    }
  },
  add: {
    url: '/word/add',
    name: '新增内容',
    post: async function(data = {}) {
      return await http.post(this.url, data)
    }
  },
  edit: {
    url: '/word/edit',
    name: '编辑内容',
    post: async function(data = {}) {
      return await http.post(this.url, data)
    }
  },
  del: {
    url: '/word/del',
    name: '删除内容',
    get: async function(data = {}) {
      return await http.get(this.url, data)
    }
  }
}
