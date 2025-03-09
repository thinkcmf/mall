<?php

namespace app\admin\service;

class SettingService
{
    /**
     * 获取多语言配置
     *
     * @return array|int[]
     */
    public function getLangSetting(): array
    {
        $langSetting = cmf_get_option('lang_setting');
        if (empty($langSetting)) {
            $app = app();
            $langConfig = $app->lang->getConfig();
            $defaultLang = $app->lang->defaultLangSet();
            $adminDefaultLang = empty($langConfig['admin_default_lang']) ? 'zh-cn' : $langConfig['admin_default_lang'];
            $langSetting = [
                'multi_lang_mode' => 1,
                'home_multi_lang' => 0,
                'default_lang' => $defaultLang,
                'allow_lang_list' => [[
                    'lang' => $defaultLang,
                    'alias' => '',
                    'domain' => '',
                ]],
                'admin_multi_lang' => 0,
                'admin_default_lang' => $adminDefaultLang,
                'admin_allow_lang_list' => [[
                    'lang' => $adminDefaultLang,
                ]]
            ];


        }
        return $langSetting;
    }

}