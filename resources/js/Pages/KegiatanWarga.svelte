<script lang="ts">
    import { onMount } from "svelte";
    import Layout from "./Layout.svelte";
    import {
        TableBody,
        TableBodyCell,
        TableBodyRow,
        TableHead,
        TableHeadCell,
        Button,
        Modal,
        Label,
        Input,
        ButtonGroup,
        Popover,
        Table,
    } from "flowbite-svelte";
    import TableSearch from "@C/General/TableSearch.svelte";
    import {
        ChevronLeftOutline,
        ChevronRightOutline,
        QuestionCircleSolid,
    } from "flowbite-svelte-icons";

    import axiosInstance from "axios";
    import Create from "@C/Kegiatan/Modals/Create.svelte";
    import Edit from "@C/Kegiatan/Modals/Edit.svelte";

    const axios = axiosInstance.create({ withCredentials: true });
    let builder = {};

    const rebuild = async () => {
        await initData();
        filteredData = data.data;
        builder = {};
    };

    let data: any | null = null;

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
    let currentPage = 1;

    let selected: string | null = null;

    const getRequestDocs = async (page = 1) => {
        const response = await axios.get(
            `/api/docs/activity/p/${encodeURIComponent(page)}`,
        );

        return response.data;
    };

    const initData = async () => {
        data = await getRequestDocs(currentPage);
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

    $: startRange = currentPosition + 1;
    $: endRange = Math.min(currentPosition + itemsPerPage, totalItems);

    $: currentPageItems = items.slice(
        currentPosition,
        currentPosition + itemsPerPage,
    );
    $: filteredItems = items.filter(
        (item) =>
            item.name.toLowerCase().indexOf(searchTerm.toLowerCase()) !== -1,
    );

    const dateFormatter = (epoc: number) => {
        const date = new Date(epoc);

        const monthNames = [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Dec",
        ];

        const day = date.getDate();

        const monthIndex = date.getMonth();
        const monthName = monthNames[monthIndex];

        const year = date.getFullYear();

        return `${day} ${monthName} ${year}`;
    };

    onMount(async () => {
        try {
            // Call renderPagination when the component initially mounts
            renderPagination(items.length);
            await initData();
            filteredData = data.data;
            // console.log(filteredData);
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    });

    let filteredData: any;
    const handleSearch = (event: any) => {
        const searchValue = event.detail.value.toLowerCase();
        // console.log("Search value in handleSearch in use file:", searchValue);
        if (searchValue == "") {
            filteredData = [data.data];
        }
        filteredData = data.data.filter((d: any) =>
            d.name.toLowerCase().includes(searchValue),
        );
        // console.log(filteredData);
        rebuild();
    };
</script>

<Layout>
    <TableSearch on:search={handleSearch}>
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
        <Table>
            <TableHead>
                <TableHeadCell>Nama Kegiatan</TableHeadCell>
                <TableHeadCell>Lokasi</TableHeadCell>
                <TableHeadCell class="text-center">Waktu</TableHeadCell>
                <!-- <TableHeadCell class="text-center">Tanggal</TableHeadCell> -->
                <TableHeadCell class="sr-only">Aksi</TableHeadCell>
            </TableHead>
            <TableBody>
                {#key builder}
                    {#if filteredData}
                        {#each filteredData as item, idx}
                            <TableBodyRow>
                                <TableBodyCell>
                                    <div
                                        class="flex justify-between align-middle gap-2"
                                    >
                                        <span class="w-full truncate">
                                            {item.name}
                                        </span>
                                        <QuestionCircleSolid
                                            id={`title-${item.id}`}
                                        />
                                    </div>
                                </TableBodyCell>
                                <TableBodyCell>
                                    {item.location}
                                </TableBodyCell>
                                <TableBodyCell
                                    class="text-center uppercase flex justify-center"
                                >
                                    <span class="text-center">
                                        {dateFormatter(item.startDate * 1000)}
                                        <br />
                                        {new Date(
                                            item.startDate * 1000,
                                        ).toLocaleTimeString(undefined, {
                                            hour12: false,
                                        })}
                                    </span>
                                    <span class="ms-5 text-center">
                                        {dateFormatter(item.endDate * 1000)}
                                        <br />
                                        {new Date(
                                            item.endDate * 1000,
                                        ).toLocaleTimeString(undefined, {
                                            hour12: false,
                                        })}
                                    </span>
                                </TableBodyCell>
                                <TableBodyCell>
                                    <Button
                                        color="blue"
                                        on:click={() => {
                                            selected = item.id;
                                            modalEdit = true;
                                        }}>Edit Data</Button
                                    >
                                </TableBodyCell>
                            </TableBodyRow>

                            <Popover
                                class="w-64 text-sm text-black dark:text-white"
                                title="Deskripsi"
                                triggeredBy={`#title-${item.id}`}
                            >
                                {item.docs_id.description}
                            </Popover>
                        {/each}
                    {/if}
                {/key}
            </TableBody>
        </Table>
        <div
            slot="footer"
            class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0 p-4"
            aria-label="Table navigation"
        >
            {#if filteredData}
                <span
                    class="text-sm font-normal text-gray-500 dark:text-gray-400"
                >
                    Showing
                    <span class="font-semibold text-gray-900 dark:text-white">
                        {currentPage < 2
                            ? 1
                            : filteredData.length < 5
                              ? data.length - filteredData.length + 1
                              : filteredData.length + 1}
                        -
                        {filteredData.length < 5
                            ? data.length
                            : filteredData.length * currentPage}
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

{#if addActivity}
    <Create bind:showState={addActivity} on:comp={rebuild} />
{/if}

{#if selected && modalEdit}
    <Edit bind:showState={modalEdit} bind:target={selected} on:comp={rebuild} />
{/if}
