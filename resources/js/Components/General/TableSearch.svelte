<script>
    import { createEventDispatcher } from "svelte";
    import { onMount } from "svelte";
    import {
        TableHeadCell,
        TableBodyRow,
        TableBodyCell,
        Search,
        Button,
    } from "flowbite-svelte";

    let searchValue = "";
    const dispatch = createEventDispatcher();

    function handleSearch() {
        console.log("Search value in TableSearch:", searchValue);
        dispatch("search", { value: searchValue });
    }
</script>

<div
    class="flex flex-col bg-white dark:bg-gray-800 shadow-md sm:rounded-lg overflow-hidden w-full"
>
    <div
        class="flex flex-col lg:flex-row justify-between items-center space-y-3 md:space-y-0 md:space-x-4 p-4 w-full"
    >
        <div class="flex justify-between items-center">
            <Search size="md" class="max-w-xs" bind:value={searchValue} />
            <Button size="md" class="ml-2" on:click={handleSearch}>Cari</Button>
        </div>
        <slot name="header" />
    </div>

    <slot />
    <slot name="footer" />
</div>
