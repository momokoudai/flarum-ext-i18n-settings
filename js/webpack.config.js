const config = require("flarum-webpack-config")();

config.entry = {
  admin: "./src/admin/index.ts",
};

// 确保使用正确的模块导出
config.output.library = {
  type: "assign",
  name: "module.exports",
};

module.exports = config;
