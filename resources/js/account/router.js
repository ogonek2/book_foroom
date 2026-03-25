import VueRouter from 'vue-router';

import ProfileOverview from './pages/ProfileOverview.vue';
import ProfileLibrary from './pages/ProfileLibrary.vue';
import ProfileReviews from './pages/ProfileReviews.vue';
import ProfileDiscussions from './pages/ProfileDiscussions.vue';
import ProfileQuotes from './pages/ProfileQuotes.vue';
import ProfileCollections from './pages/ProfileCollections.vue';
import SettingsProfile from './pages/SettingsProfile.vue';
import SettingsDesign from './pages/SettingsDesign.vue';
import SettingsSecurity from './pages/SettingsSecurity.vue';

function getBootstrap() {
  return (window && window.__ACCOUNT_BOOTSTRAP__) || { viewer: null, profileUsername: null };
}

export function createAccountRouter() {
  const { profileUsername, viewer } = getBootstrap();
  const effectiveUsername = profileUsername || (viewer && viewer.username) || null;

  const base = '/account';

  return new VueRouter({
    mode: 'history',
    base,
    scrollBehavior(to, from, savedPosition) {
      if (savedPosition) return savedPosition;
      if (to.path !== from.path) return { x: 0, y: 0 };
      return null;
    },
    routes: [
      {
        path: '/',
        redirect: effectiveUsername ? `/u/${effectiveUsername}/overview` : '/u/guest/overview',
      },
      {
        path: '/u/:username',
        redirect: (to) => `/u/${to.params.username}/overview`,
      },
      { path: '/u/:username/overview', name: 'acc.profile.overview', component: ProfileOverview },
      { path: '/u/:username/library', name: 'acc.profile.library', component: ProfileLibrary },
      { path: '/u/:username/reviews', name: 'acc.profile.reviews', component: ProfileReviews },
      { path: '/u/:username/discussions', name: 'acc.profile.discussions', component: ProfileDiscussions },
      { path: '/u/:username/quotes', name: 'acc.profile.quotes', component: ProfileQuotes },
      { path: '/u/:username/collections', name: 'acc.profile.collections', component: ProfileCollections },

      { path: '/settings/profile', name: 'acc.settings.profile', component: SettingsProfile },
      { path: '/settings/design', name: 'acc.settings.design', component: SettingsDesign },
      { path: '/settings/security', name: 'acc.settings.security', component: SettingsSecurity },

      { path: '*', redirect: '/' },
    ],
  });
}

