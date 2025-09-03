<template>
  <div class="ma-content-block">
    <sa-table ref="crudRef" :options="options" :columns="columns" :searchForm="searchForm">
      <!-- 搜索区 tableSearch -->
      <template #tableSearch>
        <a-col :sm="8" :xs="24">
          <a-form-item label="用户名" field="user_name">
            <a-input v-model="searchForm.user_name" placeholder="请输入用户名" allow-clear />
          </a-form-item>
        </a-col>
      </template>

      <!-- Table 自定义渲染 -->
    </sa-table>

    <!-- 编辑表单 -->
    <edit-form ref="editRef" @success="refresh" />

  </div>
</template>

<script setup>
import { onMounted, ref, reactive } from 'vue'
import { Message } from '@arco-design/web-vue'
import EditForm from './edit.vue'
import api from '../api/users'

// 引用定义
const crudRef = ref()
const editRef = ref()
const viewRef = ref()

// 搜索表单
const searchForm = ref({
  user_name: '',
})

// SaTable 基础配置
const options = reactive({
  api: api.getPageList,
  rowSelection: { showCheckedAll: true },
  add: {
    show: true,
    auth: ['/app/bank/Users/save'],
    func: async () => {
      editRef.value?.open()
    },
  },
  edit: {
    show: true,
    auth: ['/app/bank/Users/update'],
    func: async (record) => {
      editRef.value?.open('edit')
      editRef.value?.setFormData(record)
    },
  },
  delete: {
    show: true,
    auth: ['/app/bank/Users/destroy'],
    func: async (params) => {
      const resp = await api.destroy(params)
      if (resp.code === 200) {
        Message.success(`删除成功！`)
        crudRef.value?.refresh()
      }
    },
  },
})

// SaTable 列配置
const columns = reactive([
  { title: '用户名', dataIndex: 'user_name', width: 180 },
  { title: '上级地址', dataIndex: 'parent_addr', width: 180 },
  { title: '邀请关系树', dataIndex: 'tree', width: 180 },
  { title: '登录ip', dataIndex: 'login_ip', width: 180 },
  { title: '1:正常，2:禁用，99:删除', dataIndex: 'status', width: 180 },
  { title: '上级ID', dataIndex: 'parent', width: 180 },
  { title: '是否为合伙人', dataIndex: 'is_partner', width: 180 },
  { title: '是否为零线用户', dataIndex: 'is_zline', width: 180 },
  { title: '是否为社区用户', dataIndex: 'is_community', width: 180 },
  { title: '最后登录时间', dataIndex: 'last_login', width: 180 },
])

// 页面数据初始化
const initPage = async () => {}

// SaTable 数据请求
const refresh = async () => {
  crudRef.value?.refresh()
}

// 页面加载完成执行
onMounted(async () => {
  initPage()
  refresh()
})
</script>
