<?php

use Flarum\Api\Serializer\ForumSerializer;
use Flarum\Tags\Api\Serializer\TagSerializer;
use FoF\Links\Api\Serializer\LinkSerializer;
// use Flarum\Api\Serializer\DiscussionSerializer;
use Flarum\Extend;
use Momokoudai\FlarumExtI18nSettings\Listeners\ContentFilter;
use Momokoudai\FlarumExtI18nSettings\Providers\I18nSettingsServiceProvider;

return [
    (new Extend\Frontend('admin'))
        ->js(__DIR__ . '/js/dist/admin.js', 'momokoudai-flarum-ext-i18n-settings'),
    (new Extend\ServiceProvider())
        ->register(I18nSettingsServiceProvider::class),
    (new Extend\ApiSerializer(ForumSerializer::class))
        ->attributes(function (ForumSerializer $serializer, $model, array $attributes) {
            try {
                return resolve(ContentFilter::class)->filterForumAttributes($attributes);
            } catch (\Throwable $e) {
                return $attributes;
            }
        }),
    (new Extend\ApiSerializer(TagSerializer::class))
        ->attributes(function (TagSerializer $serializer, $model, array $attributes) {
            try {
                $filter = resolve(ContentFilter::class);
                
                // 只在前台请求且启用了flarum-tags插件时进行过滤
                if ($filter->shouldFilterCurrentRequest() && $filter->isPluginEnabled('flarum-tags')) {
                    if (isset($attributes['name'])) {
                        $attributes['name'] = $filter->filterContent($attributes['name']);
                    }
                    if (isset($attributes['description'])) {
                        $attributes['description'] = $filter->filterContent($attributes['description']);
                    }
                }
                
                return $attributes;
            } catch (\Throwable $e) {
                return $attributes;
            }
        }),
    (new Extend\ApiSerializer(LinkSerializer::class))
        ->attributes(function (LinkSerializer $serializer, $model, array $attributes) {
            try {
                $filter = resolve(ContentFilter::class);
                
                // 只在前台请求且启用了fof-links插件时进行过滤
                if ($filter->shouldFilterCurrentRequest() && $filter->isPluginEnabled('fof-links')) {
                    if (isset($attributes['title'])) {
                        $attributes['title'] = $filter->filterContent($attributes['title']);
                    }
                    if (isset($attributes['url'])) {
                        $attributes['url'] = $filter->filterContent($attributes['url']);
                    }
                }
                
                return $attributes;
            } catch (\Throwable $e) {
                return $attributes;
            }
        }),
    (new Extend\ApiSerializer(DiscussionSerializer::class))
        ->attributes(function (DiscussionSerializer $serializer, $model, array $attributes) {
            try {
                $filter = resolve(ContentFilter::class);
                    error_log('DiscussionSerializer 执行了');
                // 检查是否为前台请求和插件是否启用
                $shouldFilter = $filter->shouldFilterCurrentRequest();
                $isEnabled = $filter->isPluginEnabled('flarum-tags');
                
                // 如果需要过滤且启用了标签插件，则检查关联的标签
                if ($shouldFilter && $isEnabled) {
                    // 如果有关联的included数据（如tags），我们也需要处理它们
                    if (isset($attributes['included'])) {
                        foreach ($attributes['included'] as &$item) {
                            if ($item['type'] === 'tags' && isset($item['attributes']['name'])) {
                                $item['attributes']['name'] = $filter->filterContent($item['attributes']['name']);
                            }
                            
                            if ($item['type'] === 'tags' && isset($item['attributes']['description'])) {
                                $item['attributes']['description'] = $filter->filterContent($item['attributes']['description']);
                            }
                        }
                    }
                }
                
                return $attributes;
            } catch (\Throwable $e) {
                return $attributes;
            }
        }),
    (new Extend\Settings())
        ->default('momokoudai-flarum-ext-i18n-settings.filterPattern', '$$$')
        ->default('momokoudai-flarum-ext-i18n-settings.enabledPlugins', ''),
    (new Extend\Locales(__DIR__ . '/locale'))
];