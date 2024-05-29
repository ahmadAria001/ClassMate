<script lang="ts">
    export let title: string;
    export let value: string | number | null = 0;
    export let icon;
    export let iconBgClass: string = "bg-green-500";
    export let textColor: string = "";
    export let cardClass: string =
        "flex items-center p-4 rounded-lg shadow-md dark:bg-gray-800";
</script>

<div class={cardClass}>
    <div class={`p-3 rounded-full text-white font-bold ${iconBgClass}`}>
        <svelte:component this={icon} class="w-6 h-6" />
    </div>
    <div class="ml-4">
        <h4 class="text-black font-bold">{title}</h4>
        <h2 class={`text-2xl font-semibold ${textColor}`}>{value}</h2>\
    </div>
</div>
