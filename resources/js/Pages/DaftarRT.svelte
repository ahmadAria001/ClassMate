<script lang="ts">
    import axiosInstance from "axios";

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
        Select,
        Avatar,
    } from "flowbite-svelte";
    import {
        ChevronLeftOutline,
        ChevronRightOutline,
        ExclamationCircleOutline,
    } from "flowbite-svelte-icons";
    import Create from "@C/DaftarRT/Modals/Create.svelte";
    import Edit from "@C/DaftarRT/Modals/Edit.svelte";
    import Delete from "@C/DaftarRT/Modals/Delete.svelte";

    let items = [
        {
            id: 1,
            name: "Muhammad Fatoni",
            src: "https://images.pexels.com/photos/614810/pexels-photo-614810.jpeg?cs=srgb&dl=pexels-simon-robben-614810.jpg&fm=jpg",
            head: "1",
            address: "Jl. Semangka No. 80",
            job: "Wirausaha",
            status: "Tetap",
        },
        {
            id: 2,
            name: "Yoga Saputra",
            src: "https://images.pexels.com/photos/614810/pexels-photo-614810.jpeg?cs=srgb&dl=pexels-simon-robben-614810.jpg&fm=jpg",
            head: "2",
            address: "Jl. Mangga No. 12",
            job: "Wiraswasta",
            status: "Kontrak",
        },
        {
            id: 3,
            name: "Doni Prasojo",
            src: "https://images.pexels.com/photos/614810/pexels-photo-614810.jpeg?cs=srgb&dl=pexels-simon-robben-614810.jpg&fm=jpg",
            head: "3",
            address: "Jl. Semangka No. 80",
            job: "Guru",
            status: "Kos",
        },
    ];
    let addRT = false;
    let modalEdit = false;
    let modalFamily = false;
    let modalDelete = false;
    let modalConfirmDel = false;
    let selectedReason: string;
    let delReasons = [
        { value: "pindah", name: "Pindah" },
        { value: "meninggal", name: "Meninggal" },
    ];

    const axios = axiosInstance.create({ withCredentials: true });

    let searchTerm = "";
    let currentPosition = 0;
    const itemsPerPage = 10;
    const showPage = 5;
    let totalPages = 0;
    let pagesToShow: any[] = [];
    let totalItems: number = items.length;
    let startPage: number;
    let endPage: number;

    let selected: string | null = null;

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

    const getRT = async () => {
        const response = await axios.get("/api/rt", {
            headers: {
                Accept: "application/json",
            },
        });

        return response.data;
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

    const title = "Lihat Data Warga";
</script>

<svelte:head>
    {title}
</svelte:head>

<Layout active={title}>
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
        >
            <Button
                on:click={() => {
                    addRT = true;
                }}>+ Tambah RT</Button
            >
        </div>
        <Create bind:showState={addRT} />

        <TableHead>
            <TableHeadCell>Nama Lengkap</TableHeadCell>
            <TableHeadCell width="20%">Ketua RT</TableHeadCell>
            <TableHeadCell width="30%">Alamat RT</TableHeadCell>
            <TableHeadCell class="sr-only">Aksi</TableHeadCell>
        </TableHead>
        <TableBody>
            {#await getRT() then data}
                {#each data.data as item}
                    <TableBodyRow>
                        <TableBodyCell class="flex items-center">
                            <Avatar src={item.src} class="mr-3" />
                            {item.leader_id
                                ? item.leader_id?.civilian_id?.fullName
                                : "Tidak Ada"}
                        </TableBodyCell>
                        <TableBodyCell>RT. {item.number}</TableBodyCell>
                        <TableBodyCell>
                            {item.leader_id
                                ? item.leader_id?.civilian_id?.address
                                : "Tidak Ada"}
                        </TableBodyCell>
                        <TableBodyCell>
                            <Button href="/warga-rt">Lihat Penduduk</Button>
                            <Button
                                color="yellow"
                                on:click={() => {
                                    modalEdit = true;
                                    selected = item.id;
                                }}>Edit</Button
                            >
                            <Button
                                color="red"
                                on:click={() => {
                                    modalDelete = true;
                                    selected = item.id;
                                }}>Hapus</Button
                            >
                        </TableBodyCell>
                    </TableBodyRow>
                {/each}
            {/await}
        </TableBody>

        {#if selected}
            <Edit bind:showState={modalEdit} bind:target={selected} />
            <Delete bind:showState={modalDelete} bind:target={selected} />
        {/if}

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
