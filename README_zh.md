[English Document](README.md)

# Flarum 扩展 i18n Settings

> 为部分 **后台可填写、但原生不支持多语言输入** 的 Flarum 核心设置和扩展字段提供前台多语言显示支持。
> 如 fof/links 扩展 的 `title` 和 `url` 字段。后台支持设置标题/链接，但是只能设置一个语言，不能支持多语言输入。本扩展提供了前台多语言显示支持。通过输入特定标记，来支持输入多语言，前台根据用户当前语言进行显示。

---

## 功能特点

- ✅ 仅在前台进行替换，后台仍保留原始标记内容，方便编辑
- ✅ 支持 Flarum 核心基础设置
- ✅ 支持部分第三方扩展字段
- ✅ 支持 JSON 格式和简易 `中文|English` 格式
- ✅ 可通过 `Enabled Plugins` 精确控制生效目标
- ✅ 目标列表留空时默认不启用替换

## 已支持目标

| 目标          | 范围                         | 字段                                                                                                     |
| ------------- | ---------------------------- | -------------------------------------------------------------------------------------------------------- |
| `flarum-core` | Flarum 核心 `/admin#/basics` | `forum_title`、`forum_description`、`welcome_title`、`welcome_message`、`custom_header`、`custom_footer` |
| `fof-links`   | `fof/links`                  | `title`、`url`                                                                                           |
| `flarum-tags` | `flarum/tags`                | `name`、`description`                                                                                    |

> 只有在 `Enabled Plugins` 中明确填写的目标才会被替换。

## 安装

```bash
composer require momokoudai/flarum-ext-i18n-settings
php flarum cache:clear
```

## 配置

在 Flarum 后台扩展设置中进行配置。

### 1. 过滤标记（Filter Pattern）

默认值：

```text
$$$
```

该标记用于识别需要进行多语言替换的内容。

### 2. 启用的目标（Enabled Plugins）

示例：

```text
flarum-core
```

```text
flarum-core, fof-links, flarum-tags
```

如果留空，**默认不启用替换**。

## 使用方式

### 推荐 JSON 格式

```text
$$${"zh-Hans":"简体中文","en":"English","ja":"日本語"}$$$
```

### 简易格式

```text
$$$中文|English$$$
```

## 示例

### 论坛标题

```text
$$${"zh-Hans":"我的社区","en":"My Community"}$$$
```

### Welcome Banner

```text
$$${"zh-Hans":"欢迎来到社区","en":"Welcome to the community"}$$$
```

### 带 HTML 的 custom header / footer

```text
$$${"zh-Hans":"<div class='notice'>中文公告</div>","en":"<div class='notice'>English Notice</div>"}$$$
```

> 对于 HTML 内容，建议属性值里优先使用单引号，便于书写 JSON。

## 行为与安全性

- 只在**论坛前台**进行替换
- 后台表单仍显示原始标记内容，方便继续编辑
- 不会替换用户发布的主题、帖子和回复
- 未适配的扩展不会被自动修改，除非后续添加专门兼容代码

## 兼容性

- Flarum `^1.8`

## 说明

本扩展的定位是 **"定点兼容层"**，而不是全站字符串自动翻译引擎。

如果你还希望支持其它带后台文本设置的扩展，可以继续按适配器方式扩展。

## 许可证

MIT
