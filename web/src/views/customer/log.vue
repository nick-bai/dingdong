<template>
  <el-dialog
    title="聊天日志"
    :visible.sync="visible"
    destroy-on-close
    width="600px"
    custom-class="menu-dialog-height"
    @closed="$emit('closed')"
  >
    <el-container>
      <el-main>
        <div class="chat-content">
          <div
            id="content-box"
            ref="content"
            style="
              width: 100%;
              height: 421px;
              padding: 10px;
              overflow-y: scroll;
            "
          >
            <div
              v-for="item in chatLog"
              :key="item.id"
              :class="
                isNaN(item.from_id) ? 'chat-item  right-box' : 'chat-item'
              "
            >
              <div class="time">{{ item.time }}</div>
              <div class="flex-box">
                <div class="avatar">
                  <img :src="item.from_avatar">
                </div>
                <div class="msg-wrapper">
                  <div v-if="item.msg_type == 'text'" class="txt-wrapper pad16">
                    {{ item.content }}
                  </div>
                  <div v-else class="img-wraper">
                    <el-image
                      :src="item.content"
                      :preview-src-list="[item.content]"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </el-main>
    </el-container>
  </el-dialog>
</template>

<script>
export default {
  name: 'ChatLog',
  props: ['customer_id'],
  data() {
    return {
      visible: false,
      chatLog: [],
      nowUser: 0,
      search: {
        customer_id: 0,
        code: '',
        limit: 15,
        page: 1
      },
      pageTotal: 0
    }
  },
  created() {
    this.showChatLog(this.customer_id, 1)
  },
  mounted() {
    setTimeout(() => {
      this.$refs.content.addEventListener('scroll', this.lazyLoading)
    }, 200)
  },
  methods: {
    open() {
      this.visible = true
      return this
    },
    slide() {
      this.$nextTick(() => {
        setTimeout(() => {
          this.$refs.content.scrollTop = this.$refs.content.scrollHeight
        }, 200)
      })
    },
    lazyLoading(e) {
      // 滚动条到顶部
      if (e.target.scrollTop < 80) {
        this.search.page++
        if (this.search.page > this.pageTotal) return
        this.showChatLog(this.customer_id, 2)
      }
    },
    async showChatLog(customer_id, type) {
      this.search.customer_id = customer_id
      const res = await this.$api.customer.getChatLog.get(this.search)

      if (type === 1) {
        this.chatLog = res.data.data
        this.slide()
      } else {
        this.chatLog = res.data.data.concat(this.chatLog)
      }

      this.pageTotal = res.data.last_page
    }
  }
}
</script>

<style scoped>
.chat-item {
  margin-bottom: 10px;
}
.chat-item .time {
  text-align: center;
  color: #999;
  font-size: 14px;
  margin: 18px 0;
}
.chat-item .flex-box {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
}
.chat-item .avatar {
  width: 40px;
  height: 40px;
  margin-right: 16px;
}
.chat-item .avatar img {
  display: block;
  width: 100%;
  height: 100%;
  border-radius: 50%;
}
.chat-item .msg-wrapper {
  max-width: 320px;
  background: #f5f5f5;
  border-radius: 10px;
  color: #000;
  font-size: 14px;
  overflow: hidden;
}
.chat-item .msg-wrapper .pad16 {
  padding: 12px;
}

.chat-item .msg-wrapper .txt-wrapper {
  word-break: break-all;
}
.chat-item.right-box .flex-box {
  -webkit-box-orient: horizontal;
  -webkit-box-direction: reverse;
  -ms-flex-direction: row-reverse;
  flex-direction: row-reverse;
}
.chat-item.right-box .flex-box .avatar {
  margin-right: 0;
  margin-left: 16px;
}
.chat-item .msg-wrapper .img-wraper img {
  max-width: 100%;
  height: auto;
  display: block;
}
</style>
