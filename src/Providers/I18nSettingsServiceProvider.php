<?php

namespace Momokoudai\FlarumExtI18nSettings\Providers;

use Flarum\Foundation\AbstractServiceProvider;
use Momokoudai\FlarumExtI18nSettings\Listeners\ContentFilter;
use Exception;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\Locale\Translator;

class I18nSettingsServiceProvider extends AbstractServiceProvider
{
    public function register()
    {
        // 尝试解析依赖项
        try {
            $settings = $this->container->make(SettingsRepositoryInterface::class);
            $translator = $this->container->make(Translator::class);
            
            // 注册 ContentFilter 类
            $this->container->singleton(ContentFilter::class, function ($container) {
                return new ContentFilter(
                    $container->make(SettingsRepositoryInterface::class),
                    $container->make(Translator::class)
                );
            });
        } catch (Exception $e) {
            error_log(
                '[flarum-ext-i18n-settings] ' . $e->getMessage() .
                ' in ' . $e->getFile() .
                ':' . $e->getLine()
            );
        }
    }
    
    public function boot()
    {
        // 获取事件调度器
        $events = $this->container->make('events');

        // Flarum 核心 settings 已通过 `Extend\\ApiSerializer(ForumSerializer::class)` 定点处理。

        // 某些扩展（如 fof/links、tags）的前台显示依赖模型取出后的字段值，
        // 因此保留 retrieved 过滤，但严格跳过 admin 请求。
        $events->listen('eloquent.retrieved: FoF\Links\Link', function ($link) {
            try {
                $filter = $this->container->make(ContentFilter::class);

                if (!$filter->shouldFilterCurrentRequest() || !$filter->isPluginEnabled('fof-links')) {
                    return;
                }

                if (isset($link->title)) {
                    $link->title = $filter->filterContent($link->title);
                }
                if (isset($link->url)) {
                    $link->url = $filter->filterContent($link->url);
                }
            } catch (Exception $e) {
                error_log(
                    '[flarum-ext-i18n-settings] ' . $e->getMessage() .
                    ' in ' . $e->getFile() .
                    ':' . $e->getLine()
                );
            }
        });

        $events->listen('eloquent.retrieved: Flarum\Tags\Tag', function ($tag) {
            try {
                $filter = $this->container->make(ContentFilter::class);

                if (!$filter->shouldFilterCurrentRequest() || !$filter->isPluginEnabled('flarum-tags')) {
                    return;
                }

                if (isset($tag->name)) {
                    $tag->name = $filter->filterContent($tag->name);
                }
                if (isset($tag->description)) {
                    $tag->description = $filter->filterContent($tag->description);
                }
            } catch (Exception $e) {
                error_log(
                    '[flarum-ext-i18n-settings] ' . $e->getMessage() .
                    ' in ' . $e->getFile() .
                    ':' . $e->getLine()
                );
            }
        });
    }
}