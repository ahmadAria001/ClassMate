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
    import {
        ChevronLeftOutline,
        ChevronRightOutline,
    } from "flowbite-svelte-icons";
    import TableSearch from "@C/General/TableSearch.svelte";

    import { page } from "@inertiajs/svelte";
    import axiosInstance from "axios";
    import Create from "@C/PengajuanSurat/Modals/Create.svelte";

    const axios = axiosInstance.create({ withCredentials: true });
    let builder = {};

    const rebuild = async () => {
        builder = {};
        await initData();
        filteredData = data.data;
    };

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

    const role = $page.props.auth.user.role;
    const itemsPerPage = 10;
    const showPage = 5;

    let addCertificate = false;
    let searchTerm = "";
    let currentPosition = 0;
    let totalPages = 0;
    let pagesToShow: any[] = [];
    let totalItems: number = items.length;
    let startPage: number;
    let endPage: number;
    let currentPage = 1;

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

    const getRequestDocs = async (page = 1) => {
        const url =
            role == "Warga"
                ? `/api/docs/request/warga/${encodeURIComponent(page)}`
                : `/api/docs/request/p/${encodeURIComponent(page)}`;

        const response = await axios.get(url);

        return response.data;
    };

    const initData = async () => {
        data = await getRequestDocs(currentPage);
        // console.log(data);
    };

    $: startRange = currentPosition + 1;
    $: endRange = Math.min(currentPosition + itemsPerPage, totalItems);

    onMount(async () => {
        // Call renderPagination when the component initially mounts
        renderPagination(items.length);

        await initData();
        filteredData = data.data;
    });

    $: currentPageItems = items.slice(
        currentPosition,
        currentPosition + itemsPerPage,
    );
    // $: filteredItems = items.filter(
    //     (item) =>
    //         item.desc.toLowerCase().indexOf(searchTerm.toLowerCase()) !== -1,
    // );
    let filteredData: any;
    const handleSearch = (event: any) => {
        const searchValue = event.detail.value.toLowerCase();
        // console.log("Search value in handleSearch in use file:", searchValue);
        filteredData = data.data.filter((d: any) =>
            d.docs_id.description.toLowerCase().includes(searchValue),
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
                    addCertificate = true;
                }}>+ Buat Pengajuan</Button
            >
        </div>
        <Table>
            <TableHead>
                <TableHeadCell>No.</TableHeadCell>
                <TableHeadCell>Nama</TableHeadCell>
                <!-- <TableHeadCell>Alamat</TableHeadCell>
                <TableHeadCell>No. HP</TableHeadCell> -->
                <TableHeadCell>Keterangan</TableHeadCell>
                <TableHeadCell class="text-center">Status</TableHeadCell>
            </TableHead>
            <TableBody>
                {#key builder}
                    {#if filteredData}
                        {#if filteredData.length > 0}
                            {#each filteredData as item, idx}
                                <TableBodyRow>
                                    <TableBodyCell>{idx + 1}</TableBodyCell>

                                    <TableBodyCell
                                        >{item.request_by
                                            .fullName}</TableBodyCell
                                    >
                                    <!-- <TableBodyCell
                                        >{item.request_by
                                            .address}</TableBodyCell
                                    >
                                    <TableBodyCell
                                        >{item.request_by.phone}</TableBodyCell
                                    > -->
                                    <TableBodyCell
                                        >{item.docs_id
                                            .description}</TableBodyCell
                                    >
                                    {#if item.requestStatus == "Resolved"}
                                        <TableBodyCell class="text-center">
                                            <Badge color="green"
                                                >Disetujui</Badge
                                            >
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
                                </TableBodyRow>
                            {/each}
                        {/if}
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
                            ? filteredData.length == 0
                                ? 0
                                : 1
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

<Create bind:showState={addCertificate} on:comp={rebuild} />
