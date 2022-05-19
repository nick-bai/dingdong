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
      <el-form-item label="标题" prop="title">
        <el-input v-model="form.title" />
      </el-form-item>
      <el-form-item label="内容" prop="word">
        <el-input v-model="form.word" :rows="6" type="textarea" />
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
      mode: 'add',
      title: '添加常用语',
      form: {
        id: '',
        title: '',
        cate_id: 0,
        word: ''
      },
      rules: {
        title: [{ required: true, message: '请输入标题' }],
        word: [{ required: true, message: '请输入内容' }]
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
        this.title = '添加常用语'
      } else {
        this.title = '编辑常用语'
      }
      return this
    },
    setData(data) {
      this.form.id = data.id
      this.form.title = data.title
      this.form.cate_id = data.cate_id
      this.form.word = data.word
    },
    // 表单提交方法
    submit() {
      this.$refs.dialogForm.validate(async(valid) => {
        if (valid) {
          this.isSaveing = true
          let res
          if (this.mode === 'add') {
            res = await this.$api.word.add.post(this.form)
          } else {
            res = await this.$api.word.edit.post(this.form)
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
