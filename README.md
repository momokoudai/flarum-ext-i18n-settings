# Flarum Extension i18n Settings

> Add multilingual display support to selected Flarum core settings and extension fields that normally only support a single language input.

---

## Features

- ✅ Frontend-only replacement, admin values remain editable in raw form
- ✅ Supports Flarum core basics settings
- ✅ Supports selected third-party extensions
- ✅ Supports JSON format and simple `中文|English` format
- ✅ Explicit target control via `Enabled Plugins`
- ✅ Empty target list disables replacement by default

## Supported targets

| Target        | Scope                        | Fields                                                                                                   |
| ------------- | ---------------------------- | -------------------------------------------------------------------------------------------------------- |
| `flarum-core` | Flarum core `/admin#/basics` | `forum_title`, `forum_description`, `welcome_title`, `welcome_message`, `custom_header`, `custom_footer` |
| `fof-links`   | `fof/links`                  | `title`, `url`                                                                                           |
| `flarum-tags` | `flarum/tags`                | `name`, `description`                                                                                    |

> Only targets listed in `Enabled Plugins` will be replaced.

## Installation

```bash
composer require momokoudai/flarum-ext-i18n-settings
php flarum cache:clear
```

## Configuration

Open the extension settings in the Flarum admin panel.

### 1. Filter Pattern

Default:

```text
$$$
```

This marker identifies content that should be processed for multilingual replacement.

### 2. Enabled Plugins

Examples:

```text
flarum-core
```

```text
flarum-core, fof-links, flarum-tags
```

If left empty, **replacement is disabled by default**.

## Usage

### Recommended JSON format

```text
$$${"zh-Hans":"简体中文","en":"English","ja":"日本語"}$$$
```

### Simple format

```text
$$$中文|English$$$
```

## Examples

### Forum title

```text
$$${"zh-Hans":"我的社区","en":"My Community"}$$$
```

### Welcome banner

```text
$$${"zh-Hans":"欢迎来到社区","en":"Welcome to the community"}$$$
```

### Custom header / footer with HTML

```text
$$${"zh-Hans":"<div class='notice'>中文公告</div>","en":"<div class='notice'>English Notice</div>"}$$$
```

> For HTML content, prefer single quotes inside attributes to keep JSON easier to write.

## Behavior and safety

- Replacement runs only on the **forum frontend**
- Admin forms keep the original marker content for editing
- User-generated discussions, posts, and replies are **not** replaced
- Unsupported extensions are not modified unless explicit compatibility is added

## Compatibility

- Flarum `^1.8`

## Notes

This extension is designed as a **targeted compatibility layer**, not a site-wide text replacement engine.

If you need support for another extension with admin-managed text fields, it can be added through a dedicated adapter.

## License

MIT

# Flarum Extension i18n Settings / Flarum 后台设置多语言支持

> Add multilingual display support to selected Flarum core settings and extension fields that normally only support a single language input.

> 为部分 **后台可填写、但原生不支持多语言输入** 的 Flarum 核心设置和扩展字段提供前台多语言显示支持。

---

## English

### Features

- ✅ Frontend-only replacement, admin values remain editable in raw form
- ✅ Supports Flarum core basics settings
- ✅ Supports selected third-party extensions
- ✅ Supports JSON format and legacy `中文|English` format
- ✅ Explicit target control via `Enabled Plugins`
- ✅ Empty target list disables replacement by default for better stability

### Supported targets

| Target        | Scope                        | Fields                                                                                                   |
| ------------- | ---------------------------- | -------------------------------------------------------------------------------------------------------- |
| `flarum-core` | Flarum core `/admin#/basics` | `forum_title`, `forum_description`, `welcome_title`, `welcome_message`, `custom_header`, `custom_footer` |
| `fof-links`   | `fof/links`                  | `title`, `url`                                                                                           |
| `flarum-tags` | `flarum/tags`                | `name`, `description`                                                                                    |

> Only targets listed in `Enabled Plugins` will be replaced.

### Installation

```bash
composer require momokoudai/flarum-ext-i18n-settings
php flarum cache:clear
```

### Configuration

Open the extension settings in the Flarum admin panel.

#### 1. Filter Pattern

Default:

```text
$$$
```

This marker identifies content that should be processed for multilingual replacement.

#### 2. Enabled Plugins

Examples:

```text
flarum-core
```

```text
flarum-core, fof-links, flarum-tags
```

If left empty, **replacement is disabled by default**.

### Usage

#### Recommended JSON format

```text
$$${"zh-Hans":"简体中文","en":"English","ja":"日本語"}$$$
```

#### Legacy format

```text
$$$中文|English$$$
```

### Examples

#### Forum title

```text
$$${"zh-Hans":"我的社区","en":"My Community"}$$$
```

#### Welcome banner

```text
$$${"zh-Hans":"欢迎来到社区","en":"Welcome to the community"}$$$
```

#### Custom header / footer with HTML

```text
$$${"zh-Hans":"<div class='notice'>中文公告</div>","en":"<div class='notice'>English Notice</div>"}$$$
```

> For HTML content, prefer single quotes inside attributes to keep JSON easier to write.

### Behavior and safety

- Replacement runs only on the **forum frontend**
- Admin forms keep the original marker content for editing
- User-generated discussions, posts, and replies are **not** replaced
- Unsupported extensions are not modified unless explicit compatibility is added

### Compatibility

- Flarum `^1.8`

### Notes

This extension is designed as a **targeted compatibility layer**, not a site-wide text replacement engine.

If you need support for another extension with admin-managed text fields, it can be added through a dedicated adapter.

---

## 中文说明

### 功能特点

- ✅ 仅在前台进行替换，后台仍保留原始标记内容，方便编辑
- ✅ 支持 Flarum 核心基础设置
- ✅ 支持部分第三方扩展字段
- ✅ 支持 JSON 格式和旧版 `中文|English` 格式
- ✅ 可通过 `Enabled Plugins` 精确控制生效目标
- ✅ 为提高稳定性，目标列表留空时默认不启用替换

### 已支持目标

| 目标          | 范围                         | 字段                                                                                                     |
| ------------- | ---------------------------- | -------------------------------------------------------------------------------------------------------- |
| `flarum-core` | Flarum 核心 `/admin#/basics` | `forum_title`、`forum_description`、`welcome_title`、`welcome_message`、`custom_header`、`custom_footer` |
| `fof-links`   | `fof/links`                  | `title`、`url`                                                                                           |
| `flarum-tags` | `flarum/tags`                | `name`、`description`                                                                                    |

> 只有在 `Enabled Plugins` 中明确填写的目标才会被替换。

### 安装

```bash
composer require momokoudai/flarum-ext-i18n-settings
php flarum cache:clear
```

### 配置

在 Flarum 后台扩展设置中进行配置。

#### 1. 过滤标记（Filter Pattern）

默认值：

```text
$$$
```

该标记用于识别需要进行多语言替换的内容。

#### 2. 启用的目标（Enabled Plugins）

示例：

```text
flarum-core
```

```text
flarum-core, fof-links, flarum-tags
```

如果留空，**默认不启用替换**。

### 使用方式

#### 推荐 JSON 格式

```text
$$${"zh-Hans":"简体中文","en":"English","ja":"日本語"}$$$
```

#### 旧版兼容格式

```text
$$$中文|English$$$
```

### 示例

#### 论坛标题

```text
$$${"zh-Hans":"我的社区","en":"My Community"}$$$
```

#### Welcome Banner

```text
$$${"zh-Hans":"欢迎来到社区","en":"Welcome to the community"}$$$
```

#### 带 HTML 的 custom header / footer

```text
$$${"zh-Hans":"<div class='notice'>中文公告</div>","en":"<div class='notice'>English Notice</div>"}$$$
```

> 对于 HTML 内容，建议属性值里优先使用单引号，便于书写 JSON。

### 行为与安全性

- 只在**论坛前台**进行替换
- 后台表单仍显示原始标记内容，方便继续编辑
- 不会替换用户发布的主题、帖子和回复
- 未适配的扩展不会被自动修改，除非后续添加专门兼容代码

### 兼容性

- Flarum `^1.8`

### 说明

本扩展的定位是 **“定点兼容层”**，而不是全站字符串自动翻译引擎。

如果你还希望支持其它带后台文本设置的扩展，可以继续按适配器方式扩展。

---

## License

MIT
