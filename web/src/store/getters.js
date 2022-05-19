const getters = {
  sidebar: (state) => state.app.sidebar,
  device: (state) => state.app.device,
  token: (state) => state.user.token,
  avatar: (state) => state.user.avatar,
  name: (state) => state.user.name,
  userId: (state) => state.user.userId,
  roleId: (state) => state.user.roleId,
  permissionRoutes: (state) => state.permission.routes,
  s2c: (state) => state.msg.s2c,
  newCustomer: (state) => state.msg.newCustomer,
  unreadMsg: (state) => state.msg.unreadMsg,
  newMsg: (state) => state.msg.newMsg,
  customerOut: (state) => state.msg.customerOut
}
export default getters
