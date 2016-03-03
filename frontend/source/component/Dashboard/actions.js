import store from '/application/store.js'

const { dispatch } = store


export const setNetworkIndicator = (isVisible) => {
  dispatch({
    type: 'dashboard.network',
    data: isVisible,
  })
}