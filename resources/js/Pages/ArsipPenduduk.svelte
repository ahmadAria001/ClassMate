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
        type SizeType,
    } from "flowbite-svelte";
    import {
        ChevronLeftOutline,
        ChevronRightOutline,
    } from "flowbite-svelte-icons";
    let items = [
        {
            id: 1,
            name: "M Fatoni",
            address: "Jl. Semangka No. 80",
            job: "Wirausaha",
            status: "Meninggal",
        },
        {
            id: 2,
            name: "Yoga Yogasih",
            address: "Jl. Mangga No. 12",
            job: "Wiraswasta",
            status: "Pindah",
        },
    ];
    let modalDetailArchive = false;
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
        placeholder="Cari warga"
        hoverable={true}
        bind:inputValue={searchTerm}
        divClass="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg overflow-hidden"
        innerDivClass="flex items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4"
        classInput="text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2  pl-10"
    >
        <div
            slot="header"
            class="md:w-auto flex flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0"
        ></div>

        <TableHead>
            <TableHeadCell>Nama Lengkap</TableHeadCell>
            <TableHeadCell>Alamat</TableHeadCell>
            <TableHeadCell>Pekerjaan</TableHeadCell>
            <TableHeadCell class="text-center">Status</TableHeadCell>
            <TableHeadCell class="sr-only">Aksi</TableHeadCell>
        </TableHead>
        <TableBody>
            {#each filteredItems as item}
                <TableBodyRow>
                    <TableBodyCell>{item.name}</TableBodyCell>
                    <TableBodyCell>{item.address}</TableBodyCell>
                    <TableBodyCell>{item.job}</TableBodyCell>
                    {#if item.status == "Meninggal"}
                        <TableBodyCell class="text-center">
                            <Badge color="red">{item.status}</Badge>
                        </TableBodyCell>
                    {:else if item.status == "Pindah"}
                        <TableBodyCell class="text-center">
                            <Badge color="yellow">{item.status}</Badge>
                        </TableBodyCell>
                    {/if}
                    <TableBodyCell>
                        <Button
                            color="blue"
                            on:click={() => {
                                modalDetailArchive = true;
                            }}>Detail</Button
                        >
                    </TableBodyCell>
                </TableBodyRow>
            {/each}
        </TableBody>

        <!-- modal detail -->
        <Modal title="Detail Warga" bind:open={modalDetailArchive} autoclose>
            <div class="mb-4">
                <Label for="full_name" class="mb-2">Nama Lengkap</Label>
                <Input id="full_name" placeholder="Nama Lengkap" />
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
                    <Label for="religion" class="mb-2">Agama</Label>
                    <Input id="religion" placeholder="Agama" />
                </div>
                <div class="mb-4">
                    <Label for="birthdate" class="mb-2"
                        >Tempat, Tanggal Lahir</Label
                    >
                    <Input id="birthdate" placeholder="Tempat, Tanggal Lahir" />
                </div>
            </div>
            <div class="grid md:grid-cols-2 md:gap-6">
                <div class="mb-4">
                    <Label for="address" class="mb-2">Alamat</Label>
                    <Input id="address" placeholder="Alamat" />
                </div>
                <div class="mb-4">
                    <Label for="no_hp" class="mb-2">No. HP</Label>
                    <Input type="number" id="no_hp" placeholder="No. HP" />
                </div>
            </div>
            <div class="grid md:grid-cols-2 md:gap-6">
                <div class="mb-4">
                    <Label for="residentstatus" class="mb-2">Status</Label>
                    <Input id="residentstatus" placeholder="Status" />
                </div>
                <div class="mb-4">
                    <Label for="job" class="mb-2">Pekerjaan</Label>
                    <Input id="job" placeholder="Pekerjaan" />
                </div>
            </div>
            <div class="block flex">
                <Button type="submit" class="ml-auto">Kembali</Button>
            </div>
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
