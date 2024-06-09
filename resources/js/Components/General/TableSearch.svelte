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
        // console.log("Search value in TableSearch:", searchValue);
        dispatch("search", { value: searchValue });
    }

    function handleInputChange(event) {
        searchValue = event.target.value;
        if (searchValue === "") {
            dispatch("search", { value: searchValue });
        }
    }

    function handleKeyDown(event) {
        if (event.key === "Enter") {
            handleSearch();
        }
    }
</script>

<div
    class="flex flex-col bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden w-full"
>
    <div
        class="flex flex-col md:flex-row justify-between items-center space-y-3 md:space-y-0 md:space-x-4 p-4 w-full"
    >
        <div class="flex justify-between items-center">
            <!-- Kalau mau tiap kata terscan pakai on:input
                kalau ingin tiap lepas fokus pakai on:blur -->
            <Search
                size="md"
                class="max-w-xs"
                bind:value={searchValue}
                on:input={handleInputChange}
                on:keydown={handleKeyDown}
            />
            <Button size="md" class="ml-2" on:click={handleSearch}>Cari</Button>
        </div>
        <slot name="header" />
    </div>

    <slot />
    <slot name="footer" />
</div>
