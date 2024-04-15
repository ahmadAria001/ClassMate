<script lang="ts">
    import { onMount } from "svelte";
    import Layout from "./Layout.svelte";
    import {
        TableBody,
        TableBodyCell,
        TableBodyRow,
        TableHead,
        TableHeadCell,
        TableSearch,
        Button,
        Modal,
        Label,
        Input,
        ButtonGroup,
    } from "flowbite-svelte";
    import {
        ChevronLeftOutline,
        ChevronRightOutline,
    } from "flowbite-svelte-icons";
    let items = [
        {
            id: 1,
            name: "Kerja Bakti",
            location: "Jl. Rambutan - Jl. Mangga",
            time: "08.00 - 10.00",
            date: "02-02-2024",
        },
        {
            id: 2,
            name: "Rapat Karang Taruna",
            location: "Rumah Adit, Jl.Manggis no.8",
            time: "08.00 - 10.00",
            date: "02-02-2024",
        },
    ];
    let addActivity = false;
    let modalEdit = false;
    let searchTerm = "";
    let currentPosition = 0;
    const itemsPerPage = 10;
    const showPage = 5;
    let totalPages = 0;
    let pagesToShow: any[] = [];
    let totalItems: number = items.length;
    let startPage: number;
    let endPage: number;

    const updateDataAndPagination = () => {
        const currentPageItems = items.slice(
            currentPosition,
            currentPosition + itemsPerPage,
        );
        renderPagination(currentPageItems.length);
    };

    const loadNextPage = () => {
        if (currentPosition + itemsPerPage < items.length) {
            currentPosition += itemsPerPage;
            updateDataAndPagination();
        }
    };

    const loadPreviousPage = () => {
        if (currentPosition - itemsPerPage >= 0) {
            currentPosition -= itemsPerPage;
            updateDataAndPagination();
        }
    };

    const renderPagination = (totalItems: number) => {
        totalPages = Math.ceil(items.length / itemsPerPage);
        const currentPage = Math.ceil((currentPosition + 1) / itemsPerPage);

        startPage = currentPage - Math.floor(showPage / 2);
        startPage = Math.max(1, startPage);
        endPage = Math.min(startPage + showPage - 1, totalPages);

        pagesToShow = Array.from(
            { length: endPage - startPage + 1 },
            (_, i) => startPage + i,
        );
    };

    const goToPage = (pageNumber: number) => {
        currentPosition = (pageNumber - 1) * itemsPerPage;
        updateDataAndPagination();
    };

    $: startRange = currentPosition + 1;
    $: endRange = Math.min(currentPosition + itemsPerPage, totalItems);

    onMount(() => {
        // Call renderPagination when the component initially mounts
        renderPagination(items.length);
    });

    $: currentPageItems = items.slice(
        currentPosition,
        currentPosition + itemsPerPage,
    );
    $: filteredItems = items.filter(
        (item) =>
            item.name.toLowerCase().indexOf(searchTerm.toLowerCase()) !== -1,
    );
</script>

<Layout>
    <TableSearch
        placeholder="Cari Kegiatan"
        hoverable={true}
        bind:inputValue={searchTerm}
        divClass="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg overflow-hidden"
        innerDivClass="flex items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4"
        classInput="text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2  pl-10"
    >
        <div
            slot="header"
            class="md:w-auto flex flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0"
        >
            <Button
                on:click={() => {
                    addActivity = true;
                }}>+ Tambah Kegiatan</Button
            >
        </div>
        <Modal title="Tambah Kegiatan" bind:open={addActivity} autoclose>
            <form method="POST">
                <div class="grid md:grid-cols-2 md:gap-6">
                    <div class="mb-4">
                        <Label for="activityName" class="mb-2"
                            >Nama Kegiatan</Label
                        >
                        <Input id="activityName" placeholder="Nama Kegiatan" />
                    </div>
                    <div class="mb-4">
                        <Label for="location" class="mb-2">Lokasi</Label>
                        <Input id="location" placeholder="Lokasi" />
                    </div>
                </div>
                <div class="grid md:grid-cols-2 md:gap-6">
                    <div class="mb-4">
                        <Label for="time" class="mb-2">Waktu</Label>
                        <Input id="time" placeholder="Waktu" />
                    </div>
                    <div class="mb-4">
                        <Label for="date" class="mb-2">Tanggal</Label>
                        <Input type="date" id="date" placeholder="Tanggal" />
                    </div>
                </div>
                <div class="block flex">
                    <Button type="submit" class="ml-auto">Simpan</Button>
                </div>
            </form>
        </Modal>

        <TableHead>
            <TableHeadCell>Nama Kegiatan</TableHeadCell>
            <TableHeadCell>Lokasi</TableHeadCell>
            <TableHeadCell class="text-center">Waktu</TableHeadCell>
            <TableHeadCell class="text-center">Tanggal</TableHeadCell>
            <TableHeadCell class="sr-only">Aksi</TableHeadCell>
        </TableHead>
        <TableBody>
            {#each filteredItems as item}
                <TableBodyRow>
                    <TableBodyCell>{item.name}</TableBodyCell>
                    <TableBodyCell>{item.location}</TableBodyCell>
                    <TableBodyCell class="text-center uppercase"
                        >{item.time}</TableBodyCell
                    >
                    <TableBodyCell class="text-center"
                        >{item.date}</TableBodyCell
                    >
                    <TableBodyCell>
                        <Button
                            color="blue"
                            on:click={() => {
                                modalEdit = true;
                            }}>Edit Data</Button
                        >
                    </TableBodyCell>
                </TableBodyRow>
            {/each}
        </TableBody>

        <!-- modal edit -->

        <Modal title="Edit Kegiatan" bind:open={modalEdit} autoclose>
            <form method="POST">
                <div class="grid md:grid-cols-2 md:gap-6">
                    <div class="mb-4">
                        <Label for="activityName" class="mb-2"
                            >Nama Kegiatan</Label
                        >
                        <Input id="activityName" placeholder="Nama Kegiatan" />
                    </div>
                    <div class="mb-4">
                        <Label for="location" class="mb-2">Lokasi</Label>
                        <Input id="location" placeholder="Lokasi" />
                    </div>
                </div>
                <div class="grid md:grid-cols-2 md:gap-6">
                    <div class="mb-4">
                        <Label for="time" class="mb-2">Waktu</Label>
                        <Input id="time" placeholder="Waktu" />
                    </div>
                    <div class="mb-4">
                        <Label for="date" class="mb-2">Tanggal</Label>
                        <Input type="date" id="date" placeholder="Tanggal" />
                    </div>
                </div>
                <div class="block flex">
                    <Button type="submit" class="ml-auto">Simpan</Button>
                </div>
            </form>
        </Modal>

        <div
            slot="footer"
            class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0 p-4"
            aria-label="Table navigation"
        >
            <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                Showing
                <span class="font-semibold text-gray-900 dark:text-white"
                    >{startRange}-{endRange}</span
                >
                of
                <span class="font-semibold text-gray-900 dark:text-white"
                    >{totalItems}</span
                >
            </span>
            <ButtonGroup>
                <Button
                    on:click={loadPreviousPage}
                    disabled={currentPosition === 0}
                    ><ChevronLeftOutline /></Button
                >
                {#each pagesToShow as pageNumber}
                    <Button on:click={() => goToPage(pageNumber)}
                        >{pageNumber}</Button
                    >
                {/each}
                <Button
                    on:click={loadNextPage}
                    disabled={totalPages === endPage}
                    ><ChevronRightOutline /></Button
                >
            </ButtonGroup>
        </div>
    </TableSearch>
</Layout>
