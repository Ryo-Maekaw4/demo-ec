<!--
  mypage.vue … マイページ（ログイン後のページ）
  ユーザー情報表示・ユーザー情報変更・ログアウト
-->
<template>
  <div class="ma-2">
    <Header />
    <div class="ma-4">
      <v-card class="mx-auto" max-width="720" variant="elevated">
        <v-card-title>
          <h1 class="text-h5 font-weight-bold">マイページ</h1>
        </v-card-title>
        <v-card-text>
          <!-- 現在のユーザー情報表示 -->
          <v-list class="mb-4">
            <v-list-subheader class="font-weight-bold">ログイン中のアカウント</v-list-subheader>
            <v-list-item>
              <template #prepend>
                <v-icon>mdi-account-key</v-icon>
              </template>
              <v-list-item-title>ログインID（ユーザー名）</v-list-item-title>
              <v-list-item-subtitle>{{ user?.username ?? '—' }}</v-list-item-subtitle>
            </v-list-item>
            <v-list-item>
              <template #prepend>
                <v-icon>mdi-email</v-icon>
              </template>
              <v-list-item-title>メールアドレス</v-list-item-title>
              <v-list-item-subtitle>{{ user?.email ?? '—' }}</v-list-item-subtitle>
            </v-list-item>
            <v-list-item>
              <template #prepend>
                <v-icon>mdi-account-circle</v-icon>
              </template>
              <v-list-item-title>表示名</v-list-item-title>
              <v-list-item-subtitle>{{ user?.name || '（未設定）' }}</v-list-item-subtitle>
            </v-list-item>
          </v-list>

          <v-divider class="my-4" />

          <!-- ユーザー情報変更フォーム -->
          <h2 class="text-h6 font-weight-bold mb-3">ユーザー情報の変更</h2>
          <v-form ref="formRef" @submit.prevent="handleUpdate">
            <v-text-field v-model="editName" label="表示名" variant="outlined" density="compact" hide-details class="mb-3"
              placeholder="例：山田 太郎" />
            <v-text-field v-model="editEmail" label="メールアドレス" type="email" variant="outlined" density="compact"
              hide-details class="mb-3" placeholder="例：user@example.com" :rules="[rules.required, rules.email]" />
            <p class="text-caption text-grey mb-2">
              ※ログインID（ユーザー名）は変更できません。
            </p>
            <v-alert v-if="updateMessage" :type="updateSuccess ? 'success' : 'error'" density="compact" class="mb-3">
              {{ updateMessage }}
            </v-alert>
            <v-btn type="submit" color="primary" :loading="updating">変更を保存</v-btn>
          </v-form>

          <v-divider class="my-4" />

          <!-- パスワード変更（ログイン中ユーザーのみ・API側でJWTからユーザー特定） -->
          <h2 class="text-h6 font-weight-bold mb-3">パスワード変更</h2>
          <v-form ref="passwordFormRef" @submit.prevent="handlePasswordChange">
            <v-text-field v-model="newPassword" label="新しいパスワード" :type="showNewPassword ? 'text' : 'password'"
              variant="outlined" density="compact" hide-details class="mb-3" placeholder="6文字以上"
              :rules="[rules.required, rules.minLength]"
              :append-inner-icon="showNewPassword ? 'mdi-eye-off' : 'mdi-eye'"
              @click:append-inner="showNewPassword = !showNewPassword" />
            <v-text-field v-model="newPasswordConfirm" label="新しいパスワード（確認）"
              :type="showNewPasswordConfirm ? 'text' : 'password'" variant="outlined" density="compact" hide-details
              class="mb-3" placeholder="もう一度入力" :rules="[rules.required, rules.passwordMatch]"
              :append-inner-icon="showNewPasswordConfirm ? 'mdi-eye-off' : 'mdi-eye'"
              @click:append-inner="showNewPasswordConfirm = !showNewPasswordConfirm" />
            <v-alert v-if="passwordMessage" :type="passwordSuccess ? 'success' : 'error'" density="compact"
              class="mb-3">
              {{ passwordMessage }}
            </v-alert>
            <v-btn type="submit" color="primary" variant="outlined" :loading="changingPassword">パスワードを変更</v-btn>
          </v-form>

          <v-divider class="my-4" />

          <!-- ログアウト -->
          <h2 class="text-h6 font-weight-bold mb-3">ログアウト</h2>
          <p class="text-body-2 text-grey mb-2">ログアウトするとトップページに戻ります。</p>
          <v-btn color="error" variant="outlined" prepend-icon="mdi-logout" @click="handleLogout">
            ログアウト
          </v-btn>

          <v-divider class="my-4" />

          <!-- 今後の拡張案 -->
          <h2 class="text-h6 font-weight-bold mb-2">このあと追加すると便利な機能</h2>
          <v-list density="compact">
            <v-list-item title="注文履歴" prepend-icon="mdi-history" subtitle="過去の注文一覧・詳細" />
            <v-list-item title="お気に入り・ウィッシュリスト" prepend-icon="mdi-heart" subtitle="気になる商品の保存" />
            <v-list-item title="配送先住所の管理" prepend-icon="mdi-map-marker" subtitle="複数住所の登録・編集" />
          </v-list>
        </v-card-text>
      </v-card>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '@/composables/useAuth'
import Header from '@/components/Header.vue'

const router = useRouter()
const { user, logout, updateProfile, changePassword } = useAuth()

const formRef = ref<{ validate: () => Promise<{ valid: boolean }> } | null>(null)
const passwordFormRef = ref<{ validate: () => Promise<{ valid: boolean }> } | null>(null)
const editName = ref('')
const editEmail = ref('')
const updating = ref(false)
const updateMessage = ref('')
const updateSuccess = ref(false)

const passwordConfirmEmail = ref('')
const newPassword = ref('')
const newPasswordConfirm = ref('')
const showNewPassword = ref(false)
const showNewPasswordConfirm = ref(false)
const changingPassword = ref(false)
const passwordMessage = ref('')
const passwordSuccess = ref(false)

const rules = {
  required: (v: string) => !!v?.trim() || '入力必須です',
  email: (v: string) => {
    if (!v?.trim()) return true
    return /.+@.+\..+/.test(v) || '有効なメールアドレスを入力してください'
  },
  minLength: (v: string) => (v?.length ?? 0) >= 6 || '6文字以上で入力してください',
  passwordMatch: (v: string) => v === newPassword.value || 'パスワードが一致しません',
}

watch(
  () => user.value,
  (u) => {
    if (u) {
      editName.value = u.name ?? ''
      editEmail.value = u.email ?? ''
    }
  },
  { immediate: true }
)

const handleUpdate = async () => {
  updateMessage.value = ''
  const valid = await formRef.value?.validate().then((r) => r.valid) ?? true
  if (!valid) return
  updating.value = true
  try {
    const result = await updateProfile({
      name: editName.value.trim() || undefined,
      email: editEmail.value.trim(),
    })
    updateSuccess.value = result.success
    updateMessage.value = result.success ? 'ユーザー情報を更新しました。' : (result.message ?? '更新に失敗しました。')
  } finally {
    updating.value = false
  }
}

const handlePasswordChange = async () => {
  passwordMessage.value = ''
  const valid = await passwordFormRef.value?.validate().then((r) => r.valid) ?? true
  if (!valid) return
  if (newPassword.value !== newPasswordConfirm.value) {
    passwordMessage.value = '新しいパスワードが一致しません。'
    passwordSuccess.value = false
    return
  }
  if (newPassword.value.length < 6) {
    passwordMessage.value = '新しいパスワードは6文字以上で入力してください。'
    passwordSuccess.value = false
    return
  }
  changingPassword.value = true
  try {
    const result = await changePassword(newPassword.value)
    passwordSuccess.value = result.success
    passwordMessage.value = result.success ? (result.message ?? 'パスワードを変更しました。') : (result.message ?? '変更に失敗しました。')
    if (result.success) {
      newPassword.value = ''
      newPasswordConfirm.value = ''
    }
  } finally {
    changingPassword.value = false
  }
}

const handleLogout = async () => {
  await logout()
  router.push('/')
}

onMounted(() => {
  if (user.value) {
    editName.value = user.value.name ?? ''
    editEmail.value = user.value.email ?? ''
  }
})
</script>
