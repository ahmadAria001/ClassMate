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
            item.problem.toLowerCase().indexOf(searchTerm.toLowerCase()) !== -1,
    );

    let builder = {};

    const rebuild = () => {
        builder = {};
    };

    // onmount dummy
    onMount(async () => {
        try {
            filteredData = items;
        } catch (error) {
            console.error("Error fetching data: ", error);
        }
    });

    let filteredData: any;
    const handleSearch = (event: any) => {
        const searchValue = event.detail.value.toLowerCase();
        // console.log("Search value in handleSearch in use file:", searchValue);
        if (searchValue == "") {
            filteredData = [items];
        }
        filteredData = items.filter((d: any) =>
            d.problem.toLowerCase().includes(searchValue),
        );
        console.log(filteredData);
        rebuild();
    };
</script>

<Layout>
    <Badge>Masih Dummy</Badge>
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
                <TableHeadCell>Permasalahan</TableHeadCell>
                <TableHeadCell class="text-center">Status</TableHeadCell>
            </TableHead>
            <TableBody>
                {#key builder}
                    {#if filteredData}
                        {#each filteredData as item}
                            <TableBodyRow>
                                <TableBodyCell>{item.name}</TableBodyCell>
                                <TableBodyCell>{item.address}</TableBodyCell>
                                <TableBodyCell>{item.noHp}</TableBodyCell>
                                <TableBodyCell>{item.problem}</TableBodyCell>
                                {#if item.status == "Selesai"}
                                    <TableBodyCell class="text-center">
                                        <Badge color="green"
                                            >{item.status}</Badge
                                        >
                                    </TableBodyCell>
                                {:else if item.status == "Dalam Proses"}
                                    <TableBodyCell class="text-center">
                                        <Badge color="indigo"
                                            >{item.status}</Badge
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

<Modal
    title="Buat Pengajuan Bantuan Sosial"
    bind:open={addComplaintModal}
    autoclose
>
    <form method="POST">
        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="mb-4">
                <Label for="fullName" class="mb-2">Nama Lengkap</Label>
                <Input id="fullName" placeholder="Nama Lengkap" />
            </div>
            <div class="mb-4">
                <Label for="bansos_type" class="mb-2">Jenis Bantuan</Label>
                <Input id="bansos_type" placeholder="Jenis Bantuan" />
            </div>
        </div>
        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="mb-4">
                <Label for="kk" class="mb-2">No KK</Label>
                <Input id="kk" placeholder="No KK" />
            </div>
            <div class="mb-4">
                <Label for="nik" class="mb-2">NIK</Label>
                <Input id="nik" placeholder="NIK" />
            </div>
        </div>
        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="mb-4">
                <Label for="address" class="mb-2">Alamat</Label>
                <Input id="address" placeholder="Alamat" />
            </div>
            <div class="mb-4">
                <Label for="job" class="mb-2">Pekerjaan</Label>
                <Input id="job" placeholder="Pekerjaan" />
            </div>
        </div>
        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="mb-4">
                <Label for="income" class="mb-2"
                    >Total Pendapatan Keluarga</Label
                >
                <Input id="income" placeholder="Total Pendapatan Keluarga" />
            </div>
            <div class="mb-4">
                <Label for="dependents" class="mb-2">Beban Tanggungan</Label>
                <Input id="dependents" placeholder="Beban Tanggungan" />
            </div>
        </div>
        <div class="mb-4">
            <div class="flex items-center justify-center w-full">
                <label
                    for="dropzone-file"
                    class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600"
                >
                    <div
                        class="flex flex-col items-center justify-center pt-5 pb-6"
                    >
                        <svg
                            class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 20 16"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"
                            />
                        </svg>
                        <p
                            class="mb-2 text-sm text-gray-500 dark:text-gray-400 font-semibold"
                        >
                            Upload Gambar
                        </p>
                    </div>
                    <input id="dropzone-file" type="file" class="hidden" />
                </label>
            </div>
        </div>
        <div class="block flex">
            <Button type="submit" class="ml-auto">Kirim Pengajuan</Button>
        </div>
    </form>
</Modal>
