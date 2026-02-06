import { ref, type Ref } from 'vue'
import apiClient from '@/plugins/axios'

const cartCount = ref<number>(0)

/**
 * カート件数（バッジ表示用）を管理する composable
 * ログイン時・カート追加後に fetchCount を呼ぶとヘッダーのバッジが更新される
 */
export function useCart() {
  const fetchCount = async (): Promise<number> => {
    try {
      const response = await apiClient.post('/api/cart/count')
      if (response.data.success && typeof response.data.count === 'number') {
        cartCount.value = response.data.count
        return cartCount.value
      }
    } catch {
      cartCount.value = 0
    }
    return 0
  }

  const clearCount = (): void => {
    cartCount.value = 0
  }

  return {
    cartCount: cartCount as Ref<number>,
    fetchCount,
    clearCount,
  }
}
