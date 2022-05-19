<template>
  <el-container>
    <el-container>
      <el-main>
        <el-card class="box-card">
          <div class="list-search">
            <el-form ref="form" :model="search" :inline="true">
              <el-form-item label="访客名称">
                <el-input v-model="search.customer_name" />
              </el-form-item>
              <el-form-item>
                <el-button type="primary" @click="onSearch">查询</el-button>
              </el-form-item>
            </el-form>
          </div>
        </el-card>
        <el-card class="box-card" style="margin-top: 10px">
          <el-table
            :data="tableData"
            style="width: 100%"
            :row-style="{ height: '50px' }"
          >
            <el-table-column label="访客ID" prop="customer_id" />
            <el-table-column label="访客名称" prop="customer_name" />
            <el-table-column label="头像" prop="customer_avatar">
              <template slot-scope="scope">
                <el-image
                  :src="scope.row.customer_avatar"
                  style="width: 30px; height: 30px"
                />
              </template>
            </el-table-column>
            <el-table-column label="上次登录IP" prop="customer_ip" />
            <el-table-column label="状态" prop="status" width="80px">
              <template #default="scope">
                <span v-if="scope.row.online_status == 1">
                  <el-tag size="medium" type="success">在线</el-tag>
                </span>
                <span
                  v-else
                ><el-tag size="medium" type="danger">离线</el-tag></span>
              </template>
            </el-table-column>
            <el-table-column label="首次咨询时间" prop="create_time" />
            <el-table-column label="所在省份" prop="province" />
            <el-table-column label="所在城市" prop="city" />
            <el-table-column label="操作">
              <template #default="scope">
                <el-button
                  type="text"
                  size="small"
                  @click="take_care(scope.row)"
                >接待记录</el-button>
                <el-button
                  type="text"
                  size="small"
                  @click="chat_log(scope.row)"
                >聊天记录</el-button>
              </template>
            </el-table-column>
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
        </el-card>
      </el-main>
    </el-container>

    <take-dialog
      v-if="dialog.save"
      ref="takeDialog"
      :customer_id="customer_id"
      @closed="dialog.save = false"
    />
    <log-dialog
      v-if="dialog.logShow"
      ref="logDialog"
      :customer_id="customer_id"
      @closed="dialog.logShow = false"
    />
  </el-container>
</template>

<script>
import takeDialog from './take'
import logDialog from './log'

export default {
  name: 'Customer',
  components: {
    takeDialog,
    logDialog
  },
  data() {
    return {
      dialog: {
        save: false,
        logShow: false
      },
      customer_id: '',
      page: {
        total: 0,
        currentPage: 1,
        size: 15,
        pageSizes: [15, 25, 35, 45]
      },
      search: {
        customer_name: '',
        page: 1,
        limit: 15
      },
      tableData: []
    }
  },
  mounted() {
    this.getList()
  },
  methods: {
    take_care(row) {
      this.customer_id = row.customer_id
      this.dialog.save = true
      this.$nextTick(() => {
        this.$refs.takeDialog.open()
      })
    },
    chat_log(row) {
      this.customer_id = row.customer_id
      this.dialog.logShow = true
      this.$nextTick(() => {
        this.$refs.logDialog.open()
      })
    },
    onSearch() {
      this.getList()
    },
    async getList() {
      const res = await this.$api.customer.list.get(this.search)
      this.page.total = res.data.total
      this.tableData = res.data.data
    },
    // 分页规格改变
    handleSizeChange(val) {
      this.page.size = val
      this.page.currentPage = 1
      this.search.page = 1
      this.search.limit = val
      this.getList()
    },
    // 分页点击
    handleCurrentChange(val) {
      this.page.currentPage = val
      this.search.page = val
      this.search.limit = this.page.size
      this.getList()
    }
  }
}
</script>

<style>
.box-card {
  border: none !important;
  box-shadow: none !important;
  width: 100%;
}
.list-title {
  display: inline-block;
  color: #17233d;
  font-weight: 500;
  font-size: 20px;
}
.el-form-item .el-form-item__label {
  font-weight: 400 !important;
  font-size: 12px !important;
}
.el-table td,
.el-table th {
  padding: 9px 0;
}
.page {
  margin-left: 20px;
  margin-top: 20px;
  display: flex;
  background: #fff;
  justify-content: space-between;
}
</style>
