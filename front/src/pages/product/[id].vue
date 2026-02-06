<!--
  product/[id].vue … 商品詳細ページ（URL が /product/123 のように表示）
  使っているVueの機能：{{ }}、:src / :to / :disabled でバインド、v-if/v-else-if/v-else、@click、ref、watch、onMounted
-->
<template>
  <div class="ma-2">
    <Header />
    <div class="ma-4">
      <!-- v-if：loading が true のときだけスピナーを表示（条件分岐） -->
      <div v-if="loading" class="d-flex justify-center align-center pa-12">
        <v-progress-circular indeterminate color="primary" size="64" />
      </div>

      <!-- v-else-if：loading でなく、かつ product が null のとき「見つかりませんでした」 -->
      <div v-else-if="!product" class="text-center pa-12">
        <v-icon size="64" color="grey">mdi-package-variant-remove</v-icon>
        <p class="text-h6 mt-4">商品が見つかりませんでした</p>
        <!-- :to はバインド。リンクの飛び先を '/search_list' にしている -->
        <v-btn color="primary" class="mt-4" :to="'/search_list'">商品一覧へ</v-btn>
      </div>

      <!-- v-else：それ以外（商品が取れたとき）は詳細を表示 -->
      <v-container v-else class="product-detail">
        <v-row>
          <v-col cols="12" md="5">
            <!-- :src：画像URLを product.image_url から渡す（バインド） -->
            <v-img
              :src="product.image_url || 'https://via.placeholder.com/400x400?text=No+Image'"
              aspect-ratio="1"
              cover
              class="rounded-lg bg-grey-lighten-3"
            />
          </v-col>
          <v-col cols="12" md="7">
            <p class="text-caption text-grey mb-1">商品コード: {{ product.product_code }}</p>
            <h1 class="text-h4 font-weight-bold mb-3">{{ product.name }}</h1>
            <p class="text-h5 font-weight-bold text-primary mb-4">
              ¥{{ product.price.toLocaleString() }} <span class="text-body-2 font-weight-normal">（税込）</span>
            </p>

            <div class="mb-4">
              <v-chip v-if="!product.is_in_stock" color="error" class="mb-2">在庫なし</v-chip>
              <v-chip v-else-if="product.stock_quantity <= 5" color="warning" class="mb-2">
                残り{{ product.stock_quantity }}個
              </v-chip>
              <v-chip v-else color="success" class="mb-2">在庫あり</v-chip>
            </div>

            <p v-if="product.description" class="text-body-1 mb-4">{{ product.description }}</p>

            <v-row class="mb-4">
              <v-col cols="12" sm="6">
                <h5 class="mb-2">個数入力</h5>
                <v-number-input
                  v-model="quantity"
                  :min="1"
                  :max="product.stock_quantity"
                  control-variant="split"
                  inset
                />
              </v-col>
            </v-row>

            <div class="d-flex flex-wrap">
              <v-btn
                color="primary"
                size="large"
                prepend-icon="mdi-cart-plus"
                :disabled="!product.is_in_stock"
                @click="addToCart"
              >
                カートに追加する
              </v-btn>
              <v-btn variant="outlined" size="large" class="ml-2" :to="'/search_list'">
                商品一覧へ戻る
              </v-btn>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </div>

    <v-snackbar
      v-model="snackbar"
      multi-line
    >
      {{ snackbarText }}
      <template v-slot:actions>
        <v-btn
          color="red"
          variant="text"
          @click="snackbar = false"
        >
          Close
        </v-btn>
      </template>
    </v-snackbar>
  </div>
</template>

<script setup lang="ts">
/* Composition API：ref で「箱」を用意。API で取った商品データを product に格納 */
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import apiClient from '@/plugins/axios'
import Header from '@/components/Header.vue'
import { useCart } from '@/composables/useCart'

const route = useRoute()
const { fetchCount } = useCart()

interface Product {
  id: number
  product_code: string
  name: string
  price: number
  description: string | null
  image_url: string | null
  stock_quantity: number
  is_in_stock: boolean
}

const product = ref<Product | null>(null)
const loading = ref(true)
const quantity = ref(1)
const snackbar = ref(false)
const snackbarText = ref('')

const fetchProduct = async (id: string) => {
  loading.value = true
  product.value = null
  try {
    const response = await apiClient.post('/api/product/detail', { id })
    if (response.data.success) {
      product.value = response.data.product
      quantity.value = 1
    }
  } catch (e) {
    console.error('商品詳細取得エラー:', e)
  } finally {
    loading.value = false
  }
}

const addToCart = async () => {
  if (!product.value) return
  const qty = Math.max(1, Math.min(product.value.stock_quantity, quantity.value))
  try {
    const response = await apiClient.post('/api/cart/add', {
      product_id: product.value.id,
      quantity: qty,
    })
    if (response.data.success) {
      snackbarText.value = response.data.message ?? 'カートに追加しました'
      snackbar.value = true
      await fetchCount()
    } else {
      snackbarText.value = response.data.message ?? 'カートに追加できませんでした'
      snackbar.value = true
    }
  } catch (e: unknown) {
    const msg = (e as { response?: { data?: { message?: string } } })?.response?.data?.message
    snackbarText.value = msg ?? 'カートに追加に失敗しました（ログインが必要な場合があります）'
    snackbar.value = true
  }
}

/* onMounted：このページが表示された直後に一度だけ実行。URL の id を読んでAPIで商品を取得 */
onMounted(() => {
  const id = (route.params as { id?: string }).id
  if (id) fetchProduct(id)
})

/* watch：「ある値が変わったとき」に処理を実行する。
   ここでは「URL の id（商品ID）」が変わったとき（例：/product/1 → /product/2）に、再度APIで商品を取得 */
watch(() => (route.params as { id?: string }).id, (newId) => {
  if (newId) fetchProduct(newId as string)
})
</script>

<style scoped>
.product-detail {
  max-width: 900px;
}
</style>
