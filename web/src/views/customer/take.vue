<template>
  <el-dialog
    :title="title"
    :visible.sync="visible"
    destroy-on-close
    width="1200px"
    custom-class="menu-dialog-height"
    @closed="$emit('closed')"
  >
    <el-table
      :data="tableData"
      style="width: 100%"
      :row-style="{ height: '50px' }"
    >
      <el-table-column label="访客ID" prop="customer_id" />
      <el-table-column label="访客名称" prop="customer_name" />
      <el-table-column label="头像" prop="customer_avatar" width="80px">
        <template slot-scope="scope">
          <el-image
            :src="scope.row.customer_avatar"
            style="width: 30px; height: 30px"
          />
        </template>
      </el-table-column>
      <el-table-column label="登录IP" prop="customer_ip" />
      <el-table-column label="接待客服" prop="kefu_name" />
      <el-table-column label="开始时间" prop="start_time" />
      <el-table-column label="结束时间" prop="end_time" />
      <el-table-column label="咨询时长" prop="chat_time" />
    </el-table>
    <div class="page">
      <el-pagination
        :hide-on-single-page="page.total < page.size ? true : false"
        background
        :current-page="page.currentPage"
        :page-sizes="page.pageSizes"
        :page-size="page.size"
        layout="total, sizes, prev, pager, next, jumper"
        :total="page.total"
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
      />
    </div>
  </el-dialog>
</template>

<script>
export default {
  emits: ['success', 'closed'],
  props: ['customer_id'],
  data() {
    return {
      title: '接待记录',
      visible: false,
      isSaveing: false,
      page: {
        total: 0,
        currentPage: 1,
        size: 10,
        pageSizes: [10, 20, 30, 40]
      },
      search: {
        page: 1,
        limit: 10
      },
      tableData: []
    }
  },
  created() {
    this.getServiceLog()
  },
  methods: {
    open() {
      this.visible = true
      return this
    },
    async getServiceLog() {
      this.search.customer_id = this.customer_id
      const res = await this.$api.customer.serviceLog.get(this.search)

      this.tableData = res.data.data
      this.page.total = res.data.total
    },
    // 分页规格改变
    handleSizeChange(val) {
      this.page.size = val
      this.page.currentPage = 1
      this.search.page = 1
      this.search.limit = val
      this.getServiceLog()
    },
    // 分页点击
    handleCurrentChange(val) {
      this.page.currentPage = val
      this.search.page = val
      this.search.limit = this.page.size
      this.getServiceLog()
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
