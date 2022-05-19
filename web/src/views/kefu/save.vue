<template>
  <el-dialog
    :title="title"
    :visible.sync="visible"
    destroy-on-close
    width="600px"
    custom-class="menu-dialog-height"
    @closed="$emit('closed')"
  >
    <el-form ref="dialogForm" :model="form" label-width="80px" :rules="rules">
      <el-form-item label="手机号" prop="phone">
        <el-input v-model="form.phone" />
      </el-form-item>
      <el-form-item label="昵称" prop="name">
        <el-input v-model="form.name" />
      </el-form-item>
      <el-form-item label="密码" prop="password">
        <el-input v-model="form.password" :placeholder="placeholder" />
      </el-form-item>
      <el-form-item label="头像" prop="avatar">
        <el-upload
          class="avatar-uploader"
          :action="apiUrl + '/Upload/uploadImg'"
          :show-file-list="false"
          :on-success="handleAvatarSuccess"
        >
          <img v-if="form.avatar" :src="form.avatar" class="avatar">
          <i v-else class="el-icon-plus avatar-uploader-icon" />
        </el-upload>
      </el-form-item>

      <el-form-item>
        <el-button @click="visible = false">取 消</el-button>
        <el-button
          type="primary"
          :loading="isSaveing"
          @click="submit()"
        >确 定</el-button>
      </el-form-item>
    </el-form>
  </el-dialog>
</template>

<script>
export default {
  emits: ['success', 'closed'],
  data() {
    return {
      apiUrl: process.env.VUE_APP_BASE_API,
      mode: 'add',
      title: '添加客服',
      form: {
        id: '',
        name: '',
        phone: '',
        avatar: '',
        password: ''
      },
      placeholder: '请输入密码',
      rules: {
        name: [{ required: true, message: '请输入昵称' }],
        phone: [{ required: true, message: '请输入手机号' }],
        avatar: [{ required: true, message: '请上传头像' }]
      },
      visible: false,
      isSaveing: false
    }
  },
  methods: {
    open(mode = 'add') {
      this.visible = true
      this.mode = mode
      if (this.mode === 'add') {
        this.title = '添加客服'

        this.rules.password = [{ required: true, message: '请输入密码' }]
      } else {
        this.title = '编辑客服'
        this.placeholder = '输入则更改密码'
      }
      return this
    },
    setData(data) {
      this.form.id = data.id
      this.form.name = data.name
      this.form.phone = data.phone
      this.form.avatar = String(data.avatar)
    },
    handleAvatarSuccess(res) {
      this.form.avatar = res.data.src
    },
    // 表单提交方法
    submit() {
      this.$refs.dialogForm.validate(async(valid) => {
        if (valid) {
          this.isSaveing = true
          let res
          if (this.mode === 'add') {
            res = await this.$api.account.add.post(this.form)
          } else {
            res = await this.$api.account.edit.post(this.form)
          }
          this.isSaveing = false
          if (res.code === 0) {
            this.$emit('success', this.form, this.mode)
            this.visible = false
            this.$message.success(res.msg)
          } else {
            this.$alert(res.msg, '提示', { type: 'error' })
          }
        }
      })
    }
  }
}
</script>

<style>
.avatar-uploader .el-upload {
  border: 1px dashed #c2c2c2;
  border-radius: 6px;
  cursor: pointer;
  position: relative;
  overflow: hidden;
}
.avatar-uploader .el-upload:hover {
  border-color: #409eff;
}
.avatar-uploader-icon {
  font-size: 28px;
  color: #8c939d;
  width: 100px;
  height: 100px;
  line-height: 100px;
  text-align: center;
}
.avatar {
  width: 100px;
  height: 100px;
  display: block;
}
</style>
