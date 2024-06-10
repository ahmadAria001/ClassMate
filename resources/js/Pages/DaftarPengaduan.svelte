<script lang="ts">
    import { onMount } from "svelte";
    import Layout from "./Layout.svelte";
    import {
        Badge,
        TableBody,
        Table,
        TableBodyCell,
        TableBodyRow,
        TableHead,
        TableHeadCell,
        Button,
        ButtonGroup,
        Popover,
    } from "flowbite-svelte";
    import {
        ChevronLeftOutline,
        ChevronRightOutline,
        QuestionCircleSolid,
    } from "flowbite-svelte-icons";
    import TableSearch from "@C/General/TableSearch.svelte";

    import { page } from "@inertiajs/svelte";
    import axiosInstance from "axios";
    import Edit from "@C/Pengaduan/Modals/Detail.svelte";
    import Detail from "@C/Pengaduan/Modals/Detail.svelte";

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

    let data: any;
    let modalDetailPengaduan = false;
    let searchTerm = "";
    let currentPosition = 0;
    let totalPages = 0;
    let pagesToShow: any[] = [];
    let totalItems: number = items.length;
    let startPage: number;
    let endPage: number;
    let selected: string | null = null;
    let currentPage = 1;

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

    const getComplainPaged = async (page: number) => {
        let format = /[ `!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/;
        if (format.test(encodeURIComponent(page))) return;

        let url = "";

        if (role == "RT")
            url = `/api/docs/complaint/rt/${encodeURIComponent(page)}`;
        if (role == "Warga")
            url = `/api/docs/complaint/warga/${encodeURIComponent(page)}`;
        if (role != "RT" && role != "Warga")
            url = `/api/docs/complaint/${encodeURIComponent(page)}`;

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

    const getComplaints = async (id: string = "") => {
        let format = /[ `!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/;
        if (format.test(encodeURIComponent(id))) return;

        try {
            const response = await axios.get(
                `/api/docs/complaint/${encodeURIComponent(id)}`,
                {
                    headers: {
                        Accept: "*/*",
                    },
                },
            );

            console.log(response.data);
            return response.data;
        } catch (error) {
            console.error(error);
        }
    };

    const initPage = async () => {
        data = await getComplainPaged(currentPage);
    };

    const handleSearch = async (filterInput: string) => {
        data = await getComplaints(filterInput);
        rebuild();
    };

    $: startRange = currentPosition + 1;
    $: endRange = Math.min(currentPosition + itemsPerPage, totalItems);

    onMount(async () => {
        // Call renderPagination when the component initially mounts
        renderPagination(items.length);

        await initPage();
    });

    $: currentPageItems = items.slice(
        currentPosition,
        currentPosition + itemsPerPage,
    );
    $: filteredItems = items.filter(
        (item) =>
            item.problem.toLowerCase().indexOf(searchTerm.toLowerCase()) !== -1,
    );
</script>

<Layout>
    <TableSearch on:search={(e) => handleSearch(e.detail.value)}>
        <div
            slot="header"
            class="md:w-auto flex flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0"
        ></div>
        <Table>
            <TableHead>
                <TableHeadCell>Nama</TableHeadCell>
                <TableHeadCell>Alamat</TableHeadCell>
                <TableHeadCell>No. HP</TableHeadCell>
                <TableHeadCell>Permasalahan</TableHeadCell>
                <TableHeadCell class="text-center">Status</TableHeadCell>
                <TableHeadCell class="sr-only">Aksi</TableHeadCell>
            </TableHead>
            <TableBody>
                {#key builder}
                    {#if data}
                        {#if data.data.length > 0}
                            {#each data.data as item}
                                <TableBodyRow>
                                    <TableBodyCell>
                                        <span
                                            class="w-1/4 truncate
"
                                        >
                                            {item.created_by.civilian_id
                                                .fullName}
                                        </span>
                                    </TableBodyCell>
                                    <TableBodyCell tdClass="max-w-52">
                                        <div
                                            class="flex justify-between align-middle gap-2"
                                        >
                                            <span class="w-full truncate">
                                                {item.created_by.civilian_id
                                                    .address}
                                            </span>
                                            <QuestionCircleSolid
                                                id={`address-${item.id}`}
                                            />
                                        </div>
                                    </TableBodyCell>
                                    <TableBodyCell
                                        >{item.created_by.civilian_id
                                            .phone}</TableBodyCell
                                    >
                                    <TableBodyCell class="w-20">
                                        <div
                                            class="flex justify-between max-w-52 align-middle gap-2"
                                        >
                                            <span class="w-full truncate">
                                                {item.docs_id.description}
                                            </span>
                                            <QuestionCircleSolid
                                                id={`desc-${item.id}`}
                                            />
                                        </div>
                                    </TableBodyCell>
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

                                    <TableBodyCell>
                                        <Button
                                            color="blue"
                                            on:click={() => {
                                                selected = item.id;
                                                modalDetailPengaduan = true;
                                            }}>Detail</Button
                                        >
                                    </TableBodyCell>
                                </TableBodyRow>

                                <Popover
                                    class="w-64 text-sm text-black dark:text-white"
                                    title="Alamat"
                                    triggeredBy={`#address-${item.id}`}
                                >
                                    {item.created_by.civilian_id.address}
                                </Popover>

                                <Popover
                                    class="w-64 text-sm text-black dark:text-white"
                                    title="Masalah"
                                    triggeredBy={`#desc-${item.id}`}
                                >
                                    {item.docs_id.description}
                                </Popover>
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

{#if selected}
    <Detail
        bind:showState={modalDetailPengaduan}
        bind:target={selected}
        on:comp={rebuild}
    />
{/if}
