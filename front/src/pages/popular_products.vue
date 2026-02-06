<!--
  popular_products.vue … 人気商品（ランキング）ページ（URL が /popular_products のとき表示）
  商品一覧APIを sort=popular で呼び出し、評価（review）の高い順で表示。縦1列・横長カード。
-->
<template>
    <div class="ma-2">
        <Header />
        <div class="ma-4">
            <h1 class="text-h5 font-weight-bold mb-4">人気商品（ランキング）</h1>

            <div v-if="!loading && products.length === 0" class="text-center pa-8">
                <v-icon size="64" color="grey">mdi-trophy</v-icon>
                <p class="text-h6 mt-4">人気商品はありません</p>
            </div>

            <v-container v-if="products.length > 0" class="pa-4">
                <v-row>
                    <v-col v-for="(product, index) in products" :key="product.id" cols="12">
                        <v-card class="popular-card d-flex align-stretch" variant="outlined"
                            @click="showProductDetail(product.id)">
                            <v-card class="popular-card-inner d-flex flex-grow-1 ma-1" variant="flat">
                                <span v-if="index < 3" class="rank-badge">{{ index + 1 }}</span>
                                <span v-else class="rank-number">{{ index + 1 }}</span>
                                <v-card class="popular-card-img-wrap flex-shrink-0" variant="outlined">
                                    <v-img :src="product.image_url" width="160" min-width="160" height="160" cover
                                        class="popular-card-img bg-grey-lighten-4">
                                        <v-chip v-if="!product.is_in_stock" color="error" size="x-small"
                                            class="ma-1">在庫なし</v-chip>
                                        <v-chip v-else-if="product.stock_quantity <= 5" color="warning" size="x-small"
                                            class="ma-1">
                                            残り{{ product.stock_quantity }}個
                                        </v-chip>
                                    </v-img>
                                </v-card>
                                <div class="popular-card-body d-flex flex-column flex-grow-1 pa-3">
                                    <div class="d-flex align-center">
                                        <div>
                                            <v-card-title class="text-body-1 font-weight-bold text-left pa-0 pb-1">
                                                {{ product.name }}
                                            </v-card-title>
                                            <v-card-subtitle class="text-caption text-grey text-left pa-0 pb-1">
                                                {{ product.product_code }}
                                            </v-card-subtitle>
                                            <div class="popular-card-stars mt-1 mb-1">
                                                <v-icon v-for="n in fullStars(product)"
                                                    :key="'full-' + product.id + '-' + n" size="x-small"
                                                    class="ma-0">mdi-star</v-icon>
                                                <v-icon v-if="hasHalfStar(product)" size="x-small"
                                                    class="ma-0">mdi-star-half-full</v-icon>
                                                <v-icon v-for="n in emptyStars(product)"
                                                    :key="'empty-' + product.id + '-' + n" size="x-small"
                                                    class="ma-0">mdi-star-outline</v-icon>
                                            </div>
                                            <div class="mt-auto pt-2">
                                                <span class="text-h6 font-weight-bold text-primary">¥{{
                                                    product.price.toLocaleString() }}</span>
                                            </div>
                                            <div class="mt-auto pt-2">
                                                <span class="text-caption text-secondary text-no-wrap">{{ product.release_date }}発売</span>
                                            </div>
                                        </div>
                                        <div class="ml-auto pa-4">{{ product.description }}</div>
                                    </div>
                                </div>
                            </v-card>
                        </v-card>
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
      sort: 'popular',
    })
        if (response.data.success) {
            products.value = response.data.products || []
            pagination.value = response.data.pagination || { page: 1, limit: 20, total: 0, total_pages: 0 }
            currentPage.value = pagination.value.page
        }
    } catch (error: any) {
        console.error('人気商品取得エラー:', error)
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

// 評価（0.0〜5.0）の星表示用
const normalizedReview = (product: any) => {
    const v = Number(product?.review)
    if (Number.isNaN(v) || v < 0 || v > 5) return null
    return Math.round(v * 2) / 2
}
const fullStars = (product: any) => Math.floor(normalizedReview(product) ?? 0)
const hasHalfStar = (product: any) => ((normalizedReview(product) ?? 0) % 1) >= 0.5
const emptyStars = (product: any) =>
    5 - fullStars(product) - (hasHalfStar(product) ? 1 : 0)

onMounted(() => {
    fetchProducts(1)
})
</script>

<style scoped>
.popular-card {
    cursor: pointer;
    transition: box-shadow 0.2s;
}

.popular-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.popular-card-inner {
    position: relative;
    width: 100%;
    min-height: 160px;
}

.popular-card-img {
    flex-shrink: 0;
}

.popular-card-body {
    text-align: left;
    min-width: 0;
}

.rank-badge {
    position: absolute;
    top: 8px;
    left: 8px;
    z-index: 1;
    min-width: 28px;
    height: 28px;
    line-height: 28px;
    text-align: center;
    font-weight: bold;
    font-size: 0.875rem;
    border-radius: 50%;
    background: linear-gradient(135deg, #ffd700 0%, #ffb347 100%);
    color: #333;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
}

.rank-number {
    position: absolute;
    top: 8px;
    left: 8px;
    z-index: 1;
    min-width: 28px;
    height: 28px;
    line-height: 28px;
    text-align: center;
    font-weight: bold;
    font-size: 0.875rem;
    border-radius: 50%;
    background: rgba(0, 0, 0, 0.5);
    color: #fff;
}
</style>
