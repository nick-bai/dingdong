<template>
  <div :class="classObj" class="app-wrapper">
    <div
      v-if="device === 'mobile' && sidebar.opened"
      class="drawer-bg"
      @click="handleClickOutside"
    />
    <sidebar class="sidebar-container" />
    <div class="main-container">
      <div :class="{ 'fixed-header': fixedHeader }">
        <navbar />
      </div>
      <app-main />
    </div>
  </div>
</template>

<script>
import { Navbar, Sidebar, AppMain } from './components'
import ResizeMixin from './mixin/ResizeHandler'
import { playSound } from '@/utils/index'

export default {
  name: 'Layout',
  components: {
    Navbar,
    Sidebar,
    AppMain
  },
  mixins: [ResizeMixin],
  data() {
    return {
      kefuInfo: {}
    }
  },
  computed: {
    sidebar() {
      return this.$store.state.app.sidebar
    },
    device() {
      return this.$store.state.app.device
    },
    fixedHeader() {
      return this.$store.state.settings.fixedHeader
    },
    classObj() {
      return {
        hideSidebar: !this.sidebar.opened,
        openSidebar: this.sidebar.opened,
        withoutAnimation: this.sidebar.withoutAnimation,
        mobile: this.device === 'mobile'
      }
    }
  },
  watch: {
    '$store.state.msg.kefuOut': function() {
      this.$socket.emit('KEFU_OUT', this.$store.state.kefuOut)
    }
  },
  created() {
    this.kefuInfo = JSON.parse(localStorage.getItem('kefu_info'))
    this.$message.success('链接服务器成功')
    this.$socket.emit('KEFU_LOGIN', JSON.stringify(this.kefuInfo))
  },
  sockets: {
    connect() {
      /* this.$message.success('链接服务器成功')
      // 客服登录
      this.kefuInfo = JSON.parse(localStorage.getItem('kefu_info'))
      this.$message.success('链接服务器成功')
      this.$socket.emit('KEFU_LOGIN', JSON.stringify(this.kefuInfo))*/
    },
    CUSTOMER_IN(res) {
      playSound('new_customer.mp3')
      this.$store.commit('SET_NEW_CUSTOMER', res)
    },
    CHAT(res) {
      playSound('new_msg.wav')
      if (window.location.hash === '#/work/index') {
        this.$store.commit('SET_NEW_MSG', res)
      } else {
        const unreadMsg = parseInt(this.$store.getters.unreadMsg) + 1
        this.$store.commit('SET_UNREAD_MSG', unreadMsg)
      }
    },
    CUSTOMER_OUT(res) {
      console.log('收到了访客离线', res)
      this.$store.commit('SET_CUSTOMER_OUT', res + '_' + this.randomNumber())
    },
    reconnect() {
      // 重连
      this.$socket.emit('KEFU_LOGIN', JSON.stringify(this.kefuInfo))
    }
  },
  methods: {
    handleClickOutside() {
      this.$store.dispatch('app/closeSideBar', { withoutAnimation: false })
    },
    randomNumber() {
      const now = new Date()

      let randStr = ''
      for (var i = 0; i < 8; i++) {
        randStr += Math.floor(Math.random() * 10)
      }

      return now.getFullYear().toString() + randStr
    }
  }
}
</script>

<style lang="scss" scoped>
@import '~@/styles/mixin.scss';
@import '~@/styles/variables.scss';

.app-wrapper {
  @include clearfix;
  position: relative;
  height: 100%;
  width: 100%;
  &.mobile.openSidebar {
    position: fixed;
    top: 0;
  }
}
.drawer-bg {
  background: #000;
  opacity: 0.3;
  width: 100%;
  top: 0;
  height: 100%;
  position: absolute;
  z-index: 999;
}

.fixed-header {
  position: fixed;
  top: 0;
  right: 0;
  z-index: 9;
  width: calc(100% - #{$sideBarWidth});
  transition: width 0.28s;
}

.hideSidebar .fixed-header {
  width: calc(100% - 54px);
}

.mobile .fixed-header {
  width: 100%;
}
</style>
