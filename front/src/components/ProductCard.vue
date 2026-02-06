<template>
    <!-- @click：カードをクリックしたら商品詳細ページへ遷移 -->
    <v-card class="product-card" variant="outlined" @click="handleClick" style="cursor: pointer; min-width: 150px;">
        <!-- バインド :src：画像のURLを product.image_url から渡す（属性にJSの値を結ぶ） -->
        <v-img :src="product.image_url" height="200" class="bg-grey-lighten-4">
            <template v-if="normalizedReview != null">
                <v-icon v-for="n in fullStars" :key="'full-' + n" size="x-small" class="ma-0">mdi-star</v-icon>
                <v-icon v-if="hasHalfStar" size="x-small" class="ma-0">mdi-star-half-full</v-icon>
                <v-icon v-for="n in emptyStars" :key="'empty-' + n" size="x-small"
                    class="ma-0">mdi-star-outline</v-icon>
            </template>
            <v-chip v-if="showReleaseDate && isReleasedThisYear" color="primary" size="x-small" class="ma-1">
                NEW
            </v-chip>
            <v-chip v-if="!product.is_in_stock" color="error" size="x-small" class="ma-1">在庫なし</v-chip>
            <v-chip v-else-if="product.stock_quantity <= 5" color="warning" size="x-small" class="ma-1">
                残り{{ product.stock_quantity }}個
            </v-chip>
        </v-img>

        <v-card-title class="text-body-1 font-weight-bold">{{ product.name }}</v-card-title>
        <v-card-subtitle class="text-caption text-grey">{{ product.product_code }}</v-card-subtitle>

        <v-card-text class="">
            <span class="text-h6 font-weight-bold text-primary">
                ¥{{ product.price.toLocaleString() }}
            </span>
            <p v-if="showReleaseDate && formattedReleaseDate" class="text-caption text-grey mt-1 mb-0">
                発売日：{{ formattedReleaseDate }}
            </p>
        </v-card-text>
    </v-card>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps({
    product: { type: Object, required: true },
    /** 新着商品ページ用：発売日表示と本年度発売の NEW バッジを表示 */
    showReleaseDate: { type: Boolean, default: false },
})
const emit = defineEmits(['show-detail'])

const handleClick = () => {
    emit('show-detail', props.product.id)
}

// 0.0〜5.0、0.5刻みの評価を正規化（範囲外は null）
const normalizedReview = computed(() => {
    const v = Number(props.product?.review)
    if (Number.isNaN(v) || v < 0 || v > 5) return null
    return Math.round(v * 2) / 2 // 0.5刻みに丸める
})

const fullStars = computed(() => Math.floor(normalizedReview.value ?? 0))
const hasHalfStar = computed(() => (normalizedReview.value ?? 0) % 1 >= 0.5)
// 5つ星のうち、満星・半星で埋まらない分をアウトラインで表示
const emptyStars = computed(() => 5 - fullStars.value - (hasHalfStar.value ? 1 : 0))

// 発売日：YYYY-MM-DD やタイムスタンプを「YYYY年M月D日」に整形
const formattedReleaseDate = computed(() => {
    const v = props.product?.release_date
    if (v == null || v === '') return null
    const d = typeof v === 'number' ? new Date(v * 1000) : new Date(String(v))
    if (Number.isNaN(d.getTime())) return null
    return `${d.getFullYear()}年${d.getMonth() + 1}月${d.getDate()}日`
})

// 本年度（暦年）に発売したか
const isReleasedThisYear = computed(() => {
    const v = props.product?.release_date
    if (v == null || v === '') return false
    const d = typeof v === 'number' ? new Date(v * 1000) : new Date(String(v))
    if (Number.isNaN(d.getTime())) return false
    return d.getFullYear() === new Date().getFullYear()
})
</script>