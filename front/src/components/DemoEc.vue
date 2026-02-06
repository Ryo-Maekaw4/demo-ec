<!--
  DemoEc.vue … トップページのメインコンポーネント
  使っているVueの機能：{{ }}、v-model、:src / :title でバインド、v-for、:key、v-if、@click、ref、onMounted、router.push
-->
<template>
  <div class="ma-2">
    <Header />
  </div>
  <div>
    <!-- バインド :show-arrows：コロン : は v-bind の省略。属性に「JSの式の結果」を渡している -->
    <v-carousel cycle :show-arrows="false" delimiter-icon="mdi-square">
      <v-carousel-item src="https://cdn.vuetifyjs.com/images/cards/docks.jpg" cover></v-carousel-item>
      <v-carousel-item src="https://cdn.vuetifyjs.com/images/cards/hotel.jpg" cover></v-carousel-item>
      <v-carousel-item src="https://cdn.vuetifyjs.com/images/cards/sunshine.jpg" cover></v-carousel-item>
    </v-carousel>
  </div>
  <v-card class="mx-auto" color="white" max-width="400" variant="flat">
    <v-card-text>
      <!-- v-model：検索窓の文字と topSearchKeyword を双方向で結ぶ。
          虫眼鏡クリック or Enter で search を実行（イベント） -->
      <v-text-field v-model="topSearchKeyword" append-inner-icon="mdi-magnify" density="compact" label="商品を探す"
        variant="outlined" hide-details single-line clearable @click:append-inner="search"
        @keyup.enter="search"></v-text-field>
    </v-card-text>
  </v-card>

  <v-card max-width="800" color="grey-lighten-3" class="ma-2 pb-2 px-2 mx-auto bg-white" title="新着情報" variant="flat">
    <v-list lines="one">
      <!-- v-for：n を 1, 2, 3 で繰り返す（3回表示）。:key で各要素を一意に。:title でバインド -->
      <v-list-item v-for="n in 3" :key="n" :title="'2025/01/2' + n"
        subtitle="Lorem ipsum dolor sit amet, consectetur adipisci elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur. Quis aute iure reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint obcaecat cupiditat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."></v-list-item>
    </v-list>
  </v-card>

  <div class="">
    <v-sheet class="mx-auto" variant="flat" color="grey-lighten-3">
      <!-- v-model：スライドで「今どれが選ばれているか」を model という ref に自動で入れる -->
      <v-slide-group v-model="model" class="pa-4" selected-class="bg-primary" show-arrows>
        <!-- v-for：products 配列の要素の数だけ、商品カードを繰り返し表示。:key は product.id -->
        <v-slide-group-item v-for="product in products" :key="product.id"
          v-slot="{ isSelected, toggle, selectedClass }">
          <!-- @click：カードをクリックしたら商品詳細ページへ遷移（イベントハンドリング） -->
          <v-card style="margin: 0 20px 0 0; max-width: 400px; min-width: 180px; max-height: 280px; cursor: pointer;"
            @click="showProductDetail(product.id)">
            <!-- :src：画像のURLを product.image_url から渡す（バインド）。なければプレースホルダー -->
            <v-img class="white--text align-end" height="200px"
              :src="product.image_url || 'https://via.placeholder.com/300x300?text=No+Image'" cover>
              <v-card-title class="text-white text-caption" style="background: rgba(0,0,0,0.5);">
                {{ product.name }}
              </v-card-title>
            </v-img>
            <v-card-text>
              <div class="d-flex align-center">
                <div class="text-h6">¥{{ product.price.toLocaleString() }}</div>
                <!-- v-if：在庫がないときだけ「在庫なし」チップを表示 -->
                <v-chip v-if="!product.is_in_stock" color="error" size="x-small" class="ml-2">在庫なし</v-chip>
              </div>
            </v-card-text>
          </v-card>
        </v-slide-group-item>
      </v-slide-group>

      <!-- v-if：model に値が入っているときだけ、選択中の表示エリアを出す -->
      <v-expand-transition>
        <v-sheet v-if="model != null" height="200">
          <div class="d-flex fill-height align-center justify-center">
            <h3 class="text-h6">Selected {{ model }}</h3>
          </div>
        </v-sheet>
      </v-expand-transition>
    </v-sheet>
  </div>
</template>

<script setup lang="ts">
/* Composition API：ref で「箱」を用意。script 内で中身を変えるときは .value を使う */
import { useRouter } from 'vue-router'
import { ref, onMounted } from 'vue'
import apiClient from '@/plugins/axios'

interface Product {
  id: number
  product_code: string
  name: string
  price: number
  image_url?: string
  is_in_stock: boolean
  stock_quantity: number
}

const router = useRouter()
const model = ref<number | null>(null)
const products = ref<Product[]>([])
const loading = ref<boolean>(false)
const topSearchKeyword = ref('')

const fetchProducts = async () => {
  loading.value = true
  try {
    const response = await apiClient.post('/api/product/list', {
      keyword: '',
      page: 1,
      limit: 20,
    })
    if (response.data.success) {
      products.value = response.data.products || []
    }
  } catch (error: any) {
    console.error('商品一覧取得エラー:', error)
  } finally {
    loading.value = false
  }
}

/* router.push で商品詳細ページ（/product/123 など）へ遷移 */
const showProductDetail = (productId: number) => {
  router.push(`/product/${productId}`)
}

const search = () => {
  const keyword = (topSearchKeyword.value || '').trim()
  router.push({ path: '/search_list', query: keyword ? { keyword } : {} })
}

/* onMounted：このコンポーネントが画面に表示された「直後」に一度だけ実行。APIで商品一覧を取得 */
onMounted(() => {
  fetchProducts()
})
</script>
