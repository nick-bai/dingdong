<script>
export default {
  name: 'MenuItem',
  functional: true,
  props: {
    icon: {
      type: String,
      default: ''
    },
    title: {
      type: String,
      default: ''
    },
    unreadMsg: {
      type: Number,
      default: 0
    }
  },
  render(h, context) {
    const { icon, title, unreadMsg } = context.props
    const vnodes = []

    if (icon) {
      if (icon.includes('el-icon')) {
        vnodes.push(<i class={[icon, 'sub-el-icon']} />)
      } else {
        vnodes.push(<svg-icon icon-class={icon} />)
      }
    }

    if (title) {
      if (title === '对话' && unreadMsg !== 0) {
        vnodes.push(
          <span slot='title'>
            {title} <el-badge value={unreadMsg} class='item' />
          </span>
        )
      } else {
        vnodes.push(<span slot='title'>{title}</span>)
      }
    }
    return vnodes
  }
}
</script>

<style scoped>
.sub-el-icon {
  color: currentColor;
  width: 1em;
  height: 1em;
}
</style>
