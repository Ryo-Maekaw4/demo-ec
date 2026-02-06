import { ref, computed, onMounted } from 'vue'
import apiClient from '@/plugins/axios'

const isLoggedIn = ref<boolean>(false)
const user = ref<any>(null)
const isLoading = ref<boolean>(false)
let isInitialized = false // 初期化フラグ

/**
 * ログイン状態を管理するcomposable
 * localStorageとAPIを組み合わせて、毎回APIを呼ばないようにする
 */
export function useAuth() {
  // 初期化時にlocalStorageからログイン状態を復元（一度だけ実行）
  const initAuth = () => {
    // 既に初期化済みの場合はスキップ
    if (isInitialized) {
      return
    }
    
    isInitialized = true
    
    // JWTトークンがあるかチェック
    const token = localStorage.getItem('auth_token')
    const savedAuth = localStorage.getItem('auth')
    
    console.log('[useAuth] initAuth', {
      hasToken: !!token,
      hasAuth: !!savedAuth
    })
    
    if (token && savedAuth) {
      try {
        const authData = JSON.parse(savedAuth)
        isLoggedIn.value = authData.isLoggedIn || false
        user.value = authData.user || null
        console.log('[useAuth] ログイン状態を復元:', {
          isLoggedIn: isLoggedIn.value,
          user: user.value
        })
      } catch (e) {
        console.error('[useAuth] パースエラー:', e)
        // パースエラーの場合はクリア
        localStorage.removeItem('auth')
        localStorage.removeItem('auth_token')
        isLoggedIn.value = false
        user.value = null
      }
    } else {
      console.log('[useAuth] トークンまたはauthがないため、未ログイン状態に設定')
      // トークンがない場合は、未ログイン状態に設定（クリアはしない）
      isLoggedIn.value = false
      user.value = null
    }
  }

  // ログイン状態を確認（APIを呼び出して整合性をチェック）
  const checkAuthStatus = async (force: boolean = false): Promise<boolean> => {
    // 既にログイン状態が確認中の場合はスキップ（force=trueの場合は強制実行）
    if (isLoading.value && !force) return isLoggedIn.value
    
    isLoading.value = true
    
    try {
      const response = await apiClient.post('/api/login/status')
      if (response.data.success && response.data.isLoggedIn && response.data.user) {
        isLoggedIn.value = true
        user.value = response.data.user
        saveAuthState()
        return true
      } else {
        isLoggedIn.value = false
        user.value = null
        clearAuthState()
        return false
      }
    } catch (error) {
      // APIエラーの場合はlocalStorageの状態を信頼
      // ただし、明示的にログアウトされている場合はクリア
      const savedAuth = localStorage.getItem('auth')
      if (!savedAuth) {
        isLoggedIn.value = false
        user.value = null
        return false
      }
      // localStorageに保存されている場合は、その状態を返す
      return isLoggedIn.value
    } finally {
      isLoading.value = false
    }
  }
  
  // 最後にチェックした時刻を記録
  let lastCheckTime = 0
  const CHECK_INTERVAL = 5 * 60 * 1000 // 5分間隔でチェック
  
  // 必要に応じてログイン状態を確認（一定時間経過後、または強制チェック）
  const checkAuthStatusIfNeeded = async (force: boolean = false) => {
    const now = Date.now()
    const timeSinceLastCheck = now - lastCheckTime
    
    console.log('[useAuth] checkAuthStatusIfNeeded', {
      force,
      timeSinceLastCheck,
      CHECK_INTERVAL,
      shouldCheck: force || timeSinceLastCheck > CHECK_INTERVAL,
      currentState: isLoggedIn.value
    })
    
    // 強制チェック、または最後のチェックから一定時間経過している場合
    if (force || timeSinceLastCheck > CHECK_INTERVAL) {
      console.log('[useAuth] APIを呼び出してログイン状態を確認')
      const result = await checkAuthStatus(force)
      lastCheckTime = now
      console.log('[useAuth] API結果:', result)
      return result
    }
    // チェック不要な場合は現在の状態を返す
    console.log('[useAuth] APIをスキップ、現在の状態を返す:', isLoggedIn.value)
    return isLoggedIn.value
  }

  // ログイン状態をlocalStorageに保存
  const saveAuthState = () => {
    localStorage.setItem('auth', JSON.stringify({
      isLoggedIn: isLoggedIn.value,
      user: user.value,
      timestamp: Date.now()
    }))
  }

  // ログイン状態をクリア
  const clearAuthState = () => {
    localStorage.removeItem('auth')
    localStorage.removeItem('auth_token') // JWTトークンも削除
    isLoggedIn.value = false
    user.value = null
  }

  // ログイン処理
  const login = async (email: string, password: string, rememberMe: boolean = false) => {
    try {
      console.log('[useAuth] ログイン開始')
      const response = await apiClient.post('/api/login/login', {
        email,
        password,
        remember_me: rememberMe
      })
      
      console.log('[useAuth] ログインAPIレスポンス:', {
        success: response.data.success,
        hasToken: !!response.data.token,
        hasUser: !!response.data.user
      })
      
      if (response.data.success) {
        // JWTトークンをlocalStorageに保存
        if (response.data.token) {
          localStorage.setItem('auth_token', response.data.token)
          console.log('[useAuth] トークンを保存しました:', {
            tokenLength: response.data.token.length,
            tokenPreview: response.data.token.substring(0, 20) + '...'
          })
        } else {
          console.warn('[useAuth] 警告: レスポンスにトークンが含まれていません')
          console.warn('[useAuth] レスポンス全体:', response.data)
        }
        
        isLoggedIn.value = true
        user.value = response.data.user || null
        saveAuthState()
        
        // 保存後の確認
        const savedToken = localStorage.getItem('auth_token')
        const savedAuth = localStorage.getItem('auth')
        console.log('[useAuth] ログイン成功:', {
          isLoggedIn: isLoggedIn.value,
          user: user.value,
          tokenSaved: !!savedToken,
          authSaved: !!savedAuth,
          tokenMatches: savedToken === response.data.token
        })
        
        return { success: true, user: user.value }
      } else {
        console.log('[useAuth] ログイン失敗:', response.data.message)
        return { success: false, message: response.data.message || 'ログインに失敗しました' }
      }
    } catch (error: any) {
      console.error('[useAuth] ログインエラー:', error)
      return {
        success: false,
        message: error.response?.data?.message || 'ログインに失敗しました。しばらくしてから再度お試しください。'
      }
    }
  }

  // ログアウト処理
  const logout = async () => {
    try {
      await apiClient.post('/api/login/logout')
    } catch (error) {
      // エラーが発生してもログアウト状態にする
      console.error('Logout error:', error)
    } finally {
      clearAuthState()
    }
  }

  // パスワード変更（ログイン中ユーザー。API側でJWTからユーザーを特定）
  const changePassword = async (newPassword: string) => {
    try {
      const response = await apiClient.post('/api/login/change_password', {
        new_password: newPassword,
      })
      if (response.data.success) {
        return { success: true, message: (response.data as { message?: string }).message }
      }
      return {
        success: false,
        message: (response.data as { message?: string }).message || 'パスワードの変更に失敗しました'
      }
    } catch (error: any) {
      const message = error.response?.data?.message || 'パスワードの変更に失敗しました'
      return { success: false, message }
    }
  }

  // ユーザー情報更新（名前・メールアドレス）
  const updateProfile = async (params: { name?: string; email?: string }) => {
    try {
      const response = await apiClient.post('/api/login/update', params)
      if (response.data.success && response.data.user) {
        user.value = response.data.user
        saveAuthState()
        return { success: true, user: response.data.user }
      }
      return {
        success: false,
        message: (response.data as { message?: string }).message || '更新に失敗しました'
      }
    } catch (error: any) {
      const message = error.response?.data?.message || '更新に失敗しました'
      return { success: false, message }
    }
  }

  return {
    isLoggedIn: computed(() => isLoggedIn.value),
    user: computed(() => user.value),
    isLoading: computed(() => isLoading.value),
    initAuth,
    checkAuthStatus,
    checkAuthStatusIfNeeded,
    login,
    logout,
    updateProfile,
    changePassword,
    clearAuthState
  }
}
