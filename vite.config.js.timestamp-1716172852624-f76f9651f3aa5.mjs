// vite.config.js
import { defineConfig } from "file:///home/tereza/Codes/Composer/Laravel/KawanDesa/node_modules/.pnpm/vite@5.2.9/node_modules/vite/dist/node/index.js";
import laravel from "file:///home/tereza/Codes/Composer/Laravel/KawanDesa/node_modules/.pnpm/laravel-vite-plugin@1.0.1_vite@5.2.9/node_modules/laravel-vite-plugin/dist/index.js";
import { svelte } from "file:///home/tereza/Codes/Composer/Laravel/KawanDesa/node_modules/.pnpm/@sveltejs+vite-plugin-svelte@3.0.2_svelte@4.2.11_vite@5.2.9/node_modules/@sveltejs/vite-plugin-svelte/src/index.js";
import typescript from "file:///home/tereza/Codes/Composer/Laravel/KawanDesa/node_modules/.pnpm/@rollup+plugin-typescript@11.1.6_tslib@2.6.2_typescript@5.4.4/node_modules/@rollup/plugin-typescript/dist/es/index.js";
import sveltePreprocess from "file:///home/tereza/Codes/Composer/Laravel/KawanDesa/node_modules/.pnpm/svelte-preprocess@5.1.3_postcss@8.4.38_svelte@4.2.11_typescript@5.4.4/node_modules/svelte-preprocess/dist/index.js";
import path from "path";
var __vite_injected_original_dirname = "/home/tereza/Codes/Composer/Laravel/KawanDesa";
var vite_config_default = defineConfig({
  plugins: [
    laravel({
      input: ["resources/css/app.postcss", "resources/js/app.js"],
      refresh: true
    }),
    svelte({
      preprocess: [sveltePreprocess({ typescript: true })]
    }),
    typescript({
      tsconfig: "./tsconfig.json"
    })
  ],
  resolve: {
    alias: {
      "@P": path.resolve(__vite_injected_original_dirname, "resources/js/Pages"),
      "@R": path.resolve(__vite_injected_original_dirname, "resources/js/"),
      "@C": path.resolve(__vite_injected_original_dirname, "resources/js/Components"),
      "@U": path.resolve(__vite_injected_original_dirname, "resources/js/Utils)")
    }
  }
  //  (name) => {
  //     const pages = import.meta.glob("./Pages/**/*.svelte", { eager: true });
  //     return pages[`./Pages/${name}.svelte`];
  // },
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCIvaG9tZS90ZXJlemEvQ29kZXMvQ29tcG9zZXIvTGFyYXZlbC9LYXdhbkRlc2FcIjtjb25zdCBfX3ZpdGVfaW5qZWN0ZWRfb3JpZ2luYWxfZmlsZW5hbWUgPSBcIi9ob21lL3RlcmV6YS9Db2Rlcy9Db21wb3Nlci9MYXJhdmVsL0thd2FuRGVzYS92aXRlLmNvbmZpZy5qc1wiO2NvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9pbXBvcnRfbWV0YV91cmwgPSBcImZpbGU6Ly8vaG9tZS90ZXJlemEvQ29kZXMvQ29tcG9zZXIvTGFyYXZlbC9LYXdhbkRlc2Evdml0ZS5jb25maWcuanNcIjtpbXBvcnQgeyBkZWZpbmVDb25maWcgfSBmcm9tIFwidml0ZVwiO1xuaW1wb3J0IGxhcmF2ZWwgZnJvbSBcImxhcmF2ZWwtdml0ZS1wbHVnaW5cIjtcbmltcG9ydCB7IHN2ZWx0ZSB9IGZyb20gXCJAc3ZlbHRlanMvdml0ZS1wbHVnaW4tc3ZlbHRlXCI7XG5pbXBvcnQgdHlwZXNjcmlwdCBmcm9tIFwiQHJvbGx1cC9wbHVnaW4tdHlwZXNjcmlwdFwiO1xuaW1wb3J0IHN2ZWx0ZVByZXByb2Nlc3MgZnJvbSBcInN2ZWx0ZS1wcmVwcm9jZXNzXCI7XG5pbXBvcnQgcGF0aCBmcm9tIFwicGF0aFwiO1xuXG5leHBvcnQgZGVmYXVsdCBkZWZpbmVDb25maWcoe1xuICAgIHBsdWdpbnM6IFtcbiAgICAgICAgbGFyYXZlbCh7XG4gICAgICAgICAgICBpbnB1dDogW1wicmVzb3VyY2VzL2Nzcy9hcHAucG9zdGNzc1wiLCBcInJlc291cmNlcy9qcy9hcHAuanNcIl0sXG4gICAgICAgICAgICByZWZyZXNoOiB0cnVlLFxuICAgICAgICB9KSxcbiAgICAgICAgc3ZlbHRlKHtcbiAgICAgICAgICAgIHByZXByb2Nlc3M6IFtzdmVsdGVQcmVwcm9jZXNzKHsgdHlwZXNjcmlwdDogdHJ1ZSB9KV0sXG4gICAgICAgIH0pLFxuICAgICAgICB0eXBlc2NyaXB0KHtcbiAgICAgICAgICAgIHRzY29uZmlnOiBcIi4vdHNjb25maWcuanNvblwiLFxuICAgICAgICB9KSxcbiAgICBdLFxuICAgIHJlc29sdmU6IHtcbiAgICAgICAgYWxpYXM6IHtcbiAgICAgICAgICAgIFwiQFBcIjogcGF0aC5yZXNvbHZlKF9fZGlybmFtZSwgXCJyZXNvdXJjZXMvanMvUGFnZXNcIiksXG4gICAgICAgICAgICBcIkBSXCI6IHBhdGgucmVzb2x2ZShfX2Rpcm5hbWUsIFwicmVzb3VyY2VzL2pzL1wiKSxcbiAgICAgICAgICAgIFwiQENcIjogcGF0aC5yZXNvbHZlKF9fZGlybmFtZSwgXCJyZXNvdXJjZXMvanMvQ29tcG9uZW50c1wiKSxcbiAgICAgICAgICAgIFwiQFVcIjogcGF0aC5yZXNvbHZlKF9fZGlybmFtZSwgXCJyZXNvdXJjZXMvanMvVXRpbHMpXCIpLFxuICAgICAgICB9LFxuICAgIH0sXG4gICAgLy8gIChuYW1lKSA9PiB7XG4gICAgLy8gICAgIGNvbnN0IHBhZ2VzID0gaW1wb3J0Lm1ldGEuZ2xvYihcIi4vUGFnZXMvKiovKi5zdmVsdGVcIiwgeyBlYWdlcjogdHJ1ZSB9KTtcbiAgICAvLyAgICAgcmV0dXJuIHBhZ2VzW2AuL1BhZ2VzLyR7bmFtZX0uc3ZlbHRlYF07XG4gICAgLy8gfSxcbn0pO1xuIl0sCiAgIm1hcHBpbmdzIjogIjtBQUF5VCxTQUFTLG9CQUFvQjtBQUN0VixPQUFPLGFBQWE7QUFDcEIsU0FBUyxjQUFjO0FBQ3ZCLE9BQU8sZ0JBQWdCO0FBQ3ZCLE9BQU8sc0JBQXNCO0FBQzdCLE9BQU8sVUFBVTtBQUxqQixJQUFNLG1DQUFtQztBQU96QyxJQUFPLHNCQUFRLGFBQWE7QUFBQSxFQUN4QixTQUFTO0FBQUEsSUFDTCxRQUFRO0FBQUEsTUFDSixPQUFPLENBQUMsNkJBQTZCLHFCQUFxQjtBQUFBLE1BQzFELFNBQVM7QUFBQSxJQUNiLENBQUM7QUFBQSxJQUNELE9BQU87QUFBQSxNQUNILFlBQVksQ0FBQyxpQkFBaUIsRUFBRSxZQUFZLEtBQUssQ0FBQyxDQUFDO0FBQUEsSUFDdkQsQ0FBQztBQUFBLElBQ0QsV0FBVztBQUFBLE1BQ1AsVUFBVTtBQUFBLElBQ2QsQ0FBQztBQUFBLEVBQ0w7QUFBQSxFQUNBLFNBQVM7QUFBQSxJQUNMLE9BQU87QUFBQSxNQUNILE1BQU0sS0FBSyxRQUFRLGtDQUFXLG9CQUFvQjtBQUFBLE1BQ2xELE1BQU0sS0FBSyxRQUFRLGtDQUFXLGVBQWU7QUFBQSxNQUM3QyxNQUFNLEtBQUssUUFBUSxrQ0FBVyx5QkFBeUI7QUFBQSxNQUN2RCxNQUFNLEtBQUssUUFBUSxrQ0FBVyxxQkFBcUI7QUFBQSxJQUN2RDtBQUFBLEVBQ0o7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUtKLENBQUM7IiwKICAibmFtZXMiOiBbXQp9Cg==
