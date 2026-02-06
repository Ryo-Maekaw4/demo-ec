<!--
  new_products.vue … 新着商品ページ（URL が /new_products のとき表示）
  商品一覧APIを sort=newest（デフォルト）で呼び出し、新着順で表示
-->
<template>
  <div class="ma-2">
    <Header />
    <div class="ma-4">
      <h1 class="text-h5 font-weight-bold mb-4">新着商品</h1>

      <div v-if="!loading && products.length === 0" class="text-center pa-8">
        <v-icon size="64" color="grey">mdi-package-variant</v-icon>
        <p class="text-h6 mt-4">新着商品はありません</p>
      </div>

      <v-container v-if="products.length > 0" class="pa-0">
        <v-row>
          <v-col v-for="product in products" :key="product.id" cols="12" sm="3" md="2" lg="2">
            <ProductCard :product="product" show-release-date @show-detail="showProductDetail" />
          </v-col>
        </v-row>
      </v-container>

      <v-pagination v-if="pagination.total_pages > 1" v-model="currentPage" :length="pagination.total_pages"
        :total-visible="7" class="mt-4" @update:model-value="handlePageChange" />

      <v-overlay v-if="loading" class="align-center justify-center">
        <v-progress-circular indeterminate color="primary" size="64" />
      </v-overlay>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import apiClient from '@/plugins/axios'
import Header from '@/components/Header.vue'
import ProductCard from '@/components/ProductCard.vue'

const router = useRouter()
const products = ref<any[]>([])
const loading = ref(false)
const currentPage = ref(1)
const pagination = ref({
  page: 1,
  limit: 20,
  total: 0,
  total_pages: 0,
})

const fetchProducts = async (page: number = 1) => {
  loading.value = true
  try {
    const response = await apiClient.post('/api/product/list', {
      page,
      limit: 20,
      sort: 'newest',
    })
    if (response.data.success) {
      products.value = response.data.products || []
      pagination.value = response.data.pagination || { page: 1, limit: 20, total: 0, total_pages: 0 }
      currentPage.value = pagination.value.page
    }
  } catch (error: any) {
    console.error('新着商品取得エラー:', error)
  } finally {
    loading.value = false
  }
}

const handlePageChange = (page: number) => {
  fetchProducts(page)
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const showProductDetail = (productId: number) => {
  router.push(`/product/${productId}`)
}

onMounted(() => {
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
