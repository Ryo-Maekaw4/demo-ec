<!--
  index.vue … トップページ（URLが / のとき表示）

  URL が / のとき、router が「この index.vue を表示する」と決めている。
  <DemoEc /> は「DemoEc という部品をここに置く」という意味。コンポーネントは再利用できる画面の部品。
  親（index.vue）が子（DemoEc.vue）を1つ表示しているだけの、シンプルなページ。
-->
<template>
  <!-- DemoEc コンポーネントを1つ表示。コンポーネント = 再利用できる画面の部品 -->
  <DemoEc />
</template>

<script lang="ts" setup>
</script>
