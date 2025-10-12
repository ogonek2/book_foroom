const mix = require('laravel-mix');
const path = require('path');

mix.js('resources/js/app.js', 'public/js')
   .vue()
   .sass('resources/css/app.scss', 'public/css')
   .options({
      processCssUrls: false,
      postCss: [
         require('tailwindcss'),
         require('autoprefixer'),
      ],
   })
   .webpackConfig({
      resolve: {
         extensions: ['.js', '.vue', '.json'],
         alias: {
            '@': path.resolve(__dirname, 'resources/js'),
         },
      },
   })
   .version()
   .sourceMaps();

if (mix.inProduction()) {
   mix.version();
}