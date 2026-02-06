<!--
  login.vue … ログインページ（URL が /login のとき表示）
  使っているVueの機能：データバインディング（{{ }}、v-model）、ディレクティブ（v-if）、イベント（@submit.prevent）、ref / script setup
-->
<template>
  <div class="ma-4">
    <Header />
    <v-card max-width="400" class="mx-auto mt-4" variant="flat" color="grey-lighten-3">
      <v-card-title>
        <h1 class=" text-h4">ログイン</h1>
      </v-card-title>
      <v-card-text>
        <!-- v-if：errorMessage が「あるときだけ」このブロックを表示する（条件分岐） -->
        <!-- イベント：閉じるボタンが押されたら errorMessage を空にする -->
        <v-alert v-if="errorMessage" type="error" class="mb-4" closable @click:close="errorMessage = ''">
          <!-- マスタッシュ {{ }}：ref の値（errorMessage）をここに表示 -->
          {{ errorMessage }}
        </v-alert>
        <!-- イベント：フォーム送信時に handleLogin を実行。.prevent でページが再読み込みされないようにする -->
        <v-form @submit.prevent="handleLogin">
          <!-- 双方向バインディング v-model：入力欄と「email という ref」を結びつける。
              ユーザーが打つと email が変わり、プログラムで email を変えると入力欄も変わる -->
          <!-- :disabled はバインド（: は v-bind の省略）。loading が true のときだけ入力不可にする -->
          <v-text-field v-model="email" label="メールアドレス" type="email" :disabled="loading" required></v-text-field>
          <v-text-field v-model="password" label="パスワード" type="password" :disabled="loading" required></v-text-field>
          <v-checkbox v-model="rememberMe" label="ログイン状態を保持する" :disabled="loading"></v-checkbox>
          <v-btn type="submit" block color="primary" :loading="loading" :disabled="loading">ログイン</v-btn>
        </v-form>
      </v-card-text>
    </v-card>
  </div>
</template>

<script setup lang="ts">
/* Composition API：ref で「リアクティブな箱」を用意。中身が変わると画面も自動で更新される */
import { useRouter } from 'vue-router'
import { ref } from 'vue'
import { useAuth } from '@/composables/useAuth'

const router = useRouter()
const { login } = useAuth()

/* ref：値を入れておく「箱」。.value で中身を読んだり書き換えたりする。
   template では .value を書かなくてもよく、email と書くだけで中身が表示される */
const email = ref('')
const password = ref('')
const rememberMe = ref(false)
const loading = ref(false)
const errorMessage = ref('')

const handleLogin = async () => {
  loading.value = true
  errorMessage.value = ''

  const result = await login(email.value, password.value, rememberMe.value)

  if (result.success) {
    router.push('/mypage')  /* ルーティング：プログラムからマイページへ移動 */
  } else {
    errorMessage.value = result.message || 'ログインに失敗しました'
  }

  loading.value = false
}
</script>

<style scoped>
.fill-height {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>
