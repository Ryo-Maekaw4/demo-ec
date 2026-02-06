/**
 * axios.ts
 * 
 * Axiosの設定とインターセプター
 * すべてのAPIリクエストで認証エラー（401）を検知して自動処理
 */

import axios from 'axios'
import { useAuth } from '@/composables/useAuth'

// Axiosインスタンスを作成
const apiClient = axios.create({
  baseURL: '/fuel',
  // JWTトークンベースなので、withCredentialsは不要
  headers: {
    'Content-Type': 'application/json',
  },
})

// リクエストインターセプター（すべてのリクエスト前に実行）
apiClient.interceptors.request.use(
  (config) => {
    // localStorageからJWTトークンを取得してAuthorizationヘッダーに追加
    const token = localStorage.getItem('auth_token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// レスポンスインターセプター（すべてのレスポンス後に実行）
apiClient.interceptors.response.use(
  (response) => {
    // 正常なレスポンスはそのまま返す
    return response
  },
  async (error) => {
    // 401 Unauthorized エラーを検知
    if (error.response?.status === 401) {
      // ログイン状態をクリア
      const { clearAuthState } = useAuth()
      clearAuthState()
      
      // ログインページにリダイレクト（現在のページがログインページでない場合）
      if (window.location.pathname !== '/login') {
        window.location.href = '/login'
      }
    }
    
    return Promise.reject(error)
  }
)

export default apiClient
