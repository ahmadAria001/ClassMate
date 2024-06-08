<script lang="ts">
    import { onMount } from "svelte";
    import Layout from "./Layout.svelte";
    import {
        Badge,
        TableBody,
        TableBodyCell,
        TableBodyRow,
        TableHead,
        TableHeadCell,
        Button,
        ButtonGroup,
        Table,
    } from "flowbite-svelte";
    import TableSearch from "@C/General/TableSearch.svelte";
    import {
        ChevronLeftOutline,
        ChevronRightOutline,
    } from "flowbite-svelte-icons";

    import { page } from "@inertiajs/svelte";
    import axiosInstance from "axios";
    import Edit from "@C/PengajuanSurat/Modals/Edit.svelte";

    const axios = axiosInstance.create({ withCredentials: true });
    let data: any | null = null;

    let items = [
        {
            id: 1,
            name: "Muhammad Fatoni",
            address: "Jl. Semangka No. 80",
            noHp: "081234567890",
            desc: "Surat Kurang Mampu",
            status: "Selesai",
        },
        {
            id: 2,
            name: "Muhammad Fatoni",
            address: "Jl. Semangka No. 80",
            noHp: "081234567890",
            desc: "Surat Kependudukan",
            status: "Dalam Proses",
        },
    ];

    let modalDetailSurat = false;
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

    let builder = {};
    const rebuild = async () => {
        selected = null;
        await initData();
        builder = {};
    };

    const role = $page.props.auth.user.role;

    const getRequestDocs = async (page = 1) => {
        let url: string = "";

        if (role == "RT")
            url = `/api/docs/request/rt/${encodeURIComponent(page)}`;

        if (role == "RW")
            url = `/api/docs/request/rw/${encodeURIComponent(page)}`;

        if (role == "Admin")
            url = `/api/docs/request/p/${encodeURIComponent(page)}`;

        const response = await axios.get(url);

        return response.data;
    };

    const initData = async () => {
        data = await getRequestDocs(currentPage);
        // console.log(data);
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

    onMount(async () => {
        try {
            // Call renderPagination when the component initially mounts
            renderPagination(items.length);
            await initData();
            filteredData = data.data;
            console.log(data);
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    });

    let filteredData: any;
    const handleSearch = (event: any) => {
        const searchValue = event.detail.value.toLowerCase();
        // console.log("Search value in handleSearch in use file:", searchValue);
        if (searchValue == "") {
            filteredData = [...data.data];
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
        ></div>
        <Table>
            <TableHead>
                <TableHeadCell>Nama</TableHeadCell>
                <!-- <TableHeadCell>Alamat</TableHeadCell> -->
                <TableHeadCell>No. HP</TableHeadCell>
                <TableHeadCell>Keterangan</TableHeadCell>
                <TableHeadCell class="text-center">Status</TableHeadCell>
                <TableHeadCell class="sr-only">Aksi</TableHeadCell>
            </TableHead>
            <TableBody>
                {#key builder}
                    {#if data}
                        {#each data.data as item}
                            <TableBodyRow>
                                <TableBodyCell>
                                    {item.request_by.fullName}
                                </TableBodyCell>
                                <!-- <TableBodyCell
                                    >{item.request_by.address}</TableBodyCell
                                > -->
                                <TableBodyCell
                                    >{item.request_by.phone}</TableBodyCell
                                >
                                <TableBodyCell
                                    >{item.docs_id.description}</TableBodyCell
                                >
                                {#if item.requestStatus == "Resolved"}
                                    <TableBodyCell class="text-center">
                                        <Badge color="green">Disetujui</Badge>
                                    </TableBodyCell>
                                {:else if item.requestStatus == "Open"}
                                    <TableBodyCell class="text-center">
                                        <Badge color="indigo"
                                            >Dalam Proses</Badge
                                        >
                                    </TableBodyCell>
                                {:else}
                                    <TableBodyCell class="text-center">
                                        <Badge color="red">Ditolak</Badge>
                                    </TableBodyCell>
                                {/if}
                                <TableBodyCell>
                                    <Button
                                        color="blue"
                                        on:click={() => {
                                            selected = item.id;
                                            modalDetailSurat = true;
                                        }}>Detail</Button
                                    >
                                </TableBodyCell>
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
                        on:click={loadPreviousPage}
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
                        on:click={loadNextPage}
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

{#if selected}
    <Edit
        bind:showState={modalDetailSurat}
        bind:target={selected}
        on:comp={rebuild}
    />
{/if}
