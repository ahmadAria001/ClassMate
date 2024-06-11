// vite.config.js
import { defineConfig } from "file:///home/tereza/Codes/Composer/Laravel/KawanDesa/node_modules/.pnpm/vite@5.1.3/node_modules/vite/dist/node/index.js";
import laravel from "file:///home/tereza/Codes/Composer/Laravel/KawanDesa/node_modules/.pnpm/laravel-vite-plugin@1.0.1_vite@5.1.3/node_modules/laravel-vite-plugin/dist/index.js";
import { svelte } from "file:///home/tereza/Codes/Composer/Laravel/KawanDesa/node_modules/.pnpm/@sveltejs+vite-plugin-svelte@3.0.2_svelte@4.2.11_vite@5.1.3/node_modules/@sveltejs/vite-plugin-svelte/src/index.js";
import path from "path";
var __vite_injected_original_dirname = "/home/tereza/Codes/Composer/Laravel/KawanDesa";
var vite_config_default = defineConfig({
  plugins: [
    laravel({
      input: ["resources/css/app.postcss", "resources/js/app.js"],
      refresh: true
    }),
    svelte({})
  ],
  resolve: {
    alias: {
      "@P": path.resolve(__vite_injected_original_dirname, "resources/js/Pages"),
      "@R": path.resolve(__vite_injected_original_dirname, "resources/js/"),
      "@C": path.resolve(__vite_injected_original_dirname, "resources/js/Components")
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
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCIvaG9tZS90ZXJlemEvQ29kZXMvQ29tcG9zZXIvTGFyYXZlbC9LYXdhbkRlc2FcIjtjb25zdCBfX3ZpdGVfaW5qZWN0ZWRfb3JpZ2luYWxfZmlsZW5hbWUgPSBcIi9ob21lL3RlcmV6YS9Db2Rlcy9Db21wb3Nlci9MYXJhdmVsL0thd2FuRGVzYS92aXRlLmNvbmZpZy5qc1wiO2NvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9pbXBvcnRfbWV0YV91cmwgPSBcImZpbGU6Ly8vaG9tZS90ZXJlemEvQ29kZXMvQ29tcG9zZXIvTGFyYXZlbC9LYXdhbkRlc2Evdml0ZS5jb25maWcuanNcIjtpbXBvcnQgeyBkZWZpbmVDb25maWcgfSBmcm9tIFwidml0ZVwiO1xuaW1wb3J0IGxhcmF2ZWwgZnJvbSBcImxhcmF2ZWwtdml0ZS1wbHVnaW5cIjtcbmltcG9ydCB7IHN2ZWx0ZSB9IGZyb20gXCJAc3ZlbHRlanMvdml0ZS1wbHVnaW4tc3ZlbHRlXCI7XG5pbXBvcnQgcGF0aCBmcm9tIFwicGF0aFwiO1xuXG5leHBvcnQgZGVmYXVsdCBkZWZpbmVDb25maWcoe1xuICAgIHBsdWdpbnM6IFtcbiAgICAgICAgbGFyYXZlbCh7XG4gICAgICAgICAgICBpbnB1dDogW1wicmVzb3VyY2VzL2Nzcy9hcHAucG9zdGNzc1wiLCBcInJlc291cmNlcy9qcy9hcHAuanNcIl0sXG4gICAgICAgICAgICByZWZyZXNoOiB0cnVlLFxuICAgICAgICB9KSxcbiAgICAgICAgc3ZlbHRlKHt9KSxcbiAgICBdLFxuICAgIHJlc29sdmU6IHtcbiAgICAgICAgYWxpYXM6IHtcbiAgICAgICAgICAgIFwiQFBcIjogcGF0aC5yZXNvbHZlKF9fZGlybmFtZSwgXCJyZXNvdXJjZXMvanMvUGFnZXNcIiksXG4gICAgICAgICAgICBcIkBSXCI6IHBhdGgucmVzb2x2ZShfX2Rpcm5hbWUsIFwicmVzb3VyY2VzL2pzL1wiKSxcbiAgICAgICAgICAgIFwiQENcIjogcGF0aC5yZXNvbHZlKF9fZGlybmFtZSwgXCJyZXNvdXJjZXMvanMvQ29tcG9uZW50c1wiKSxcbiAgICAgICAgfSxcbiAgICB9LFxuICAgIC8vICAobmFtZSkgPT4ge1xuICAgIC8vICAgICBjb25zdCBwYWdlcyA9IGltcG9ydC5tZXRhLmdsb2IoXCIuL1BhZ2VzLyoqLyouc3ZlbHRlXCIsIHsgZWFnZXI6IHRydWUgfSk7XG4gICAgLy8gICAgIHJldHVybiBwYWdlc1tgLi9QYWdlcy8ke25hbWV9LnN2ZWx0ZWBdO1xuICAgIC8vIH0sXG59KTtcbiJdLAogICJtYXBwaW5ncyI6ICI7QUFBeVQsU0FBUyxvQkFBb0I7QUFDdFYsT0FBTyxhQUFhO0FBQ3BCLFNBQVMsY0FBYztBQUN2QixPQUFPLFVBQVU7QUFIakIsSUFBTSxtQ0FBbUM7QUFLekMsSUFBTyxzQkFBUSxhQUFhO0FBQUEsRUFDeEIsU0FBUztBQUFBLElBQ0wsUUFBUTtBQUFBLE1BQ0osT0FBTyxDQUFDLDZCQUE2QixxQkFBcUI7QUFBQSxNQUMxRCxTQUFTO0FBQUEsSUFDYixDQUFDO0FBQUEsSUFDRCxPQUFPLENBQUMsQ0FBQztBQUFBLEVBQ2I7QUFBQSxFQUNBLFNBQVM7QUFBQSxJQUNMLE9BQU87QUFBQSxNQUNILE1BQU0sS0FBSyxRQUFRLGtDQUFXLG9CQUFvQjtBQUFBLE1BQ2xELE1BQU0sS0FBSyxRQUFRLGtDQUFXLGVBQWU7QUFBQSxNQUM3QyxNQUFNLEtBQUssUUFBUSxrQ0FBVyx5QkFBeUI7QUFBQSxJQUMzRDtBQUFBLEVBQ0o7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUtKLENBQUM7IiwKICAibmFtZXMiOiBbXQp9Cg==
