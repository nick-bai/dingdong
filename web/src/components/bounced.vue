<template>
  <el-dialog
      :close-on-click-modal="false"
      append-to-body
      id="bouncedBox"
      :title="title"
      :visible.sync="show"
      :width="width + 'px'"
      @close="close"
  >
    <slot v-if="show" />
  </el-dialog>
</template>
<script>
export default {
  props: {
    title: {
      type: String,
      default: ''
    },
    dialogShow: {
      type: Boolean,
      default: false
    },
    width: {
      type: Number,
      default: 300
    }
  },
  data() {
    return {
      show: false
    }
  },
  watch: {
    dialogShow: function(newQuestion, oldQuestion) {
      this.show = newQuestion
    }
  },
  methods: {
    close() {
      this.$emit('cloneCheck', this.show)
      this.$emit('update:dialogShow', this.show)
    }
  }
}
</script>
<style lang="scss">
#bouncedBox {
  .el-dialog {
    border-radius: 10px;
  }
  .el-dialog__body {
    padding: 20px 20px;
  }
  .el-dialog__header {
    /*background: #2b5fd6 !important;*/
    border-radius: 10px 10px 0 0 !important;
    /*padding: 15px 20px 15px;*/
    border-bottom: 1px solid #ebeef5;
  }

  .el-dialog__title {
    /*color: #fff;*/
    font-size: 16px;
    font-weight: bold;
  }

  .el-dialog__close {
    color: #303133;
  }
}
</style>
