<template>
  <div class="chat-bg">
    <div class="chat-box">
      <el-container>
        <el-header class="chat-title">
          <img :src="serviceKF.avatar">{{ serviceKF.name }}</el-header>
        <el-container>
          <el-main>
            <div class="chat-content">
              <div
                id="content-box"
                ref="content_box"
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
                    item.from_id == nowUser
                      ? 'chat-item right-box'
                      : 'chat-item'
                  "
                >
                  <div class="time">{{ item.time }}</div>
                  <div class="flex-box">
                    <div class="avatar">
                      <img
                        :src="
                          item.from_id == nowUser
                            ? custmerInfo.customer_avatar
                            : item.from_avatar
                        "
                      >
                    </div>
                    <div class="msg-wrapper">
                      <div
                        v-if="item.msg_type == 'text'"
                        class="txt-wrapper pad16"
                      >
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
                <p
                  v-if="offline"
                  style="font-size: 12px; text-align: center; color: #ff5722"
                >
                  客服不在线，请稍后再来。
                </p>
              </div>

              <div class="chat-textarea">
                <div class="chat-bar">
                  <emotion
                    v-if="showEmotion"
                    style="
                      position: absolute;
                      top: calc(100% - 375px);
                      background: #fff;
                    "
                    :height="200"
                    @emotion="handleEmotion"
                  />
                  <i
                    class="iconfonts icon-xiaolian"
                    alt="表情"
                    style="
                      font-size: 20px;
                      cursor: pointer;
                      margin-left: 20px;
                      color: #333;
                    "
                    @click.stop="showEmotion = true"
                  />
                  <i
                    class="el-icon-picture-outline"
                    style="
                      font-size: 20px;
                      cursor: pointer;
                      margin-left: 20px;
                      color: #333;
                    "
                  />
                </div>
                <div class="textarea-box">
                  <textarea
                    ref="content"
                    v-model="content"
                    style="width: 100%"
                    placeholder="输入内容"
                  />
                  <div class="send-btn">
                    <el-button
                      type="primary"
                      size="small"
                      @click="send('')"
                    >发送</el-button>
                  </div>
                </div>
              </div>
            </div>
          </el-main>
          <el-aside
            width="200px"
            style="border-left: 1px solid #ececec; padding: 10px"
          >
            <h3>公司简介</h3>
            <p style="font-size: 12px; line-height: 20px">
              深圳市腾讯计算机系统有限公司成立于1998年11月，由马化腾、张志东、许晨晔、陈一丹、曾李青五位创始人共同创立。<br>
              腾讯多元化的服务包括：社交和通信服务QQ及微信/WeChat、社交网络平台QQ空间、腾讯游戏旗下QQ游戏平台、门户网站腾讯网、腾讯新闻客户端和网络视频服务腾讯视频等。
            </p>
          </el-aside>
        </el-container>
      </el-container>
    </div>
  </div>
</template>

<script>
import '../static/font/iconfont.css'
import emotion from '../static/Emotion/index.vue'
import { getShortDate, playSound } from '@/utils/index'

export default {
  components: {
    emotion
  },
  data() {
    return {
      serviceKF: {
        avatar: process.env.VUE_APP_BASE_API + '/static/avatar/2.png',
        name: '官方客服'
      },
      offline: false,
      showEmotion: false,
      nowUser: 0,
      chatLog: [],
      content: '',
      kefuInfo: {},
      custmerInfo: {},
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
    document.title = '访客咨询'
    this.initUser()
  },
  mounted() {
    this.$refs.content_box.addEventListener('scroll', this.lazyLoading)
  },
  sockets: {
    connect() {
      console.log('链接服务器成功')
      /* this.$message.success('链接服务器成功')
      this.$socket.emit('CUSTOMER_LOGIN', JSON.stringify(this.custmerInfo))*/
    },
    LOGIN_SUCCESS(res) {
      this.kefuInfo = res
      this.showChatLog(this.nowUser, res.code, 1)
      this.serviceKF.avatar = res.avatar
      this.serviceKF.name = res.name
    },
    KEFU_OFFLINE() {
      this.offline = true
    },
    CHAT(res) {
      playSound('new_msg.wav')
      this.chatLog.push(res)
      this.slide()
    },
    reconnect() {
      // 重连
      this.$socket.emit('CUSTOMER_LOGIN', JSON.stringify(this.custmerInfo))
    }
  },
  methods: {
    slide() {
      this.$nextTick(() => {
        setTimeout(() => {
          document.getElementById('content-box').scrollTop =
            document.getElementById('content-box').scrollHeight
        }, 200)
      })
    },
    handleEmotion(i) {
      this.content += i
      this.showEmotion = false
      this.$refs.content.focus()
    },
    randomNumber() {
      const now = new Date()

      let randStr = ''
      for (var i = 0; i < 8; i++) {
        randStr += Math.floor(Math.random() * 10)
      }

      return now.getFullYear().toString() + randStr
    },
    lazyLoading(e) {
      // 滚动条到顶部
      if (e.target.scrollTop < 70) {
        this.search.page++
        if (this.search.page > this.pageTotal) return
        const mine = JSON.parse(localStorage.getItem('customer_info'))
        this.showChatLog(mine.customer_id, this.kefuInfo.code, 2)
      }
    },
    initUser() {
      this.custmerInfo = localStorage.getItem('customer_info')
      if (!this.custmerInfo) {
        const uid = this.randomNumber()
        this.custmerInfo = {
          customer_id: uid,
          customer_name: '访客' + uid,
          customer_avatar: config.API_URL + '/static/avatar/1.png'
        }
        localStorage.setItem('customer_info', JSON.stringify(this.custmerInfo))
      } else {
        this.custmerInfo = JSON.parse(this.custmerInfo)
      }

      this.$message.success('链接服务器成功')
      this.$socket.emit('CUSTOMER_LOGIN', JSON.stringify(this.custmerInfo))
      this.nowUser = this.custmerInfo.customer_id
    },
    async showChatLog(customer_id, code, type) {
      this.search.customer_id = customer_id
      this.search.code = code
      const res = await this.$api.user.getChatLog.get(this.search)

      if (type === 1) {
        this.chatLog = res.data.data
        this.slide()
      } else {
        this.chatLog = res.data.data.concat(this.chatLog)
      }

      this.pageTotal = res.data.last_page
    },
    send() {
      if (
        this.content.length === 0 ||
        this.content.replace(/^\s*|\s*$/g, '') === ''
      ) {
        this.$message.error('请输入内容')
        return
      }

      const chatMessage = {
        from_id: this.custmerInfo.customer_id,
        from_name: this.custmerInfo.customer_name,
        from_avatar: this.custmerInfo.customer_avatar,
        to_id: this.kefuInfo.code,
        to_name: this.kefuInfo.name,
        to_avatar: this.kefuInfo.avatar,
        content: this.content,
        msg_type: 'text'
      }

      this.$socket.emit('C2S', JSON.stringify(chatMessage))
      chatMessage.time = getShortDate()
      this.content = ''
      this.chatLog.push(chatMessage)
      this.slide()
    }
  }
}
</script>

<style>
.chat-bg {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.7);
}
.chat-box {
  display: flex;
  -webkit-box-pack: justify;
  -ms-flex-pack: justify;
  justify-content: space-between;
  width: 100%;
  height: 100%;
  max-width: 840px;
  max-height: 654px;
  -webkit-box-orient: vertical;
  -webkit-box-direction: normal;
  -ms-flex-direction: column;
  flex-direction: column;
  background: #fff;
  position: fixed;
  top: 50%;
  left: 50%;
  -webkit-transform: translate(-50%, -50%);
  transform: translate(-50%, -50%);
  -webkit-box-shadow: 1px 1px 15px 0 rgb(0 0 0 / 30%);
  box-shadow: 1px 1px 15px 0 rgb(0 0 0 / 30%);
  border-radius: 8px;
  overflow: hidden;
}
.chat-title {
  background: linear-gradient(270deg, #1890ff, #3875ea);
  text-align: left;
  line-height: 60px;
  color: #fff;
  font-size: 16px;
}
.chat-title img {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  margin-right: 10px;
  top: 10px;
  position: relative;
}
.chat-textarea {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-orient: vertical;
  -webkit-box-direction: normal;
  -ms-flex-direction: column;
  flex-direction: column;
  height: 175px;
  border-top: 1px solid #ececec;
}
.chat-bar {
  height: 40px;
  width: 100%;
  line-height: 40px;
  text-align: left;
}
.chat-textarea .textarea-box {
  -webkit-box-flex: 1;
  -ms-flex: 1;
  flex: 1;
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-orient: vertical;
  -webkit-box-direction: normal;
  -ms-flex-direction: column;
  flex-direction: column;
  min-height: 0;
  position: relative;
}
.chat-textarea .editable {
  -webkit-box-flex: 1;
  -ms-flex: 1;
  flex: 1;
  padding: 4px 7px;
  overflow-x: hidden;
  overflow-y: auto;
  font-size: 14px;
  line-height: 1.5;
  color: #515a6e;
}
.send-btn {
  position: absolute;
  right: 0;
  bottom: 10px;
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: end;
  -ms-flex-pack: end;
  justify-content: flex-end;
  margin-top: 10px;
  margin-right: 10px;
}
.textarea-box textarea {
  height: 95px;
  resize: none;
  border: none;
  background: transparent;
  border-style: none;
  font-size: 14px;
  outline: none;
  padding: 0px 15px;
}
.chat-box .chat-item {
  margin-bottom: 10px;
}
.chat-box .chat-item .time {
  text-align: center;
  color: #999;
  font-size: 14px;
  margin: 18px 0;
}
.chat-box .chat-item .flex-box {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
}
.chat-box .chat-item .avatar {
  width: 40px;
  height: 40px;
  margin-right: 16px;
}
.chat-box .chat-item .avatar img {
  display: block;
  width: 100%;
  height: 100%;
  border-radius: 50%;
}
.chat-box .chat-item .msg-wrapper {
  max-width: 320px;
  background: #f5f5f5;
  border-radius: 10px;
  color: #000;
  font-size: 14px;
  overflow: hidden;
}
.chat-box .chat-item .msg-wrapper .pad16 {
  padding: 12px;
}

.chat-item .msg-wrapper .txt-wrapper {
  word-break: break-all;
}
.chat-box .chat-item.right-box .flex-box {
  -webkit-box-orient: horizontal;
  -webkit-box-direction: reverse;
  -ms-flex-direction: row-reverse;
  flex-direction: row-reverse;
}
.emotion-box {
  border: 1px solid #e2e2e2 !important;
}
.emotion-box-line {
  cursor: pointer;
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
