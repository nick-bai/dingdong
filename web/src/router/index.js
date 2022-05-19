import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

/* Layout */
import Layout from '@/layout'

/**
 * constantRoutes
 * a base page that does not have permission requirements
 * all roles can be accessed
 */
export const constantRoutes = [
  {
    path: '/login',
    component: () => import('@/views/login/index'),
    hidden: true
  },
  {
    path: '/chat',
    component: () => import('@/views/chat/index'),
    hidden: true
  },
  {
    path: '/',
    component: Layout,
    redirect: '/dashboard',
    children: [
      {
        path: 'dashboard',
        name: '首页',
        component: () => import('@/views/dashboard/index'),
        meta: {
          title: '首页',
          icon: 'el-icon-s-home'
        }
      }
    ]
  }
]

// 动态异步路由
export const asyncRoutes = [
  {
    path: '/work',
    component: Layout,
    children: [
      {
        path: 'index',
        name: '对话',
        component: () => import('@/views/work/index'),
        meta: { title: '对话', icon: 'el-icon-s-comment' }
      }
    ]
  },
  // 客服管理
  {
    path: '/kefu',
    component: Layout,
    children: [
      {
        path: 'index',
        name: '客服',
        component: () => import('@/views/kefu/index'),
        meta: { title: '客服', icon: 'el-icon-s-custom' }
      }
    ]
  },
  {
    path: '/word',
    component: Layout,
    children: [
      {
        path: 'index',
        name: '常用语',
        component: () => import('@/views/word/index'),
        meta: { title: '常用语', icon: 'el-icon-info' }
      }
    ]
  },
  {
    path: '/customer',
    component: Layout,
    children: [
      {
        path: 'index',
        name: '访客',
        component: () => import('@/views/customer/index'),
        meta: { title: '访客', icon: 'el-icon-user' }
      }
    ]
  }
]

const createRouter = () =>
  new Router({
    // mode: 'history', // require service support
    scrollBehavior: () => ({
      y: 0
    }),
    routes: constantRoutes
  })

const router = createRouter()

// Detail see: https://github.com/vuejs/vue-router/issues/1234#issuecomment-357941465
export function resetRouter() {
  const newRouter = createRouter()
  router.matcher = newRouter.matcher // reset router
}

export default router
