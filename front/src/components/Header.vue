<!--
  Header.vue … ヘッダーコンポーネント
  使っているVueの機能：v-if / v-else、v-for、:key、@click、router-link / router.push、{{ }}、:to / :prepend-icon でバインド、<template> でブロック分岐
-->
<template>
  <div class="d-flex justify-space-between">
    <!-- router-link：クリックすると to で指定したパス（/）に遷移する。aタグのようなもの -->
    <router-link to="/"><img src="@/assets/vue_ec.png" width="300" height="100" alt="DEMO EC"
        class="logo"></router-link>
    <div class="text-right d-flex align-center">
      <!-- @click：クリックしたら openSearch が実行され、検索ページへ遷移 -->
      <v-btn prepend-icon="mdi-magnify" variant="text" size="x-large" @click="openSearch">SEARCH</v-btn>
      <!-- v-if：isLoggedIn が true のときだけ MYPAGE ボタンを表示（条件分岐） -->
      <v-btn prepend-icon="mdi-account" variant="text" size="x-large" v-if="isLoggedIn"
        @click="openMypage">MYPAGE</v-btn>
      <!-- v-else：上の v-if が false のとき（未ログインのとき）は LOGIN ボタンを表示 -->
      <v-btn prepend-icon="mdi-account-outline" variant="text" size="x-large" v-else @click="openLogin">LOGIN</v-btn>
      <v-btn v-if="isLoggedIn" prepend-icon="mdi-cart-outline" variant="text" size="x-large" :to="'/cart'">
        <v-badge v-if="cartBadgeContent" location="top right" color="primary" :content="cartBadgeContent" :offset-x="10"
          :offset-y="-10">
        </v-badge>
        CART
      </v-btn>
      <v-btn v-else prepend-icon="mdi-cart-outline" variant="text" size="x-large" :to="'/cart'">CART</v-btn>
      <v-btn prepend-icon="mdi-menu" variant="outlined" size="x-large">MENU
        <v-menu activator="parent">
          <v-list class="hamburger-menu-list pa-0">
            <!-- v-for：hamburgerMenu 配列の1つ1つを item として繰り返し表示。
                :key：各項目を一意に識別するため item.path または item.menuTitle を使う -->
            <v-list-item v-for="item in hamburgerMenu" :key="item.path || item.menuTitle" class="ps-0 pe-0">
              <!-- template + v-if：複数の要素をまとめて「条件が true のときだけ」表示する。
                  template はDOMに残らない「見えないラッパー」。menuTitle が ITEM で category があるとき子リスト表示 -->
              <template v-if="item.menuTitle === 'ITEM' && item.category?.length">
                <div class="ma-1 pa-1 rounded bg-grey-lighten-2">
                  <v-list-item-title class="text-h6 font-weight-bold">{{ item.menuTitle }}</v-list-item-title>
                  <v-list density="compact" class="pt-0 pb-0 pl-4 bg-grey-lighten-2">
                    <v-list-item v-for="category in item.category" :key="category.path" :to="category.path" link>
                      {{ category.categoryName }}
                    </v-list-item>
                  </v-list>
                </div>
              </template>
              <!-- v-else：それ以外の項目はボタン1つで :to のページへ遷移。:prepend-icon はバインド（動的にアイコン名を渡す） -->
              <v-btn v-else :prepend-icon="item.icon ? 'mdi-' + item.icon : undefined" variant="text" size="x-large"
                block :to="item.path">
                {{ item.menuTitle }}
              </v-btn>
            </v-list-item>
          </v-list>
        </v-menu>
      </v-btn>
    </div>
  </div>
  <div>
    <!-- v-for、:to / :text：タブの「リンク先」と「表示テキスト」を tabMenu のデータからバインド -->
    <v-tabs bg-color="surface-variant" fixed-tabs>
      <v-tab v-for="tab in tabMenu" :key="tab.path" :to="tab.path" :text="tab.text">
        {{ tab.text }}
      </v-tab>
    </v-tabs>
  </div>
</template>

<script setup lang="ts">
/* Composition API。ここではメニュー用の配列をそのまま定数で定義（変更しないので ref ではない） */
import { useRouter } from 'vue-router'
import { ref, computed, onMounted, watch } from 'vue'
import { useAuth } from '@/composables/useAuth'
import { useCart } from '@/composables/useCart'

const router = useRouter()
const { isLoggedIn } = useAuth()
const { cartCount, fetchCount, clearCount } = useCart()

const cartBadgeContent = computed(() => {
  if (!isLoggedIn.value || cartCount.value <= 0) return null
  return cartCount.value >= 99 ? '99+' : String(cartCount.value)
})

onMounted(() => {
  if (isLoggedIn.value) fetchCount()
})

watch(isLoggedIn, (loggedIn) => {
  if (loggedIn) fetchCount()
  else clearCount()
})

const tabMenu = [
  { text: '商品一覧', path: '/search_list' },
  { text: '新着商品', path: '/new_products' },
  { text: '人気商品', path: '/popular_products' },
  { text: 'カテゴリ一覧', path: '/category_list' },
]

const hamburgerMenu = [
  {
    menuTitle: 'ITEM',
    category: [
      { categoryName: 'ランキング', path: '/ranking' },
      { categoryName: '新着商品', path: '/new_products' },
      { categoryName: 'カテゴリ別', path: '/popular_products' },
    ]
  },
  { menuTitle: 'マイページ', icon: 'account-outline', path: '/mypage' },
  { menuTitle: 'カート情報', icon: 'cart-outline', path: '/cart' },
]

const isHamburgerMenuOpen = ref(false)

const openHamburgerMenu = () => {
  isHamburgerMenuOpen.value = true
}

/* router.push でプログラムから別のページへ移動 */
const openLogin = () => { router.push('/login') }
const openMypage = () => { router.push('/mypage') }
const openSearch = () => { router.push('/search_list') }
</script>

<style scoped>
.hamburger-menu-title,
.hamburger-menu-category-list {
  background-color: #bbb;
}
</style>
