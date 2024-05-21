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
    import Create from "@C/Pengaduan/Modals/Create.svelte";

    import axiosInstance from "axios";

    const axios = axiosInstance.create({ withCredentials: true });

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
    const itemsPerPage = 10;
    const showPage = 5;
    let totalPages = 0;
    let pagesToShow: any[] = [];
    let totalItems: number = items.length;
    let startPage: number;
    let endPage: number;

    let builder = {};

    const rebuild = () => {
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

    $: startRange = currentPosition + 1;
    $: endRange = Math.min(currentPosition + itemsPerPage, totalItems);

    onMount(() => {
        // Call renderPagination when the component initially mounts
        renderPagination(items.length);
    });

    $: currentPageItems = items.slice(
        // currentPosition,
        currentPosition + itemsPerPage,
    );
    $: filteredItems = items.filter(
        (item) =>
            item.problem.toLowerCase().indexOf(searchTerm.toLowerCase()) !== -1,
    );

    const getComplaints = async (id: string = "") => {
        try {
            const response = await axios.get(`/api/docs/complaint/${id}`, {
                headers: {
                    Accept: "*/*",
                },
            });
            return response.data;
        } catch (error) {
            console.error(error);
        }
    };
</script>

<Layout>
    <TableSearch
        placeholder="Cari Pengaduan"
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
                    addComplaintModal = true;
                }}>+ Buat Pengaduan</Button
            >
        </div>
        <TableHead>
            <TableHeadCell>Nama</TableHeadCell>
            <!-- <TableHeadCell>Alamat</TableHeadCell> -->
            <TableHeadCell>No. HP</TableHeadCell>
            <TableHeadCell>Permasalahan</TableHeadCell>
            <TableHeadCell class="text-center">Status</TableHeadCell>
        </TableHead>
        <TableBody>
            {#key builder}
                {#await getComplaints() then data}
                    {#each data.data as item}
                        <TableBodyRow>
                            <TableBodyCell
                                >{item.created_by.civilian_id
                                    .fullName}</TableBodyCell
                            >
                            <!-- <TableBodyCell
                                >{item.created_by.civilian_id
                                    .address}</TableBodyCell
                            > -->
                            <TableBodyCell
                                >{item.created_by.civilian_id
                                    .phone}</TableBodyCell
                            >
                            <TableBodyCell
                                >{item.docs_id.description}</TableBodyCell
                            >
                            {#if item.complaintStatus == "Resolved"}
                                <TableBodyCell class="text-center">
                                    <Badge color="green"
                                        >{item.complaintStatus}</Badge
                                    >
                                </TableBodyCell>
                            {:else if item.complaintStatus == "Open"}
                                <TableBodyCell class="text-center">
                                    <Badge color="primary"
                                        >{item.complaintStatus}</Badge
                                    >
                                </TableBodyCell>
                            {:else}
                                <TableBodyCell class="text-center">
                                    <Badge color="red"
                                        >{item.complaintStatus}</Badge
                                    >
                                </TableBodyCell>
                            {/if}
                        </TableBodyRow>
                    {/each}
                {/await}
            {/key}
        </TableBody>

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

<Create bind:showState={addComplaintModal} on:comp={rebuild} />
