import app from "flarum/admin/app";

app.initializers.add("momokoudai/flarum-ext-i18n-settings", () => {
  try {
    const extensionId = "momokoudai-i18n-settings";

    app.extensionData
      .for(extensionId)
      .registerSetting({
        setting: "momokoudai-flarum-ext-i18n-settings.filterPattern",
        type: "text",
        label: app.translator.trans(
          "momokoudai-i18n-settings.admin.settings.filter_pattern_label",
        ),
        help: app.translator.trans(
          "momokoudai-i18n-settings.admin.settings.filter_pattern_help",
        ),
        placeholder: "$$$",
      })
      .registerSetting({
        setting: "momokoudai-flarum-ext-i18n-settings.enabledPlugins",
        type: "text",
        label: app.translator.trans(
          "momokoudai-i18n-settings.admin.settings.enabled_plugins_label",
        ),
        help: app.translator.trans(
          "momokoudai-i18n-settings.admin.settings.enabled_plugins_help",
        ),
        placeholder: app.translator.trans(
          "momokoudai-i18n-settings.admin.settings.enabled_plugins_placeholder",
        ),
      });
  } catch (error) {
    console.error("Error registering admin settings:", error);
  }
});
