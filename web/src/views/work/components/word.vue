<template>
  <el-dialog
    title="常见问题"
    :visible.sync="visible"
    destroy-on-close
    width="1200px"
    custom-class="menu-dialog-height"
    @closed="$emit('closed')"
  >
    <el-container style="height: calc(100vh - 300px)">
      <el-aside
        width="300px"
        style="background: #fff; border-right: 1px solid #e2e2e2"
      >
        <el-button
          type="primary"
          icon="el-icon-plus"
          size="small"
          style="margin-top: 20px; margin-left: 20px"
          @click="add"
        >添加分类</el-button>
        <el-button
          type="danger"
          icon="el-icon-delete"
          size="small"
          style="margin-top: 20px; margin-left: 20px"
          :disabled="canDel ? false : true"
          @click="delCate"
        >删除分类</el-button>
        <hr style="border: 1px solid #e2e2e2">
        <div class="cate">
          <div
            v-for="item in cate"
            :key="item.id"
            class="item"
            :class="item.id == nowCate ? 'active' : ''"
            @click="showDetail(item.id)"
          >
            <span>{{ item.cate_name }}</span>
          </div>
        </div>
      </el-aside>
      <el-main style="background: #fff">
        <el-alert
          title="点击常用语表格中的一行，则可选中使用"
          type="warning"
          show-icon
        />
        <div style="margin: 20px 5px 5px 5px">
          <div class="list-search">
            <el-form ref="form" :model="search" :inline="true">
              <el-form-item label="标题">
                <el-input v-model="search.title" />
              </el-form-item>
              <el-form-item>
                <el-button
                  type="primary"
                  size="small"
                  @click="onSearch"
                >查询</el-button>
                <el-button
                  type="primary"
                  size="small"
                  @click="addWord"
                >添加常用语</el-button>
              </el-form-item>
            </el-form>
          </div>

          <el-table
            :data="tableData"
            style="width: 100%"
            @row-click="handleSelectWord"
          >
            <el-table-column prop="id" label="ID" />
            <el-table-column prop="title" label="标题" />
            <el-table-column prop="create_time" label="创建时间" />
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
        </div>
      </el-main>

      <save-dialog
        v-if="dialog.save"
        ref="saveDialog"
        @success="handleSaveSuccess"
        @closed="dialog.save = false"
      />
    </el-container>
  </el-dialog>
</template>

<script>
import saveDialog from './save'

export default {
  components: {
    saveDialog
  },
  data() {
    return {
      visible: false,
      nowCate: 0,
      dialog: {
        save: false
      },
      canDel: false,
      cate: [],
      page: {
        total: 0,
        currentPage: 1,
        size: 15,
        pageSizes: [15, 25, 35, 45]
      },
      search: {
        title: '',
        cate_id: 0,
        page: 1,
        limit: 15
      },
      tableData: []
    }
  },
  mounted() {
    this.getCate()
  },
  methods: {
    open() {
      this.visible = true
      return this
    },
    add() {
      this.$prompt('请输入分类', '友情提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消'
      })
        .then(async({ value }) => {
          const res = await this.$api.cate.add.post({ cate_name: value })
          if (res.code === 0) {
            this.$message.success(res.msg)
            this.getCate()
          } else {
            this.$message.error(res.msg)
          }
        })
        .catch(() => {})
    },
    onSearch() {
      this.getList()
    },
    addWord() {
      this.dialog.save = true
      this.$nextTick(() => {
        this.$refs.saveDialog.open().setData({
          cate_id: this.search.cate_id
        })
      })
    },
    async getCate() {
      const res = await this.$api.cate.list.get()
      this.cate = res.data
    },
    showDetail(id) {
      this.nowCate = id
      this.canDel = true

      this.search.cate_id = id
      this.getList()
    },
    async getList() {
      const res = await this.$api.word.list.get(this.search)
      this.tableData = res.data.data
      this.page.total = res.data.total
    },
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
          const res = await this.$api.word.del.get({ id: row.id })
          if (res.code === 0) {
            this.$message.success(res.msg)
            this.getList()
          } else {
            this.$message.eror(res.msg)
          }
        })
        .catch(() => {})
    },
    async delCate() {
      const res = await this.$api.cate.del.get({ id: this.nowCate })
      if (res.code === 0) {
        this.$message.success(res.msg)
        this.getCate()
      } else {
        this.$message.error(res.msg)
      }
      this.canDel = false
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
    },
    handleSelectWord(row) {
      this.$emit('success', row)
    }
  }
}
</script>

<style scoped>
.item {
  height: 40px;
  line-height: 40px;
  padding-left: 20px;
  cursor: pointer;
}
.active {
  color: rgb(83, 168, 255);
  background: #f6f6f6;
}
.item span {
  padding-left: 5px;
}
.item:hover {
  color: rgb(83, 168, 255);
  background: #f6f6f6;
}
</style>

<style>
.list-search .el-input__inner {
  height: 33px;
}
</style>
