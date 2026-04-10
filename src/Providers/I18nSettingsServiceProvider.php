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
        // 移除所有eloquent.retrieved事件监听，统一使用API序列化器进行处理
        // 这样可以避免在数据操作过程中修改原始数据
    }
}