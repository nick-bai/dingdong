<template>
  <el-container>
    <el-aside width="320px">
      <div class="chat-list">
        <div
          v-for="item in customerList"
          :key="item.id"
          :class="
            customerInfo.customer_id == item.customer_id
              ? 'chat-item  active'
              : 'chat-item'
          "
          @click="checkCustomer(item.customer_id)"
        >
          <div class="avatar">
            <img :src="item.avatar">
            <div :class="item.online_status == 1 ? 'status' : 'status off'" />
          </div>
          <div class="user-info">
            <div class="hd">
              <span class="name line1">{{ item.customer_name }}</span>
              <span class="label pc">PC端</span>
            </div>
            <div class="bd line1">{{ item.last_word }}</div>
          </div>
          <div class="right-box">
            <div class="time">{{ item.time }}</div>
            <div class="num">
              <el-badge
                v-if="item.unread_msg > 0"
                :value="item.unread_msg"
                class="item"
              />
            </div>
          </div>
        </div>
      </div>
    </el-aside>
    <el-container>
      <el-main>
        <div class="chat-box">
          <div
            id="content-box"
            ref="content_box"
            style="
              height: calc(100% - 214px);
              padding: 20px;
              overflow-y: scroll;
            "
          >
            <div
              v-for="item in chatLog"
              :key="item.id"
              :class="
                item.from_id == nowUser ? 'chat-item right-box' : 'chat-item'
              "
            >
              <div class="time">{{ item.time }}</div>
              <div class="flex-box">
                <div class="avatar">
                  <img
                    :src="
                      item.from_id == nowUser ? nowAvatar : item.from_avatar
                    "
                  >
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

          <div class="chat-textarea">
            <div class="chat-bar">
              <emotion
                v-if="showEmotion"
                style="
                  position: absolute;
                  top: calc(100% - 465px);
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
                @click="upfile"
              />

              <i
                class="el-icon-info"
                style="
                  font-size: 20px;
                  cursor: pointer;
                  margin-left: 20px;
                  color: #333;
                "
                @click="showWord"
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
    </el-container>
    <el-aside width="280px">
      <div class="customer-info user-wrapper">
        <div v-if="customerInfo.id > 0" class="user">
          <div class="avatar">
            <img :src="customerInfo.customer_avatar">
          </div>
          <div class="name line1">
            <span>{{ customerInfo.customer_name }}</span>
          </div>
          <div class="label">
            <span class="label pc">PC端</span>
          </div>
        </div>
        <div v-if="customerInfo.id > 0" class="user-info">
          <div class="item">
            <span>IP</span>
            <span>{{ customerInfo.customer_ip }}</span>
          </div>
          <div class="item">
            <span>地区</span>
            <span>{{ customerInfo.province }}{{ customerInfo.city }}</span>
          </div>
        </div>
      </div>
    </el-aside>
    <input
      ref="file"
      style="display: none"
      accept="image/bmp,image/jpeg,image/jpg,image/png"
      type="file"
      @change="fileChange"
    >
    <word-Dialog
      v-if="dialog.save"
      ref="wordDialog"
      @success="handleSaveSuccess"
      @closed="dialog.save = false"
    />
  </el-container>
</template>

<script>
import '../static/font/iconfont.css'
import emotion from '../static/Emotion/index.vue'
import { getShortDate } from '@/utils/index'
import wordDialog from './components/word'

export default {
  components: {
    emotion,
    wordDialog
  },
  data() {
    return {
      dialog: {
        save: false
      },
      nowUser: 0,
      nowAvatar: 0,
      content: '',
      showEmotion: false,
      customerList: [],
      chatLog: [],
      customerInfo: {
        id: 0,
        customer_id: '',
        customer_name: '',
        customer_avatar: '',
        customer_ip: '',
        province: '',
        city: ''
      },
      search: {
        customer_id: 0,
        limit: 15,
        page: 1
      },
      pageTotal: 0,
      uploadForm: new FormData()
    }
  },
  watch: {
    '$store.state.msg.newCustomer': function() {
      const newCustomer = this.$store.state.msg.newCustomer
      var hasIn = false
      this.customerList.forEach((ele, index) => {
        if (ele.customer_id === newCustomer.customer_id) {
          this.customerList[index].time = newCustomer.time
          this.customerList[index].online_status = newCustomer.online_status
          hasIn = true
          return
        }
      })

      if (!hasIn) {
        this.customerList.push(newCustomer)
      }

      // 默认选中第一个
      if (this.customerList.length > 0) {
        this.checkCustomer(this.customerList[0].customer_id)
      }
    },
    '$store.state.msg.newMsg': function() {
      const newMsg = this.$store.state.msg.newMsg
      if (newMsg.from_id === this.customerInfo.customer_id) {
        this.chatLog.push(newMsg)
        this.customerList.forEach((ele, index) => {
          if (ele.customer_id === this.customerInfo.customer_id) {
            this.customerList[index].last_word = newMsg.content
            return
          }
        })
        this.slide()
      } else {
        this.customerList.forEach((ele, index) => {
          if (ele.customer_id === newMsg.from_id) {
            this.customerList[index].unread_msg = parseInt(ele.unread_msg) + 1
            this.customerList[index].last_word = newMsg.content
            return
          }
        })
      }
    },
    '$store.state.msg.customerOut': {
      handler: function(customerId) {
        const customer_id = customerId.split('_')[0]
        this.customerList.forEach((ele, index) => {
          if (ele.customer_id === customer_id) {
            this.customerList[index].online_status = 0
            return
          }
        })
      },
      immediate: true
    }
  },
  created() {
    this.$store.commit('SET_UNREAD_MSG', 0)
    this.nowUser = JSON.parse(localStorage.getItem('kefu_info')).code
    this.nowAvatar = JSON.parse(localStorage.getItem('kefu_info')).avatar
    this.getNowService()
  },
  mounted() {
    this.$refs.content_box.addEventListener('scroll', this.lazyLoading)
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
    checkCustomer(customer_id) {
      this.customerList.forEach((ele, index) => {
        if (ele.customer_id === customer_id) {
          this.customerList[index].unread_msg = 0
          return
        }
      })

      this.search.page = 1
      this.showChatLog(customer_id, 1)
      this.getCustomerInfo(customer_id)
    },
    async getNowService() {
      const res = await this.$api.api.getNowService.get()
      this.customerList = res.data

      // 默认选中第一个
      if (this.customerList.length > 0) {
        this.checkCustomer(this.customerList[0].customer_id)
      }
    },
    async showChatLog(customer_id, type) {
      const loading = this.$loading({
        lock: true,
        text: '记录加载中...',
        spinner: 'el-icon-loading',
        background: 'rgba(0, 0, 0, 0.7)',
        target: document.querySelector('#content-box')
      })

      this.search.customer_id = customer_id
      const res = await this.$api.api.getChatLog.get(this.search)
      loading.close()

      if (type === 1) {
        this.chatLog = res.data.data
        this.slide()
      } else {
        this.chatLog = res.data.data.concat(this.chatLog)
      }

      this.pageTotal = res.data.last_page
    },
    async getCustomerInfo(customer_id) {
      const res = await this.$api.api.getCustomerInfo.get({
        customer_id: customer_id
      })

      this.customerInfo = res.data
    },
    lazyLoading(e) {
      // 滚动条到顶部
      if (e.target.scrollTop < 70) {
        this.search.page++
        if (this.search.page > this.pageTotal) return
        this.showChatLog(this.customerInfo.customer_id, 2)
      }
    },
    async fileChange(event) {
      var e = window.event || event
      var oFile = e.target.files[0]
      this.uploadForm.append('file', oFile)

      const loading = this.$loading({
        lock: true,
        text: '上传中...',
        spinner: 'el-icon-loading'
      })
      const res = await this.$api.api.upload.post(this.uploadForm)
      this.content = res.data.src
      this.send('image')
      loading.close()
    },
    upfile() {
      this.$refs.file.click()
    },
    send(type) {
      if (
        this.content.length === 0 ||
        this.content.replace(/^\s*|\s*$/g, '') === ''
      ) {
        this.$message.error('请输入内容')
        return
      }

      if (this.customerInfo.customer_id === '') {
        this.$message.error('请选择访客')
        return
      }

      let msType = 'text'
      if (type) {
        msType = 'image'
      }

      const kefuInfo = JSON.parse(localStorage.getItem('kefu_info'))

      const chatMessage = {
        from_id: kefuInfo.code,
        from_name: kefuInfo.name,
        from_avatar: kefuInfo.avatar,
        to_id: this.customerInfo.customer_id,
        to_name: this.customerInfo.customer_name,
        to_avatar: this.customerInfo.customer_avatar,
        content: this.content,
        msg_type: msType
      }

      this.$socket.emit('S2C', JSON.stringify(chatMessage))
      chatMessage.time = getShortDate()

      this.customerList.forEach((ele, index) => {
        if (ele.customer_id === this.customerInfo.customer_id) {
          this.customerList[index].last_word = this.content
          return
        }
      })

      this.content = ''
      this.chatLog.push(chatMessage)
      this.slide()
    },
    showWord() {
      console.log('显示')
      this.dialog.save = true
      this.$nextTick(() => {
        this.$refs.wordDialog.open()
      })
    },
    handleSaveSuccess(res) {
      this.content += res.word
      this.dialog.save = false
    }
  }
}
</script>

<style>
.chat-list,
.customer-info {
  background: #fff;
  height: calc(100vh - 100px);
}
.customer-info {
  border-left: 1px solid #ececec;
}
.chat-list {
  border-right: 1px solid #ececec;
}
.chat-header {
  text-align: left;
  line-height: 60px;
  height: 100%;
  background: #fff;
  border-bottom: 1px solid #ececec;
}
.chat-box {
  height: calc(100vh - 100px);
  background: #fff;
}
.chat-list .chat-item {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: center;
  -ms-flex-align: center;
  align-items: center;
  -webkit-box-pack: justify;
  -ms-flex-pack: justify;
  justify-content: space-between;
  padding: 12px 10px;
  height: 74px;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
  border-left: 3px solid transparent;
  cursor: pointer;
}
.chat-list .chat-item .avatar {
  position: relative;
  width: 40px;
  height: 40px;
}
.chat-list .chat-item .avatar img {
  display: block;
  width: 100%;
  height: 100%;
  border-radius: 50%;
}
img {
  border-style: none;
}
.chat-list .chat-item .avatar .status.off {
  background: #999;
}

.chat-list .chat-item .avatar .status {
  position: absolute;
  right: 3px;
  bottom: 0;
  width: 8px;
  height: 8px;
  background: #48d452;
  border: 1px solid #fff;
  border-radius: 50%;
}
.chat-list .chat-item .user-info {
  width: 155px;
  margin-left: 12px;
  margin-top: 5px;
  font-size: 16px;
}
.chat-list .chat-item .user-info .hd {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: center;
  -ms-flex-align: center;
  align-items: center;
  color: rgba(0, 0, 0, 0.65);
}
.chat-list .chat-item .user-info .hd .name {
  max-width: 67%;
}

.line1,
.line2 {
  overflow: hidden;
  text-overflow: ellipsis;
}
.line1 {
  white-space: nowrap;
}
.chat-list .chat-item .user-info .hd .label.pc {
  background: rgba(100, 64, 194, 0.16);
  color: #6440c2;
}

.chat-list .chat-item .user-info .hd .label {
  margin-left: 5px;
  color: #3875ea;
  font-size: 12px;
  background: #d8e5ff;
  border-radius: 2px;
  padding: 1px 5px;
}
.chat-list .chat-item .user-info .bd {
  margin-top: 3px;
  font-size: 12px;
  color: #8e959e;
}

.line1,
.line2 {
  overflow: hidden;
  text-overflow: ellipsis;
}
.chat-list .chat-item .right-box {
  position: relative;
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
  -webkit-box-align: end;
  -ms-flex-align: end;
  align-items: flex-end;
  color: #8e959e;
}
.chat-list .chat-item .right-box .num {
  margin-right: 12px;
}
.chat-list .chat-item.active {
  background: #eff0f1;
  border-left: 3px solid #1890ff;
}
.chat-list .time {
  font-size: 12px;
}
.chat-list .num {
  margin-top: 10px;
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
.chat-box .chat-item.right-box .flex-box .avatar {
  margin-right: 0;
  margin-left: 16px;
}
.chat-textarea {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-orient: vertical;
  -webkit-box-direction: normal;
  -ms-flex-direction: column;
  flex-direction: column;
  height: 214px;
  border-top: 1px solid #ececec;
}
.chat-bar {
  height: 50px;
  width: 100%;
  line-height: 50px;
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
.user-wrapper {
  padding: 0 8px;
}
.user-wrapper .user {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: center;
  -ms-flex-align: center;
  align-items: center;
  padding: 16px 0;
  border-bottom: 1px solid #ececec;
}
.user-wrapper .user .avatar {
  width: 42px;
  height: 42px;
}
.user-wrapper .user .avatar img {
  display: block;
  width: 100%;
  height: 100%;
  border-radius: 50%;
}
.user-wrapper .user .name {
  max-width: 150px;
  margin-left: 10px;
  font-size: 16px;
  color: rgba(0, 0, 0, 0.65);
}
.user-wrapper .user .label {
  margin-left: 5px;
  font-size: 12px;
  border-radius: 2px;
  padding: 2px 5px;
}
.user-wrapper .user .label.pc {
  background: rgba(100, 64, 194, 0.16);
  color: #6440c2;
}

.user-wrapper .user .label {
  margin-left: 5px;
  font-size: 12px;
  border-radius: 2px;
  padding: 2px 5px;
}

.user-info {
  padding-top: 15px;
  padding-bottom: 10px;
}
.user-info .item {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: center;
  -ms-flex-align: center;
  align-items: center;
  margin-bottom: 10px;
  font-size: 14px;
  color: #333;
}
.user-info .item span {
  width: 70px;
  font-size: 13px;
  color: #666;
}
.emotion-box-line {
  cursor: pointer;
}
.textarea-box textarea {
  height: 120px;
  resize: none;
  border: none;
  background: transparent;
  border-style: none;
  font-size: 14px;
  outline: none;
  padding: 0px 15px;
}
.emotion-box {
  border: 1px solid #e2e2e2 !important;
}
.chat-item .msg-wrapper .img-wraper img {
  max-width: 100%;
  height: auto;
  display: block;
}
</style>
