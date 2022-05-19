<template>
  <el-container>
    <el-container>
      <el-main>
        <el-card class="box-card">
          <div class="list-search">
            <el-form ref="form" :model="search" :inline="true">
              <el-form-item label="客服名称">
                <el-input v-model="search.name" />
              </el-form-item>
              <el-form-item>
                <el-button type="primary" @click="onSearch">查询</el-button>
              </el-form-item>
            </el-form>
            <el-button
              type="primary"
              icon="el-icon-plus"
              size="small"
              @click="add"
            >添加</el-button>
          </div>
        </el-card>
        <el-card class="box-card" style="margin-top: 10px">
          <el-table
            :data="tableData"
            style="width: 100%"
            :row-style="{ height: '50px' }"
          >
            <el-table-column label="ID" prop="id" width="50px" />
            <el-table-column label="标识" prop="code" />
            <el-table-column label="昵称" prop="name" />
            <el-table-column label="头像" prop="avatar">
              <template slot-scope="scope">
                <el-image
                  :src="scope.row.avatar"
                  style="width: 30px; height: 30px"
                />
              </template>
            </el-table-column>
            <el-table-column label="手机号" prop="phone" />
            <el-table-column label="状态" prop="status">
              <template #default="scope">
                <span v-if="scope.row.status == 1">
                  <el-tag size="medium" type="success">在线</el-tag>
                </span>
                <span
                  v-else
                ><el-tag size="medium" type="danger">离线</el-tag></span>
              </template>
            </el-table-column>
            <el-table-column label="上次登录时间" prop="last_login_time" />
            <el-table-column label="上次登录IP" prop="last_ip" />
            <el-table-column label="操作">
              <template #default="scope">
                <el-button
                  type="text"
                  size="small"
                  @click="table_edit(scope.row)"
                >编辑</el-button>
                <el-button
                  type="text"
                  size="small"
                  @click="table_del(scope.row)"
                >删除</el-button>
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

    <save-dialog
      v-if="dialog.save"
      ref="saveDialog"
      @success="handleSaveSuccess"
      @closed="dialog.save = false"
    />
  </el-container>
</template>

<script>
import saveDialog from './save'

export default {
  name: 'User',
  components: {
    saveDialog
  },
  data() {
    return {
      dialog: {
        save: false
      },
      page: {
        total: 0,
        currentPage: 1,
        size: 15,
        pageSizes: [15, 25, 35, 45]
      },
      search: {
        name: '',
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
    table_edit(row) {
      this.dialog.save = true
      this.$nextTick(() => {
        this.$refs.saveDialog.open('edit').setData(row)
      })
    },
    table_del(row) {
      this.$confirm('确认删除此客服?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      })
        .then(async() => {
          const res = await this.$api.account.del.get({ id: row.id })
          if (res.code === 0) {
            this.$message.success(res.msg)
            this.getList()
          } else {
            this.$message.eror(res.msg)
          }
        })
        .catch(() => {})
    },
    add() {
      this.dialog.save = true
      this.$nextTick(() => {
        this.$refs.saveDialog.open()
      })
    },
    onSearch() {
      this.getList()
    },
    async getList() {
      const res = await this.$api.account.list.get(this.search)
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
    },
    handleSaveSuccess() {
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
  float: right;
  justify-content: space-between;
}
</style>
