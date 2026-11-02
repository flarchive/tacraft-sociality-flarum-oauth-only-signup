import app from 'flarum/admin/app';

const EXTENSION_ID = 'tacraft-sociality-oauth-only-signup';

app.initializers.add(EXTENSION_ID, () => {
  app.registry.for(EXTENSION_ID).registerSetting({
    setting: `${EXTENSION_ID}.hint`,
    type: 'textarea',
    label: app.translator.trans(`${EXTENSION_ID}.admin.hint_label`),
    help: app.translator.trans(`${EXTENSION_ID}.admin.hint_help`),
    placeholder: '本站已关闭邮箱注册，请使用下方第三方账号登录；已有账号可以直接用邮箱和密码登录。',
  });
});
