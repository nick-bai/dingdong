export default {
  state: {
    unreadMsg: 0,
    s2c: '',
    kefuOut: {},
    newCustomer: {},
    newMsg: '',
    customerOut: ''
  },
  mutations: {
    SET_UNREAD_MSG(state, key) {
      state.unreadMsg = key
    },
    SET_S2C(state, key) {
      state.s2c = key
    },
    SET_KEFU_OUT(state, key) {
      state.kefuOut = key
    },
    SET_NEW_CUSTOMER(state, key) {
      state.newCustomer = key
    },
    SET_NEW_MSG(state, key) {
      state.newMsg = key
    },
    SET_CUSTOMER_OUT(state, key) {
      state.customerOut = key
    }
  }
}
