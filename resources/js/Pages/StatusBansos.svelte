<script lang="ts">
    import { onMount } from "svelte";
    import Layout from "./Layout.svelte";
    import {
        Badge,
        Table,
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
    } from "flowbite-svelte";
    import TableSearch from "@C/General/TableSearch.svelte";
    import {
        ChevronLeftOutline,
        ChevronRightOutline,
    } from "flowbite-svelte-icons";
    import Create from "@C/StatusBansos/Modals/Create.svelte";

    import { page } from "@inertiajs/svelte";
    import axiosInstance from "axios";

    const axios = axiosInstance.create({ withCredentials: true });
    const itemsPerPage = 10;
    const showPage = 5;
    const role = $page.props.auth.user.role;

    let items = [
        {
            id: 1,
            name: "Muhammad Fatoni",
            address: "Jl. Semangka No. 80",
            noHp: "081234567890",
            problem: "Saluran Pembuangan Macet",
            status: "Selesai",
        },
        {
            id: 2,
            name: "Muhammad Fatoni",
            address: "Jl. Semangka No. 80",
            noHp: "081234567890",
            problem: "Pos Ronda Rusak",
            status: "Dalam Proses",
        },
    ];
    let addComplaintModal = false;
    let searchTerm = "";
    let currentPosition = 0;
    let totalPages = 0;
    let pagesToShow: any[] = [];
    let totalItems: number = items.length;
    let startPage: number;
    let endPage: number;
    let currentPage = 1;

    let data: any;

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

    const searchFA = async (filter: string = "") => {
        const response = await axios.get(
            `/api/bansos/like/${encodeURIComponent(currentPage)}/${encodeURIComponent(filter)}`,
            {
                headers: {
                    Accept: "application/json",
                },
            },
        );

        return response.data;
    };

    const getComplainPaged = async (page: number) => {
        let url = "";

        if (role == "RT") url = `/api/bansos/rt/${encodeURIComponent(page)}`;
        if (role == "Warga")
            url = `/api/bansos/warga/${encodeURIComponent(page)}`;
        if (role != "RT" && role != "Warga")
            url = `/api/bansos/p/${encodeURIComponent(page)}`;

        try {
            const response = await axios.get(url, {
                headers: {
                    Accept: "*/*",
                },
            });
            return response.data;
        } catch (error) {
            console.error(error);
        }
    };

    const initPage = async () => {
        data = await getComplainPaged(currentPage);
    };

    $: startRange = currentPosition + 1;
    $: endRange = Math.min(currentPosition + itemsPerPage, totalItems);

    onMount(async () => {
        // Call renderPagination when the component initially mounts
        renderPagination(items.length);
    });

    $: currentPageItems = items.slice(
        currentPosition,
        currentPosition + itemsPerPage,
    );
    $: filteredItems = items.filter(
        (item) =>
            item.problem.toLowerCase().indexOf(searchTerm.toLowerCase()) !== -1,
    );

    let builder = {};

    const rebuild = async () => {
        builder = {};
    };

    // onmount dummy
    onMount(async () => {
        try {
            await initPage();
        } catch (error) {
            console.error("Error fetching data: ", error);
        }
    });

    let filteredData: any;
    const handleSearch = async (event: any) => {
        const searchValue = event.detail.value.toLowerCase();
        // console.log("Search value in handleSearch in use file:", searchValue);

        currentPage = 1;
        if (searchValue == "") {
            await initPage();
            filteredData = [...data.data];
            await rebuild();

            return;
        }

        data = await searchFA(searchValue);
        filteredData = data.data;
        await rebuild();
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
                    addComplaintModal = true;
                }}>+ Buat Pengajuan</Button
            >
        </div>
        <Table>
            <TableHead>
                <TableHeadCell>Nama</TableHeadCell>
                <TableHeadCell>Alamat</TableHeadCell>
                <TableHeadCell>No. HP</TableHeadCell>
                <!-- <TableHeadCell>Permasalahan</TableHeadCell> -->
                <TableHeadCell class="text-center">Status</TableHeadCell>
            </TableHead>
            <TableBody>
                {#key builder}
                    {#if data}
                        {#each data.data as item}
                            <TableBodyRow>
                                <TableBodyCell
                                    >{item.request_by.civilian_id
                                        .fullName}</TableBodyCell
                                >
                                <TableBodyCell
                                    >{item.request_by.civilian_id
                                        .address}</TableBodyCell
                                >
                                <TableBodyCell
                                    >+{item.request_by.civilian_id
                                        .phone}</TableBodyCell
                                >
                                {#if item.status == 1}
                                    <TableBodyCell class="text-center">
                                        <Badge color="green">Disetujui</Badge>
                                    </TableBodyCell>
                                {:else if item.status == 0}
                                    <TableBodyCell class="text-center">
                                        <Badge color="indigo">Ditolak</Badge>
                                    </TableBodyCell>
                                {:else}
                                    <TableBodyCell class="text-center">
                                        <Badge color="indigo"
                                            >Dalam Proses</Badge
                                        >
                                    </TableBodyCell>
                                {/if}
                            </TableBodyRow>
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
            {#if data}
                <span
                    class="text-sm font-normal text-gray-500 dark:text-gray-400"
                >
                    Showing
                    <span class="font-semibold text-gray-900 dark:text-white">
                        {currentPage < 2
                            ? 1
                            : data.length < 5 || data.data.length
                              ? data.length - data.data.length + 1
                              : data.data.length * currentPage - 5 + 1}
                        -
                        {data.length < 5
                            ? data.data.length
                            : data.data.length < 5
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
                            await initPage();
                        }}><ChevronLeftOutline /></Button
                    >
                    <!-- {#each data.length as pageNumber} -->
                    <Button disabled>{currentPage}</Button>
                    <!-- {/each} -->
                    <Button
                        disabled={data.data.length < 5}
                        on:click={async () => {
                            currentPage++;
                            await initPage();
                        }}><ChevronRightOutline /></Button
                    >
                </ButtonGroup>
            {/if}
        </div>
    </TableSearch>
</Layout>

{#if addComplaintModal}
    <Create bind:showState={addComplaintModal} on:comp={rebuild} />
{/if}
