// vue.config.js
process.env.__VUE_PROD_HYDRATION_MISMATCH_DETAILS__ = true

module.exports = {
  configureWebpack: {
    define: {
      __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: 'true'
    }
  },
  devServer: {
    proxy: 'http://localhost:8000'
  }
}
