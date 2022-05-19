<template>
  <div class="login-container">
    <div class="login">
      <el-form
        v-if="title == '登录'"
        ref="loginForm"
        :model="loginForm"
        :rules="loginRules"
        class="form"
        auto-complete="on"
        label-position="left"
      >
        <div class="title-container">
          <h3 class="title">客服登录</h3>
        </div>

        <el-form-item prop="phone">
          <el-input
            ref="username"
            v-model="loginForm.phone"
            prefix-icon="el-icon-mobile-phone"
            placeholder="手机号"
            name="name"
            type="text"
            tabindex="1"
            auto-complete="on"
          />
        </el-form-item>

        <el-form-item prop="password">
          <el-input
            :key="passwordType"
            ref="password"
            v-model="loginForm.password"
            :type="passwordType"
            placeholder="密码"
            prefix-icon="el-icon-message"
            name="password"
            tabindex="2"
            auto-complete="on"
            @keyup.enter.native="handleLogin"
          />
        </el-form-item>
        <el-button
          :loading="loading"
          type="primary"
          style="width: 100%; margin-bottom: 30px"
          @click.native.prevent="handleLogin"
        >登录</el-button>
      </el-form>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Login',
  data() {
    return {
      title: '登录',
      loginForm: {
        phone: '',
        password: ''
      },
      loginRules: {
        phone: [{ required: true, trigger: 'blur', message: '请输入手机号' }],
        password: [{ required: true, trigger: 'blur', message: '请输入密码' }]
      },
      loading: false,
      passwordType: 'password',
      redirect: undefined
    }
  },
  watch: {
    $route: {
      handler: function(route) {
        this.redirect = route.query && route.query.redirect
      },
      immediate: true
    }
  },
  methods: {
    goLogin() {
      this.title = '登录'
    },
    showPwd() {
      if (this.passwordType === 'password') {
        this.passwordType = ''
      } else {
        this.passwordType = 'password'
      }
      this.$nextTick(() => {
        this.$refs.password.focus()
      })
    },
    handleLogin() {
      this.$refs.loginForm.validate(async(valid) => {
        if (valid) {
          this.loading = true

          const res = await this.$api.user.login.post(this.loginForm)
          if (res.code == 0) {
            this.$store
              .dispatch('user/login', res)
              .then(() => {
                this.$router.push({ path: '/' })
                this.loading = false
              })
              .catch(() => {
                this.loading = false
              })
          } else {
            this.$message({
              message: res.msg,
              type: 'error'
            })

            this.loading = false
          }
        } else {
          console.log('error submit!!')
          return false
        }
      })
    }
  }
}
</script>
<style lang="scss" scoped>
$bg: #fff;
$dark_gray: #889aa4;
$light_gray: black;
.el-button--primary {
  margin-bottom: 0px !important;
}
.argre {
  color: #409eff;
}
.login-container {
  .footer {
    display: flex;
    justify-content: center;
    p {
      span {
        cursor: pointer;
        color: #409eff;
      }
    }
  }
  .footer.first {
    display: flex;
    justify-content: space-between;
  }
  min-height: 100%;
  width: 100%;
  display: flex;
  align-items: center;
  background-color: $bg;
  overflow: hidden;
  .login {
    height: 600px;
    margin: 0 auto;
    display: flex;
    align-items: center;
  }
  .logo {
    margin-right: 100px;
    img {
      width: 400px;
      height: 400px;
    }
  }
  .form {
    position: relative;
    width: 500px;
    max-width: 100%;
    margin: 0 auto;
    overflow: hidden;
    padding: 100px 50px 100px 50px;
    border-radius: 50px;
    background: #fff;
    box-shadow: 20px 20px 60px #bebebe, -20px -20px 60px #ffffff;
    .captcha {
      margin: 0 0 10px 10px;
    }
    .title-container .title {
      text-align: center;
    }
  }
}
</style>
