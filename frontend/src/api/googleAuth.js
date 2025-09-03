import { request } from '@/utils/request'

const api = {
  /**
   * 获取谷歌验证码绑定信息
   * @returns {Promise}
   */
  getBindInfo() {
    return request({
      url: '/tool/googleAuth/getBindInfo',
      method: 'get'
    })
  },

  /**
   * 生成新的密钥和二维码
   * @returns {Promise}
   */
  generateSecret() {
    return request({
      url: '/tool/googleAuth/generateSecret',
      method: 'get'
    })
  },

  /**
   * 绑定谷歌验证器
   * @param {Object} data - 绑定数据
   * @param {string} data.code - 验证码
   * @param {string} data.secret - 密钥
   * @returns {Promise}
   */
  bind(data) {
    return request({
      url: '/tool/googleAuth/bind',
      method: 'post',
      data
    })
  },

  /**
   * 解绑谷歌验证器
   * @param {Object} data - 解绑数据
   * @param {string} data.code - 验证码
   * @returns {Promise}
   */
  unbind(data) {
    return request({
      url: '/tool/googleAuth/unbind',
      method: 'post',
      data
    })
  },

  /**
   * 验证谷歌验证码
   * @param {Object} data - 验证数据
   * @param {string} data.code - 验证码
   * @returns {Promise}
   */
  verify(data) {
    return request({
      url: '/tool/googleAuth/verify',
      method: 'post',
      data
    })
  }
}

export default api