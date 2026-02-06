<!--
  App.vue … アプリの「土台」になるコンポーネント（基本構造）

  - 全ページ共通のレイアウトと、URLに応じて表示するエリアをここで決めている。
  - ルーティング（router-view）、ライフサイクル（onMounted）を使っている。
-->
<template>
  <!-- v-app: Vuetify用。アプリ全体を包む「枠」 -->
  <v-app>
    <!-- v-main: メインの表示エリア -->
    <!-- router-view：今のURLに合わせて、ここに「どのページ」を表示するか決める。
        例：/ → トップ、/login → ログイン、/search_list → 商品一覧、/product/1 → 商品1の詳細 -->
    <v-main>
      <router-view />
    </v-main>
  </v-app>
</template>

<script lang="ts" setup>
/* Composition API：script setup で ref や onMounted をそのまま使う書き方 */
import { onMounted } from 'vue'
import { useAuth } from '@/composables/useAuth'

const { initAuth, checkAuthStatus } = useAuth()

/* onMounted = 「この画面が表示された直後に一度だけ実行する」処理。
   アプリが起動したタイミングで、ログイン状態を読み込んだりAPIで確認したりしている */
onMounted(() => {
  initAuth()
  checkAuthStatus()
})
</script>
