<!--
  category_list.vue … カテゴリ一覧ページ（URL が /category_list のとき表示）
  家具（１）、家電（２）、食品（３）の3カテゴリを表示。クリックで該当カテゴリの商品一覧へ遷移。
-->
<template>
  <div class="ma-2">
    <Header />
    <div class="ma-4">
      <h1 class="text-h5 font-weight-bold mb-4">カテゴリ一覧</h1>

      <v-container class="pa-0">
        <v-row>
          <v-col
            v-for="category in categories"
            :key="category.id"
            cols="12"
            sm="6"
            md="4"
          >
            <router-link :to="category.path" class="category-link">
              <v-card class="category-card" variant="outlined">
                <v-card-text class="d-flex align-center pa-4">
                  <v-icon :icon="category.icon" size="40" class="me-3" color="primary" />
                  <span class="text-h6">{{ category.name }}（{{ category.id }}）</span>
                </v-card-text>
              </v-card>
            </router-link>
          </v-col>
        </v-row>
      </v-container>
    </div>
  </div>
</template>

<script setup lang="ts">
import Header from '@/components/Header.vue'

const categories = [
  { id: 1, name: '家具', path: '/search_list?category=1', icon: 'mdi-sofa' },
  { id: 2, name: '家電', path: '/search_list?category=2', icon: 'mdi-television' },
  { id: 3, name: '食品', path: '/search_list?category=3', icon: 'mdi-food-apple' },
]
</script>

<style scoped>
.category-link {
  text-decoration: none;
  color: inherit;
}

.category-card {
  cursor: pointer;
  transition: box-shadow 0.2s;
}

.category-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}
</style>
