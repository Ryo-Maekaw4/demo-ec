/**
 * router/index.ts
 *
 * Automatic routes for `./src/pages/*.vue`
 */

// Composables
import { createRouter, createWebHistory } from 'vue-router'
import { routes } from 'vue-router/auto-routes'
import { useAuth } from '@/composables/useAuth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

// 保護されたルート（ログインが必要なページ）
const protectedRoutes = ['/mypage']

// ルーターガード：画面遷移時にログイン状態をチェック
router.beforeEach(async (to, from, next) => {
  const { isLoggedIn, checkAuthStatusIfNeeded, initAuth } = useAuth()
  
  // 初回のみ初期化（localStorageから状態を復元）
  // initAuth()は一度だけ実行される（内部でフラグで制御）
  initAuth()
  
  console.log('[Router Guard]', {
    to: to.path,
    from: from.path,
    isLoggedIn: isLoggedIn.value,
    hasToken: !!localStorage.getItem('auth_token'),
    hasAuth: !!localStorage.getItem('auth')
  })
  
  // ログインページにアクセスしようとした場合
  if (to.path === '/login') {
    console.log('[Router Guard] /login への遷移を検知')
    
    // まず、トークンがあるかチェック（高速チェック）
    const hasToken = !!localStorage.getItem('auth_token')
    const hasAuth = !!localStorage.getItem('auth')
    
    // トークンがある場合は、初期化された状態を確認
    if (hasToken && hasAuth) {
      // 既に初期化されている場合は、現在のログイン状態を確認
      if (isLoggedIn.value) {
        console.log('[Router Guard] トークンあり、ログイン済みのため /mypage にリダイレクト')
        next('/mypage')
        return
      }
      
      // トークンはあるが、状態が初期化されていない場合はAPIを呼び出して確認
      console.log('[Router Guard] トークンあり、状態を確認中...')
      const authenticated = await checkAuthStatusIfNeeded(false)
      console.log('[Router Guard] 認証状態:', authenticated)
      
      if (authenticated) {
        console.log('[Router Guard] ログイン済みのため /mypage にリダイレクト')
        next('/mypage')
        return
      }
    } else {
      // トークンがない場合は、APIを呼び出して確認（念のため）
      console.log('[Router Guard] トークンなし、APIで確認')
      const authenticated = await checkAuthStatusIfNeeded(false)
      console.log('[Router Guard] 認証状態:', authenticated)
      
      if (authenticated) {
        console.log('[Router Guard] ログイン済みのため /mypage にリダイレクト')
        next('/mypage')
        return
      }
    }
    
    console.log('[Router Guard] 未ログインのため /login を表示')
    // ログインしていない場合はログインページを表示
    next()
    return
  }
  
  // 保護されたルートかどうかをチェック
  const isProtectedRoute = protectedRoutes.some(route => to.path.startsWith(route))
  
  if (isProtectedRoute) {
    // 保護されたルートの場合、ログイン状態を確認
    // 一定時間経過している場合はAPIを呼び出して整合性をチェック
    const authenticated = await checkAuthStatusIfNeeded(false)
    
    if (!authenticated) {
      // ログインしていない場合はログインページにリダイレクト
      next('/login')
      return
    }
  } else {
    // 保護されていないルートでも、定期的にログイン状態をチェック（バックグラウンド）
    // ただし、ブロックしない（非同期で実行）
    checkAuthStatusIfNeeded(false).catch(() => {
      // エラーは無視（バックグラウンドチェックなので）
    })
  }
  
  next()
})

// Workaround for https://github.com/vitejs/vite/issues/11804
router.onError((err, to) => {
  if (err?.message?.includes?.('Failed to fetch dynamically imported module')) {
    if (localStorage.getItem('vuetify:dynamic-reload')) {
      console.error('Dynamic import error, reloading page did not fix it', err)
    } else {
      console.log('Reloading page to fix dynamic import error')
      localStorage.setItem('vuetify:dynamic-reload', 'true')
      location.assign(to.fullPath)
    }
  } else {
    console.error(err)
  }
})

router.isReady().then(() => {
  localStorage.removeItem('vuetify:dynamic-reload')
})

export default router
