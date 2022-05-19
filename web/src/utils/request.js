import axios from 'axios'
import { Message } from 'element-ui'
import store from '@/store'
import { getToken, removeToken } from '@/utils/auth'

axios.defaults.baseURL = process.env.VUE_APP_BASE_API
axios.defaults.timeout = 5000

// request interceptor
axios.interceptors.request.use(
  (config) => {
    // do something before request is sent

    if (store.getters.token) {
      // let each request carry token
      // ['X-Token'] is a custom headers key
      // please modify it according to the actual situation
      config.headers['authorization'] = 'Bearer ' + getToken()
    }
    return config
  },
  (error) => {
    // do something with request error
    console.log(error) // for debug
    return Promise.reject(error)
  }
)

// response interceptor
axios.interceptors.response.use(
  /**
   * If you want to get http information such as headers or status
   * Please return  response => response
   */

  /**
   * Determine the request status by custom code
   * Here is just an example
   * You can also judge the status by HTTP Status Code
   */
  (response) => {
    const res = response.data

    // if the custom code is not 20000, it is judged as an error.
    if (res.code !== 0) {
      // 50008: Illegal token; 50012: Other clients logged in; 50014: Token expired;
      if (res.code === 413) {
        // to re-login
        removeToken()
        this.$router.push({ path: '/login' })
      }
      return res
    } else {
      return res
    }
  },
  (error) => {
    console.log('err' + error) // for debug
    Message({
      message: error.msg,
      type: 'error',
      duration: 5 * 1000
    })
    return Promise.reject(error)
  }
)

var http = {
  /** get 请求
   * @param  {接口地址} url
   * @param  {请求参数} params
   * @param  {参数} config
   */
  get: function(url, params = {}, config = {}) {
    return new Promise((resolve, reject) => {
      axios({
        method: 'get',
        url: url,
        params: params,
        ...config
      })
        .then((response) => {
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },

  /** post 请求
   * @param  {接口地址} url
   * @param  {请求参数} data
   * @param  {参数} config
   */
  post: function(url, data = {}, config = {}) {
    return new Promise((resolve, reject) => {
      axios({
        method: 'post',
        url: url,
        data: data,
        ...config
      })
        .then((response) => {
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },

  /** put 请求
   * @param  {接口地址} url
   * @param  {请求参数} data
   * @param  {参数} config
   */
  put: function(url, data = {}, config = {}) {
    return new Promise((resolve, reject) => {
      axios({
        method: 'put',
        url: url,
        data: data,
        ...config
      })
        .then((response) => {
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },

  /** patch 请求
   * @param  {接口地址} url
   * @param  {请求参数} data
   * @param  {参数} config
   */
  patch: function(url, data = {}, config = {}) {
    return new Promise((resolve, reject) => {
      axios({
        method: 'patch',
        url: url,
        data: data,
        ...config
      })
        .then((response) => {
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },

  /** delete 请求
   * @param  {接口地址} url
   * @param  {请求参数} data
   * @param  {参数} config
   */
  delete: function(url, data = {}, config = {}) {
    return new Promise((resolve, reject) => {
      axios({
        method: 'delete',
        url: url,
        data: data,
        ...config
      })
        .then((response) => {
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },

  /** jsonp 请求
   * @param  {接口地址} url
   * @param  {JSONP回调函数名称} name
   */
  jsonp: function(url, name = 'jsonp') {
    return new Promise((resolve) => {
      var script = document.createElement('script')
      var _id = `jsonp${Math.ceil(Math.random() * 1000000)}`
      script.id = _id
      script.type = 'text/javascript'
      script.src = url
      window[name] = (response) => {
        resolve(response)
        document.getElementsByTagName('head')[0].removeChild(script)
        try {
          delete window[name]
        } catch (e) {
          window[name] = undefined
        }
      }
      document.getElementsByTagName('head')[0].appendChild(script)
    })
  }
}

export default http
