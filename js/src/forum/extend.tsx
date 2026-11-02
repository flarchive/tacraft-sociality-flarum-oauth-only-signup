import app from 'flarum/forum/app';
import { extend as extendMethod } from 'flarum/common/extend';
import type ItemList from 'flarum/common/utils/ItemList';
import type Mithril from 'mithril';

/**
 * Text shown at the bottom of the login modal, configurable from the admin
 * panel (`OAuth Only Signup` → 登录页提示). Empty string hides it.
 */
const HINT_ATTRIBUTE = 'oauthOnlySignupHint';

const oauthOnlySignupExtender = {
  extend() {
    // Appended after the submit button, so it sits at the very bottom of the
    // login form without touching the identification/password fields.
    extendMethod('flarum/forum/components/LogInModal', 'fields', function (this: unknown, items: ItemList<Mithril.Children>) {
      const hint = app.forum.attribute<string>(HINT_ATTRIBUTE);

      if (!hint || hint.trim() === '') {
        return;
      }

      items.add(
        'oauthOnlySignupHint',
        <p className="helpText" style={{ marginTop: '12px', marginBottom: '0' }}>
          {hint}
        </p>,
        -20
      );
    });
  },
};

export default [oauthOnlySignupExtender];
