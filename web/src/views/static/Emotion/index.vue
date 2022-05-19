<template>
  <div>
    <div class="emotion-box" :style="{ height: height + 'px' }">
      <div
        v-for="(line, i) in list"
        :key="i"
        class="emotion-box-line"
        @click.enter="clickHandler(line.char)"
      >
        {{ line.char }}
        <!-- <emotion
          class="emotion-item"
          v-for="(item, i) in line"
          :key="i"
          @click.enter="clickHandler(item)"
          >{{ item }}</emotion
        > -->
      </div>
    </div>
  </div>
</template>

<script>
var emoji = require('emoji.json')
// import Emotion from "./Emotion";
export default {
  props: {
    height: {
      type: Number,
      default: 200
    }
  },
  data() {
    return {
      list: []
    }
  },
  created() {
    this.list = emoji.slice(0, 50)
    this.list.push(emoji[55])
    this.list.push(emoji[56])
    this.list.push(emoji[57])
    this.list.push(emoji[60])
  },
  methods: {
    clickHandler(i) {
      //   let emotion = `#${i};`;
      console.log(i)
      this.$emit('emotion', i)
    }
  }
  //   components: {
  //     Emotion,
  //   },
}
</script>
<style scoped>
.emotion-box {
  margin: 0 auto;
  width: 250px;
  box-sizing: border-box;
  padding: 5px;
  border: 1px solid #b4b4b4;
  overflow: hidden;
  overflow-y: auto;
  display: flex;
  flex-wrap: wrap;
}
.emotion-box-line {
  display: flex;
  font-size: 21px;
}
.emotion-item {
  flex: 1;
  text-align: center;
  cursor: pointer;
}
</style>
