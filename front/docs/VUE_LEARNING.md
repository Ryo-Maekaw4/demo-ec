# このECサイトで学べる Vue の基礎（中学生向け）

このフォルダのECサイトでは、Vue.js の「基礎的・特徴的な機能」がどこでどう使われているかを、**中学生でもわかる言葉**で説明します。  
各 .vue ファイルのコメントと、このドキュメントの用語説明で、ref・双方向・バインドなどを学べます。

---

## 用語の意味（やさしい説明）— ref・双方向・バインド など

### ref（リフ）
- **「値を入れておく箱」** だと思ってください。
- その箱の中身が変わると、**画面の表示も自動で変わります**（リアクティブ）。
- 中身を読む・書くときは **`.value`** を使います。例：`count.value = 1`
- テンプレート（HTML 部分）では `email` と書くだけで中身が表示され、`.value` は不要です。

### 双方向バインディング（v-model）
- **「入力欄」と「ref の箱」が、お互いに同じ内容になる** 仕組みです。
- ユーザーが入力すると箱の中身が変わり、プログラムで箱を変えると入力欄も変わります。
- フォームの「メール」「パスワード」や、検索窓で使っています。

### バインド（v-bind / :）
- **「HTML の属性に、JavaScript の値をつなぐ」** ことです。
- コロン `:` は `v-bind` の省略。例：`:src="product.image_url"` で、画像の URL を変数から渡しています。
- 「バインド」＝「結びつける」という意味です。

### ディレクティブ（v-if, v-for など）
- **「この要素をどう表示するか」を Vue に指示する** 特別な属性です。`v-` で始まります。
- **v-if / v-else**：条件が true のときだけ表示する。
- **v-for**：配列の要素の数だけ、同じ形の要素を繰り返し表示する。
- **v-model**：入力と ref を双方向でつなぐ（上で説明したもの）。

### イベント（@click など）
- **「ユーザーがしたこと（クリック・Enter など）が起きたときに、どの関数を実行するか」** を指定します。
- `@` は `v-on` の省略。例：`@click="handleSearch"` で、クリックしたら `handleSearch` が動きます。

### ルーティング（router）
- **「URL のパス」と「表示するページ（コンポーネント）」を対応させる** 機能です。
- `/login` ならログイン画面、`/product/1` なら 1 番の商品詳細、のように切り替わります。
- `router.push('/パス')` で、プログラムから別のページへ移動できます。

### ライフサイクル（onMounted, watch）
- **「コンポーネントが表示された直後」** にやりたいことを書くのが **onMounted**。
- **「ある値が変わったとき」** にやりたいことを書くのが **watch**。

### Composition API（script setup）
- **「ロジックを ref と関数でまとめて書く」** 書き方です。
- `script setup` で、`ref()` や `onMounted()` をそのまま使っています。

---

## このサイトのどこで使っているか

| 機能 | このサイトでの例（ファイル） |
|------|------------------------------|
| データバインディング | `{{ product.name }}`（商品名表示）、`v-model="searchKeyword"`（検索窓）→ search_list.vue, login.vue |
| ディレクティブ | `v-if` / `v-else`（見出し・在庫表示）、`v-for`（商品リスト）、`:key`、`:src` → search_list.vue, Header.vue, product/[id].vue |
| イベント | `@click`（ボタン・カード）、`@submit.prevent`（ログインボタン）、`@keyup.enter`（検索）→ login.vue, search_list.vue |
| ルーティング | `router-view`（App.vue）、`router.push`（ページ遷移）、`/product/:id`（商品詳細）→ App.vue, 各ページ |
| Composition API | `ref()`, `script setup`, `.value` → 全 .vue ファイル |
| watch | `watch(() => route.params.id, ...)`（商品IDが変わったら再取得）→ product/[id].vue |
| onMounted | 表示後にAPI呼び出し → search_list.vue, product/[id].vue, App.vue |

---

## ファイルごとの「どこで何を学べるか」

- **App.vue** … ルーティング（router-view）、ライフサイクル（onMounted）
- **login.vue** … ref、双方向バインディング（v-model）、イベント（@submit.prevent）、ディレクティブ（v-if）
- **search_list.vue** … v-if / v-else、v-for、:key、v-model、onMounted、router、API 呼び出し
- **product/[id].vue** … ref、v-if / v-else-if / v-else、onMounted、watch、:src、:to
- **Header.vue** … ディレクティブ（v-if / v-else）、v-for、:key、@click、router.push、template でブロック分岐
- **DemoEc.vue** … v-for、:key、:src、@click、ref、onMounted、API

各 .vue ファイルの先頭や該当行には、**Vue の機能の説明コメント**を入れています。  
「**ref とは？**」「**双方向って？**」「**バインドって？**」というときは、このドキュメントの「用語の意味」と、該当ファイルのコメントを一緒に読むとわかりやすいです。
