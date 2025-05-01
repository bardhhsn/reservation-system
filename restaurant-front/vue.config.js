const webpack = require('webpack')

process.env.__VUE_PROD_HYDRATION_MISMATCH_DETAILS__ = true

module.exports = {
  configureWebpack: {
    plugins: [
      new webpack.DefinePlugin({
        __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: JSON.stringify(true)
      })
    ]
  },
  devServer: {
    proxy: 'http://localhost:8000'
  }
}
