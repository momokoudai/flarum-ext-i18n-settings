<?php

namespace Momokoudai\FlarumExtI18nSettings\Listeners;

use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\Locale\Translator;
use Exception;

class ContentFilter
{
    private const CORE_FIELDS = ['title', 'description', 'welcomeTitle', 'welcomeMessage', 'headerHtml', 'footerHtml'];

    /** @var SettingsRepositoryInterface */
    protected $settings;

    /** @var Translator */
    protected $translator;

    /** @var array<string>|null */
    protected $enabledPluginsCache = null;

    /** @var string|null */
    protected $filterPatternCache = null;

    /** @var string|null */
    protected $localeCache = null;

    public function __construct(SettingsRepositoryInterface $settings, Translator $translator)
    {
        $this->settings = $settings;
        $this->translator = $translator;
    }



    public function filterForumAttributes(array $attributes)
    {
        if (!$this->shouldFilterCurrentRequest() || !$this->isPluginEnabled('flarum-core') || !$this->looksLikeForumBasicsAttributes($attributes)) {
            return $attributes;
        }

        foreach (self::CORE_FIELDS as $key) {
            if (array_key_exists($key, $attributes) && is_string($attributes[$key])) {
                $attributes[$key] = $this->filterContent($attributes[$key]);
            }
        }

        return $attributes;
    }

    protected function looksLikeForumBasicsAttributes(array $attributes)
    {
        return array_key_exists('baseUrl', $attributes)
            || array_key_exists('showLanguageSelector', $attributes)
            || array_key_exists('welcomeTitle', $attributes)
            || array_key_exists('welcomeMessage', $attributes)
            || array_key_exists('headerHtml', $attributes)
            || array_key_exists('footerHtml', $attributes);
    }

    public function shouldFilterCurrentRequest(): bool
    {
        return !$this->isAdminRequest();
    }

    protected function isAdminRequest(): bool
    {
        $requestUri = (string) ($_SERVER['REQUEST_URI'] ?? '');

        if ($this->hasAdminPath($requestUri)) {
            return true;
        }

        // 避免从后台跳到前台时，因 Referer 仍带 /admin 而误判为后台请求。
        // 只有当前请求本身是 API，或 REQUEST_URI 不可用时，才使用 Referer/Origin 作为兜底判断。
        if ($requestUri !== '' && stripos($requestUri, '/api') === false) {
            return false;
        }

        foreach (['HTTP_REFERER', 'HTTP_ORIGIN'] as $key) {
            $value = (string) ($_SERVER[$key] ?? '');

            if ($this->hasAdminPath($value)) {
                return true;
            }
        }

        return false;
    }

    protected function hasAdminPath(string $value): bool
    {
        return $value !== '' && preg_match('~(?:^|/)admin(?:[/?#]|$)~i', $value) === 1;
    }

    public function isPluginEnabled($plugin): bool
    {
        if (!is_string($plugin) || $plugin === '') {
            return false;
        }

        if ($this->enabledPluginsCache === null) {
            $enabledPlugins = (string) $this->settings->get('momokoudai-flarum-ext-i18n-settings.enabledPlugins', '');
            $this->enabledPluginsCache = array_filter(array_map('trim', preg_split('/[\s,]+/', strtolower($enabledPlugins))));
        }

        if (empty($this->enabledPluginsCache)) {
            return false;
        }

        return in_array(strtolower($plugin), $this->enabledPluginsCache, true);
    }

    public function filterContent($content)
    {
        try {
            if (is_array($content)) {
                foreach ($content as $key => $value) {
                    $content[$key] = $this->filterContent($value);
                }
            } elseif (is_string($content)) {
                // 检查 settings 是否为 null
                if ($this->settings === null) {
                    return $content;
                }
                $filterPattern = $this->getFilterPattern();

                // 首先检查是否包含过滤标记，避免不必要的处理
                if ($filterPattern === '' || strpos($content, $filterPattern) === false) {
                    return $content;
                }

                // 获取当前语言
                $locale = $this->getCurrentLocale();
                $escapedPattern = preg_quote($filterPattern, '~');
                $pattern = "~{$escapedPattern}(.*?){$escapedPattern}~s";

                // 过滤内容
                $content = preg_replace_callback($pattern, function($matches) use ($locale) {
                    $jsonContent = $matches[1];
                    
                    // 尝试解析 JSON 格式
                    $decoded = json_decode($jsonContent, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        // JSON 格式：直接使用 Flarum 标准的语言标记
                        // 例如：{"zh-Hans":"简体中文","en":"English","ja":"日本語"}
                        
                        // 中文特殊支持：zh、zh-cn、zh-CN 都理解为 zh-Hans
                        $normalizedLocale = $this->normalizeLocale($locale);
                        
                        if (isset($decoded[$normalizedLocale])) {
                            return $decoded[$normalizedLocale];
                        }
                        
                        // 如果当前语言不存在，返回第一个可用的语言
                        $firstValue = reset($decoded);
                        return $firstValue ?: $matches[0];
                    } else {
                        // 兼容旧格式：中文|English
                        $parts = explode('|', $jsonContent);
                        if (strpos($locale, 'zh') === 0) {
                            $result = isset($parts[0]) ? $parts[0] : $matches[0];
                            return $result;
                        } else {
                            $result = isset($parts[1]) ? $parts[1] : (isset($parts[0]) ? $parts[0] : $matches[0]);
                            return $result;
                        }
                    }
                }, $content);
            }
        } catch (Exception $e) {
            // 捕获异常，避免插件崩溃
            error_log(
                '[flarum-ext-i18n-settings] ' . $e->getMessage() .
                ' in ' . $e->getFile() .
                ':' . $e->getLine()
            );
        }
        
        return $content;
    }
    
    protected function getFilterPattern(): string
    {
        if ($this->filterPatternCache !== null) {
            return $this->filterPatternCache;
        }

        try {
            $pattern = (string) $this->settings->get('momokoudai-flarum-ext-i18n-settings.filterPattern', '$$$');
            $this->filterPatternCache = $pattern !== '' ? $pattern : '$$$';
        } catch (Exception $e) {
            $this->filterPatternCache = '$$$';
        }

        return $this->filterPatternCache;
    }

    // 获取当前语言
    protected function getCurrentLocale(): string
    {
        if ($this->localeCache !== null) {
            return $this->localeCache;
        }

        try {
            // 直接使用注入的翻译器获取语言
            if (isset($this->translator) && method_exists($this->translator, 'getLocale')) {
                $this->localeCache = $this->translator->getLocale();
                return $this->localeCache;
            }
            
            // 如果翻译器不可用，使用默认语言
            $this->localeCache = $this->settings->get('default_locale', 'en');
            return $this->localeCache;
        } catch (Exception $e) {
            // 如果出现异常，使用默认语言
            $this->localeCache = 'en';
            return $this->localeCache;
        }
    }
    
    // 标准化语言标记，为中文提供特殊支持
    protected function normalizeLocale($locale): string
    {
        // 转换为小写进行比较
        $lowerLocale = strtolower((string) $locale);
        
        // 中文特殊支持：zh、zh-cn、zh-CN 都理解为 zh-Hans
        if ($lowerLocale === 'zh' || $lowerLocale === 'zh-cn' || $lowerLocale === 'zh_cn') {
            return 'zh-Hans';
        }
        
        // 繁体中文支持：zh-tw、zh-hk、zh_tw、zh_hk 都理解为 zh-Hant
        if ($lowerLocale === 'zh-tw' || $lowerLocale === 'zh-hk' || 
            $lowerLocale === 'zh_tw' || $lowerLocale === 'zh_hk') {
            return 'zh-Hant';
        }
        
        // 其他语言保持不变
        return $locale;
    }
}