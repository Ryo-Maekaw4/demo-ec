<!--
  cart.vue … カート画面（ログインユーザーのカート内商品一覧）
-->
<template>
  <div class="ma-2">
    <Header />
    <div class="ma-4">
      <h1 class="text-h5 font-weight-bold mb-4">カート</h1>

      <div v-if="loading" class="d-flex justify-center align-center pa-12">
        <v-progress-circular indeterminate color="primary" size="64" />
      </div>

      <div v-else-if="items.length === 0" class="text-center pa-12">
        <v-icon size="64" color="grey">mdi-cart-outline</v-icon>
        <p class="text-h6 mt-4">カートに商品がありません</p>
        <p class="text-body-2 text-grey mb-4">商品一覧からお気に入りの商品をカートに追加してください</p>
        <v-btn color="primary" :to="'/search_list'">商品一覧へ</v-btn>
      </div>

      <template v-else>
        <v-table class="cart-table mb-4">
          <thead>
            <tr>
              <th class="text-left">商品</th>
              <th class="text-right">単価（税込）</th>
              <th class="text-center">数量</th>
              <th class="text-right">小計</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in items" :key="item.cart_id">
              <td>
                <div class="d-flex align-center">
                  <v-img
                    :src="item.image_url || 'https://via.placeholder.com/80x80?text=No+Image'"
                    width="80"
                    height="80"
                    cover
                    class="rounded mr-3"
                  />
                  <div>
                    <router-link :to="`/product/${item.product_id}`" class="text-body-1 font-weight-medium text-primary text-decoration-none">
                      {{ item.product_name }}
                    </router-link>
                    <p class="text-caption text-grey mb-0">商品コード: {{ item.product_code }}</p>
                  </div>
                </div>
              </td>
              <td class="text-right">¥{{ item.price.toLocaleString() }}</td>
              <td class="text-center">{{ item.quantity }}</td>
              <td class="text-right font-weight-medium">¥{{ item.subtotal.toLocaleString() }}</td>
            </tr>
          </tbody>
        </v-table>

        <v-card variant="flat" color="grey-lighten-4" class="pa-4">
          <div class="d-flex justify-end align-center">
            <span class="text-h6 mr-4">合計（税込）</span>
            <span class="text-h5 font-weight-bold text-primary">¥{{ total.toLocaleString() }}</span>
          </div>
        </v-card>

        <div class="d-flex justify-space-between mt-4">
          <v-btn variant="outlined" :to="'/search_list'">買い物を続ける</v-btn>
          <v-btn color="primary" disabled>レジに進む（未実装）</v-btn>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import apiClient from '@/plugins/axios'
import Header from '@/components/Header.vue'

interface CartItem {
  cart_id: number
  product_id: number
  quantity: number
  add_date: string
  product_name: string
  price: number
  image_url: string | null
  product_code: string
  stock_quantity: number
  subtotal: number
}

const items = ref<CartItem[]>([])
const total = ref(0)
const loading = ref(true)

const fetchCart = async () => {
  loading.value = true
  items.value = []
  total.value = 0
  try {
    const response = await apiClient.post('/api/cart/list')
    if (response.data.success) {
      items.value = response.data.items ?? []
      total.value = response.data.total ?? 0
    }
  } catch (e) {
    console.error('カート取得エラー:', e)
    // 401 の場合は axios インターセプターで /login へリダイレクトされる
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchCart()
})
</script>

<style scoped>
.cart-table {
  min-width: 100%;
}
</style>
