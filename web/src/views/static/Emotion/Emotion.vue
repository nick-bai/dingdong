<template>
  <div class="ly-emotion">
    <slot />
  </div>
</template>

<script>
var emoji = require('emoji.json')
export default {
  name: 'LyEmotion',
  mounted() {
    const list = emoji.slice(0, 55)
    console.log(list, 'emoji')
  }
}
</script>
<style scoped>
.ly-emotion {
  display: inline-block;
}
.ly-static-emotion {
  width: 24px;
  height: 24px;
  display: inline-block;
}
</style>
