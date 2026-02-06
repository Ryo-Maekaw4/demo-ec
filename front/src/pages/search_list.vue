<!--
  search_list.vue … 商品一覧・検索ページ（URL が /search_list のとき表示）
  使っているVueの機能：{{ }}、v-model、:src などバインド、v-if/v-else、v-for、:key、@click、ref、onMounted
-->
<template>
  <div class="ma-2">
    <Header />
    <div class="ma-4">
      <div class="d-flex justify-space-between mb-4">
        <!-- 見出し：カテゴリ／キーワードに応じて切り替え -->
        <h1 class="text-h5 font-weight-bold">
          <template v-if="appliedKeyword && appliedCategoryName">
            「{{ appliedKeyword }}」の検索結果（{{ appliedCategoryName }}）
          </template>
          <template v-else-if="appliedKeyword">
            「{{ appliedKeyword }}」の検索結果
          </template>
          <template v-else-if="appliedCategoryName">
            {{ appliedCategoryName }}の商品一覧
          </template>
          <template v-else>
            商品一覧
          </template>
        </h1>

        <v-card color="white" variant="flat">
          <v-card-text class="pt-0 pb-0">
            <!-- v-model：検索窓の文字と searchKeyword を双方向で結ぶ。
                虫眼鏡クリック or Enter で handleSearch を実行（イベント） -->
            <v-text-field v-model="searchKeyword" append-inner-icon="mdi-magnify" density="compact" label="商品を探す"
              variant="outlined" hide-details single-line min-width="300" @click:append-inner="handleSearch"
              @keyup.enter="handleSearch"></v-text-field>
          </v-card-text>
        </v-card>
      </div>

      <!-- カテゴリ絞り込み -->
      <div class="mb-4">
        <v-select
          v-model="appliedCategoryId"
          :items="categoryOptions"
          item-title="name"
          item-value="id"
          label="カテゴリ"
          density="compact"
          variant="outlined"
          hide-details
          class="category-filter"
          style="max-width: 200px"
          @update:model-value="handleCategoryChange"
        />
      </div>

      <!-- v-if：ローディング中でなく、かつ商品が0件のときだけこのメッセージを表示 -->
      <div v-if="!loading && products.length === 0" class="text-center pa-8">
        <v-icon size="64" color="grey">mdi-package-variant</v-icon>
        <p class="text-h6 mt-4">商品が見つかりませんでした</p>
        <p class="text-body-2 text-grey">検索キーワードを変更してお試しください</p>
      </div>

      <!-- v-for：products 配列の「1つ1つの商品」を product として繰り返し表示。
          :key：リストの各要素を一意に識別する値。Vueが正しく更新するために必要 -->
      <v-container v-if="products.length > 0" class="pa-0">
        <v-row>
          <v-col v-for="product in products" :key="product.id" cols="12" sm="3" md="2" lg="2">
            <ProductCard :product="product" @show-detail="showProductDetail" />
          </v-col>
        </v-row>
      </v-container>

      <!-- v-model：今のページ番号と currentPage を双方向で同期。
          @update:model-value：ページを変えたときに handlePageChange を実行（イベント） -->
      <v-pagination v-if="pagination.total_pages > 1" v-model="currentPage" :length="pagination.total_pages"
        :total-visible="7" class="mt-4" @update:model-value="handlePageChange"></v-pagination>

      <v-overlay v-if="loading" class="align-center justify-center">
        <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
      </v-overlay>
    </div>
  </div>
</template>

<script setup lang="ts">
/* Composition API：ref で「箱」を用意。script 内で値を変えるときは .value を使う */
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import apiClient from '@/plugins/axios'
import Header from '@/components/Header.vue'
import ProductCard from '@/components/ProductCard.vue';

const router = useRouter()

const products = ref<any[]>([])
const searchKeyword = ref('')
const appliedKeyword = ref('')
const appliedCategoryId = ref<number>(0)
const loading = ref(false)
const currentPage = ref(1)
const pagination = ref({
  page: 1,
  limit: 20,
  total: 0,
  total_pages: 0,
})

// カテゴリID → 表示名（カテゴリマスタ 1:家具, 2:家電, 3:食品）
const CATEGORY_NAMES: Record<number, string> = {
  1: '家具',
  2: '家電',
  3: '食品',
}
const appliedCategoryName = ref<string>('')

// カテゴリ絞り込みの選択肢（0 = すべて）
const categoryOptions = [
  { id: 0, name: 'すべて' },
  { id: 1, name: '家具' },
  { id: 2, name: '家電' },
  { id: 3, name: '食品' },
]

const fetchProducts = async (page: number = 1, categoryId?: number) => {
  loading.value = true
  try {
    const body: Record<string, string | number> = {
      keyword: searchKeyword.value,
      page: page,
      limit: 20,
    }
    const catId = categoryId !== undefined ? categoryId : appliedCategoryId.value
    if (typeof catId === 'number' && catId > 0) {
      body.category = catId
    }
    const response = await apiClient.post('/api/product/list', body)
    if (response.data.success) {
      products.value = response.data.products || []
      pagination.value = response.data.pagination || { page: 1, limit: 20, total: 0, total_pages: 0 }
      currentPage.value = pagination.value.page
    }
  } catch (error: any) {
    console.error('商品一覧取得エラー:', error)
  } finally {
    loading.value = false
  }
}

const handleSearch = () => {
  currentPage.value = 1
  appliedKeyword.value = (searchKeyword.value || '').trim()
  fetchProducts(1)
}

const handleCategoryChange = (value: number | string | null | undefined) => {
  const num = typeof value === 'number' ? value : (value != null && value !== '' ? Number(value) : 0)
  const id = Number.isNaN(num) ? 0 : num
  appliedCategoryId.value = id
  appliedCategoryName.value = id > 0 ? (CATEGORY_NAMES[id] ?? '') : ''
  currentPage.value = 1
  fetchProducts(1, id)
}

const handlePageChange = (page: number) => {
  fetchProducts(page)
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

/* router.push で別のページ（商品詳細）へプログラムから遷移 */
const showProductDetail = (productId: number) => {
  router.push(`/product/${productId}`)
}

const addToCart = (product: any) => {
  console.log('カートに追加:', product)
}

/* onMounted：このページが表示された「直後」に一度だけ実行。
   URL は使わず、現在の状態で商品一覧をAPIで取得 */
onMounted(() => {
  // カテゴリ一覧からの遷移時は、URLクエリではなく history state でカテゴリIDを受け取る
  const raw = (window.history.state as any)?.categoryId
  const id = typeof raw === 'number' ? raw : (raw != null && raw !== '' ? Number(raw) : 0)
  const categoryId = Number.isNaN(id) ? 0 : id

  if (categoryId > 0) {
    appliedCategoryId.value = categoryId
    appliedCategoryName.value = CATEGORY_NAMES[categoryId] ?? ''
    fetchProducts(1, categoryId)
    return
  }

  fetchProducts(1)
})
</script>

<style scoped>
.product-card {
  transition: transform 0.2s, box-shadow 0.2s;
}

.product-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}
</style>
