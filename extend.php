<?php

use Flarum\Api\Serializer\ForumSerializer;
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
    (new Extend\Settings())
        ->default('momokoudai-flarum-ext-i18n-settings.filterPattern', '$$$')
        ->default('momokoudai-flarum-ext-i18n-settings.enabledPlugins', ''),
    (new Extend\Locales(__DIR__ . '/locale'))
];
