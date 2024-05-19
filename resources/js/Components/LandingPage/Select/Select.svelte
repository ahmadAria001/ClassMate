<script>
    import { ClockOutline } from "flowbite-svelte-icons";
    export let options = [];
    export let index = 0;
    export let value;

    $: {
        value = options[index];
    }

    function handleChange(event) {
        index = event.target.value;
    }
</script>

<div class="relative w-40">
    <div class="flex items-center space-x-2">
        <ClockOutline class="w-5 h-5" />
        <select
            bind:value={index}
            on:change={handleChange}
            class="flex-1 bg-transparent appearance-none outline-none"
        >
            {#each options as option, i}
                <option value={i}>{option.name}</option>
            {/each}
        </select>
    </div>
</div>

<style>
    select {
        outline: none;
        border: none;
    }
    select:focus {
        outline: none;
    }
</style>
