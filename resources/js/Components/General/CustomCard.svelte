<script lang="ts">
    export let divclass: string | null = "";
    export let headerclass: string | null = "";
    export let footerclass: string | null = "";
</script>

<div
    class="bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 rounded-lg border border-gray-200 dark:border-gray-700 divide-gray-200 dark:divide-gray-700 shadow-md flex w-full flex-col p-4 sm:p-6 {divclass}"
>
    <slot name="header" class={headerclass} />
    <slot />
    <slot name="footer" class={footerclass} />
</div>
