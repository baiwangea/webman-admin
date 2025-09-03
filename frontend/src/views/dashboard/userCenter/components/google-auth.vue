<template>
  <div class="google-auth-container">
    <a-card title="谷歌验证器设置" :bordered="false">
      <div v-if="!authInfo.is_bound">
        <a-alert type="info" class="mb-4">
          <template #icon><icon-info-circle /></template>
          <template #title>绑定谷歌验证器</template>
          为了提高账户安全性，建议您绑定谷歌验证器。绑定后，登录时需要输入谷歌验证码。
        </a-alert>
        
        <a-steps :current="currentStep" class="mb-6">
          <a-step title="扫描二维码" />
          <a-step title="输入验证码" />
          <a-step title="绑定完成" />
        </a-steps>
        
        <div v-if="currentStep === 1">
          <div class="text-center mb-4">
            <div class="qr-code-container">
              <img v-if="qrCodeUrl" :src="qrCodeUrl" alt="二维码" class="qr-code" />
              <a-spin v-else size="large" />
            </div>
          </div>
          <div class="text-center mb-4">
            <p class="text-gray-600">请使用谷歌验证器扫描上方二维码</p>
            <p class="text-sm text-gray-500">密钥：{{ secretKey }}</p>
          </div>
          <div class="text-center">
            <a-button type="primary" @click="nextStep">下一步</a-button>
            <a-button class="ml-2" @click="generateNewSecret">重新生成</a-button>
          </div>
        </div>
        
        <div v-if="currentStep === 2">
          <a-form :model="bindForm" @submit="handleBind">
            <a-form-item 
              field="code" 
              label="验证码" 
              :rules="[{ required: true, message: '请输入6位验证码' }]">
              <a-input 
                v-model="bindForm.code" 
                placeholder="请输入6位验证码" 
                maxlength="6" 
                allow-clear />
            </a-form-item>
            <a-form-item>
              <a-button type="primary" html-type="submit" :loading="binding">确认绑定</a-button>
              <a-button class="ml-2" @click="prevStep">上一步</a-button>
            </a-form-item>
          </a-form>
        </div>
        
        <div v-if="currentStep === 3">
          <a-result status="success" title="绑定成功！">
            <template #subtitle>
              您已成功绑定谷歌验证器，现在可以使用谷歌验证码登录了。页面将自动刷新显示绑定状态。
            </template>
          </a-result>
        </div>
      </div>
      
      <div v-else>
        <a-alert type="success" class="mb-4">
          <template #icon><icon-check-circle /></template>
          <template #title>已绑定谷歌验证器</template>
          您的账户已成功绑定谷歌验证器，登录时需要输入谷歌验证码。
        </a-alert>
        
        <a-descriptions :column="1" bordered>
          <a-descriptions-item label="绑定状态">
            <a-tag color="green">已绑定</a-tag>
          </a-descriptions-item>
          <a-descriptions-item label="绑定时间">
            {{ authInfo.bind_time || '未知' }}
          </a-descriptions-item>
        </a-descriptions>
        
        <a-alert type="warning" class="mt-4 mb-4">
          <template #icon><icon-exclamation-circle /></template>
          <template #title>重新绑定提示</template>
          您已经绑定了谷歌验证器，如需重新绑定请先解绑当前的谷歌验证器。
        </a-alert>
        
        <div class="mt-4">
          <a-button type="outline" status="danger" @click="showUnbindModal">解绑谷歌验证器</a-button>
        </div>
      </div>
    </a-card>
    
    <!-- 解绑确认弹窗 -->
    <a-modal 
      v-model:visible="unbindModalVisible" 
      title="解绑谷歌验证器" 
      @ok="handleUnbind"
      :ok-loading="unbinding">
      <a-form :model="unbindForm">
        <a-alert type="warning" class="mb-4">
          解绑后，登录将不再需要谷歌验证码，请确认您的操作。
        </a-alert>
        <a-form-item 
          field="code" 
          label="验证码" 
          :rules="[{ required: true, message: '请输入6位验证码' }]">
          <a-input 
            v-model="unbindForm.code" 
            placeholder="请输入当前谷歌验证码" 
            maxlength="6" 
            allow-clear />
        </a-form-item>
      </a-form>
    </a-modal>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { Message } from '@arco-design/web-vue'
import googleAuthApi from '@/api/googleAuth'

const currentStep = ref(1)
const qrCodeUrl = ref('')
const secretKey = ref('')
const binding = ref(false)
const unbinding = ref(false)
const unbindModalVisible = ref(false)

const authInfo = reactive({
  is_bound: false,
  bind_time: ''
})

const bindForm = reactive({
  code: '',
  secret: ''
})

const unbindForm = reactive({
  code: ''
})

// 获取绑定信息
const getBindInfo = async () => {
  try {
    const res = await googleAuthApi.getBindInfo()
    if (res.code === 200) {
      Object.assign(authInfo, res.data)
    }
  } catch (error) {
    console.error('获取绑定信息失败:', error)
  }
}

// 生成新的密钥
const generateNewSecret = async () => {
  try {
    const res = await googleAuthApi.generateSecret()
    if (res.code === 200) {
      secretKey.value = res.data.secret
      qrCodeUrl.value = res.data.qr_code_url
      bindForm.secret = res.data.secret
      // 重新生成时回到第一步
      currentStep.value = 1
      bindForm.code = ''
    } else {
      Message.error(res.message || '生成密钥失败')
      // 如果已绑定，刷新绑定信息
      if (res.message && res.message.includes('已经绑定')) {
        await getBindInfo()
      }
    }
  } catch (error) {
    Message.error('生成密钥失败')
  }
}

// 下一步
const nextStep = () => {
  if (currentStep.value < 2) {
    currentStep.value++
  }
}

// 上一步
const prevStep = () => {
  if (currentStep.value > 1) {
    currentStep.value--
  }
}

// 绑定谷歌验证器
const handleBind = async () => {
  if (!bindForm.code) {
    Message.error('请输入验证码')
    return
  }
  
  binding.value = true
  try {
    const res = await googleAuthApi.bind({
      code: bindForm.code,
      secret: bindForm.secret
    })
    if (res.code === 200) {
      Message.success('绑定成功')
      currentStep.value = 3
      await getBindInfo()
      // 绑定成功后清空表单
      bindForm.code = ''
    } else {
      Message.error(res.message || '绑定失败')
    }
  } catch (error) {
    Message.error('绑定失败')
  } finally {
    binding.value = false
  }
}

// 显示解绑弹窗
const showUnbindModal = () => {
  unbindModalVisible.value = true
  unbindForm.code = ''
}

// 解绑谷歌验证器
const handleUnbind = async () => {
  if (!unbindForm.code) {
    Message.error('请输入验证码')
    return
  }
  
  unbinding.value = true
  try {
    const res = await googleAuthApi.unbind({
      code: unbindForm.code
    })
    if (res.code === 200) {
      Message.success('解绑成功')
      unbindModalVisible.value = false
      currentStep.value = 1
      qrCodeUrl.value = ''
      secretKey.value = ''
      bindForm.code = ''
      bindForm.secret = ''
      await getBindInfo()
      // 解绑后自动生成新的密钥供重新绑定
      if (!authInfo.is_bound) {
        await generateNewSecret()
      }
    } else {
      Message.error(res.message || '解绑失败')
    }
  } catch (error) {
    Message.error('解绑失败')
  } finally {
    unbinding.value = false
  }
}

// 重置绑定流程
const resetBindFlow = async () => {
  currentStep.value = 1
  qrCodeUrl.value = ''
  secretKey.value = ''
  bindForm.code = ''
  bindForm.secret = ''
  // 重置后生成新的密钥
  await generateNewSecret()
}

// 页面加载时获取信息
onMounted(async () => {
  await getBindInfo()
  // 只有在未绑定状态下才生成新密钥
  if (!authInfo.is_bound) {
    await generateNewSecret()
  }
})
</script>

<style scoped>
.google-auth-container {
  max-width: 600px;
}

.qr-code-container {
  display: inline-block;
  padding: 20px;
  background: #f8f9fa;
  border-radius: 8px;
  border: 1px solid #e9ecef;
}

.qr-code {
  width: 200px;
  height: 200px;
  display: block;
}
</style>