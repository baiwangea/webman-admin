<template>
  <component
    is="a-modal"
    :width="tool.getDevice() === 'mobile' ? '100%' : '600px'"
    v-model:visible="visible"
    :title="title"
    :mask-closable="false"
    :ok-loading="loading"
    @cancel="close"
    @before-ok="submit">
    <!-- 表单信息 start -->
    <a-form ref="formRef" :model="formData" :rules="rules" :auto-label-width="true">
      <a-form-item label="用户名" field="user_name">
        <a-input v-model="formData.user_name" placeholder="请输入用户名" />
      </a-form-item>
      <a-form-item label="上级地址" field="parent_addr">
        <a-input v-model="formData.parent_addr" placeholder="请输入上级地址" />
      </a-form-item>
      <a-form-item label="邀请关系树" field="tree">
        <a-input v-model="formData.tree" placeholder="请输入邀请关系树" />
      </a-form-item>
      <a-form-item label="登录ip" field="login_ip">
        <a-input v-model="formData.login_ip" placeholder="请输入登录ip" />
      </a-form-item>
      <a-form-item label="1:正常，2:禁用，99:删除" field="status">
        <a-input v-model="formData.status" placeholder="请输入1:正常，2:禁用，99:删除" />
      </a-form-item>
      <a-form-item label="上级ID" field="parent">
        <a-input v-model="formData.parent" placeholder="请输入上级ID" />
      </a-form-item>
      <a-form-item label="是否为合伙人" field="is_partner">
        <a-input v-model="formData.is_partner" placeholder="请输入是否为合伙人" />
      </a-form-item>
      <a-form-item label="是否为零线用户" field="is_zline">
        <a-input v-model="formData.is_zline" placeholder="请输入是否为零线用户" />
      </a-form-item>
      <a-form-item label="是否为社区用户" field="is_community">
        <a-input v-model="formData.is_community" placeholder="请输入是否为社区用户" />
      </a-form-item>
      <a-form-item label="最后登录时间" field="last_login">
        <a-input v-model="formData.last_login" placeholder="请输入最后登录时间" />
      </a-form-item>
    </a-form>
    <!-- 表单信息 end -->
  </component>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import tool from '@/utils/tool'
import { Message, Modal } from '@arco-design/web-vue'
import api from '../api/users'

const emit = defineEmits(['success'])
// 引用定义
const visible = ref(false)
const loading = ref(false)
const formRef = ref()
const mode = ref('')

let title = computed(() => {
  return '用户信息表' + (mode.value == 'add' ? '-新增' : '-编辑')
})

// 表单初始值
const initialFormData = {
  id: null,
  user_name: '',
  parent_addr: '',
  tree: '',
  login_ip: '0',
  status: 1,
  parent: null,
  is_partner: null,
  is_zline: null,
  is_community: null,
  last_login: null,
}

// 表单信息
const formData = reactive({ ...initialFormData })

// 验证规则
const rules = {
  user_name: [{ required: true, message: '用户名必需填写' }],
  invite_code: [{ required: true, message: '邀请码必需填写' }],
  user_addr: [{ required: true, message: '用户地址必需填写' }],
  parent_addr: [{ required: true, message: '上级地址必需填写' }],
  tree: [{ required: true, message: '邀请关系树必需填写' }],
  login_ip: [{ required: true, message: '登录ip必需填写' }],
  status: [{ required: true, message: '1:正常，2:禁用，99:删除必需填写' }],
  parent: [{ required: true, message: '上级ID必需填写' }],
  is_partner: [{ required: true, message: '是否为合伙人必需填写' }],
  is_zline: [{ required: true, message: '是否为零线用户必需填写' }],
  is_community: [{ required: true, message: '是否为社区用户必需填写' }],
  last_login: [{ required: true, message: '最后登录时间必需填写' }],
  create_time: [{ required: true, message: '创建时间必需填写' }],
  update_time: [{ required: true, message: '更新时间必需填写' }],
}

// 打开弹框
const open = async (type = 'add') => {
  mode.value = type
  // 重置表单数据
  Object.assign(formData, initialFormData)
  formRef.value.clearValidate()
  visible.value = true
  await initPage()
}

// 初始化页面数据
const initPage = async () => {}

// 设置数据
const setFormData = async (data) => {
  for (const key in formData) {
    if (data[key] != null && data[key] != undefined) {
      formData[key] = data[key]
    }
  }
}

// 数据保存
const submit = async (done) => {
  const validate = await formRef.value?.validate()
  if (!validate) {
    loading.value = true
    let data = { ...formData }
    let result = {}
    if (mode.value === 'add') {
      // 添加数据
      data.id = undefined
      result = await api.save(data)
    } else {
      // 修改数据
      result = await api.update(data.id, data)
    }
    if (result.code === 200) {
      Message.success('操作成功')
      emit('success')
      done(true)
    }
    // 防止连续点击提交
    setTimeout(() => {
      loading.value = false
    }, 500)
  }
  done(false)
}

// 关闭弹窗
const close = () => (visible.value = false)

defineExpose({ open, setFormData })
</script>
