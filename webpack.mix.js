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
      module: {
         rules: [
            {
               test: /\.s[ac]ss$/i,
               use: [
                  {
                     loader: 'sass-loader',
                     options: {
                        sassOptions: {
                           silenceDeprecations: ['legacy-js-api'],
                        },
                     },
                  },
               ],
            },
         ],
      },
   })
   .version()
   .sourceMaps();

if (mix.inProduction()) {
   mix.version();
}