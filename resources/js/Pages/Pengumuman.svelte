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
        Textarea,
    } from "flowbite-svelte";
    import {
        ChevronLeftOutline,
        ChevronRightOutline,
    } from "flowbite-svelte-icons";

    import Detail from "@C/Pengumuman/Modals/Detail.svelte";
    import Create from "@C/Pengumuman/Modals/Create.svelte";
    import Delete from "@C/Pengumuman/Modals/Delete.svelte";

    import axiosInstance from "axios";
    import Edit from "@C/Pengumuman/Modals/Edit.svelte";

    const axios = axiosInstance.create({ withCredentials: true });
    let items = [
        {
            id: 1,
            name: "Kerja Bakti",
            location: "Jl. Rambutan - Jl. Mangga",
            src: "https://cdn1.epicgames.com/ue/product/Screenshot/Screenshot23-1920x1080-a2f8a3c1f88f3b5716bdd8c9a2ea0c28.jpg?resize=1&w=1920",
            title: "Nyoba",
            desc: "Menyala Abangku",
        },
    ];
    let addAnnoucement = false;
    let modalEdit = false;
    let modalPreview = false;
    let deleteModal = false;
    let searchTerm = "";
    let currentPosition = 0;
    const itemsPerPage = 10;
    const showPage = 5;
    let totalPages = 0;
    let pagesToShow: any[] = [];
    let totalItems: number = items.length;
    let startPage: number;
    let endPage: number;

    let currentPage = 1;
    let selected = "";
    let data: any;

    let builder = {};
    const rebuild = async () => {
        await initData();
        builder = {};
    };

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

    const getNewsLanding = async () => {
        const response = await axios.get(
            `/api/news/p/${encodeURIComponent(currentPage)}`,
            {
                headers: {
                    Accept: "application/json",
                },
            },
        );

        return response.data;
    };

    const getNewsData = async (id: string = "") => {
        const response = await axios.get(
            `/api/news/${encodeURIComponent(id)}`,
            {
                headers: {
                    Accept: "application/json",
                },
            },
        );

        return response.data;
    };

    const initData = async () => {
        data = await getNewsLanding();
    };

    $: startRange = currentPosition + 1;
    $: endRange = Math.min(currentPosition + itemsPerPage, totalItems);

    onMount(async () => {
        // Call renderPagination when the component initially mounts
        renderPagination(items.length);
        await initData();
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
        placeholder="Cari Pengumuman"
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
                    addAnnoucement = true;
                }}>+ Tambah Pengumuman</Button
            >
        </div>
        <TableHead>
            <TableHeadCell>Nama Pengumuman</TableHeadCell>
            <TableHeadCell>Tanggal Buat</TableHeadCell>
            <TableHeadCell class="sr-only">Aksi</TableHeadCell>
        </TableHead>
        <TableBody>
            {#key builder}
                {#if data}
                    {#each data.data as item}
                        <TableBodyRow>
                            <TableBodyCell>{item.title}</TableBodyCell>
                            <TableBodyCell
                                >{new Date(
                                    item.created_at,
                                ).toLocaleDateString()}</TableBodyCell
                            >
                            <TableBodyCell class="text-end">
                                <Button
                                    color="blue"
                                    on:click={() => {
                                        selected = item.id;
                                        modalPreview = true;
                                    }}>Preview</Button
                                >
                                <Button
                                    color="blue"
                                    class="ms-2"
                                    on:click={() => {
                                        modalEdit = true;
                                        selected = item.id;
                                    }}>Edit Data</Button
                                >
                                <Button
                                    type="button"
                                    on:click={() => {
                                        selected = item.id;
                                        deleteModal = true;
                                    }}
                                    class="ms-2"
                                    color="red">Hapus</Button
                                >
                            </TableBodyCell>
                        </TableBodyRow>
                    {/each}
                {/if}
            {/key}
        </TableBody>

        <div
            slot="footer"
            class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0 p-4"
            aria-label="Table navigation"
        >
            {#if data}
                <span
                    class="text-sm font-normal text-gray-500 dark:text-gray-400"
                >
                    Showing
                    <span class="font-semibold text-gray-900 dark:text-white">
                        {currentPage < 2
                            ? 1
                            : data.data.length < 5
                              ? data.length - data.data.length + 1
                              : data.data.length + 1}
                        -
                        {data.data.length < 5
                            ? data.length
                            : data.data.length * currentPage}
                    </span>
                    of
                    <span class="font-semibold text-gray-900 dark:text-white"
                        >{data.length}</span
                    >
                </span>
                <ButtonGroup>
                    <Button
                        disabled={currentPage < 2}
                        on:click={async () => {
                            currentPage--;
                            await initData();
                        }}><ChevronLeftOutline /></Button
                    >
                    <!-- {#each data.length as pageNumber} -->
                    <Button disabled>{currentPage}</Button>
                    <!-- {/each} -->
                    <Button
                        disabled={currentPage >= data.length / 5}
                        on:click={async () => {
                            currentPage++;
                            await initData();
                        }}><ChevronRightOutline /></Button
                    >
                </ButtonGroup>
            {/if}
        </div>
    </TableSearch>
</Layout>

<Create bind:showState={addAnnoucement} on:comp={rebuild} />

{#if selected && modalPreview}
    <Detail
        bind:showState={modalPreview}
        bind:items={selected}
        on:comp={rebuild}
    />
{/if}

{#if selected && modalEdit}
    <Edit bind:showState={modalEdit} bind:target={selected} on:comp={rebuild} />
{/if}

{#if selected && deleteModal}
    <Delete
        bind:showState={deleteModal}
        bind:target={selected}
        on:comp={rebuild}
    />
{/if}
