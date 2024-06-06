<script lang="ts">
    import axiosInstance from "axios";

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
        ButtonGroup,
        Avatar,
    } from "flowbite-svelte";
    import {
        ChevronLeftOutline,
        ChevronRightOutline,
    } from "flowbite-svelte-icons";

    import Create from "@C/DaftarRT/Modals/Create.svelte";
    import Edit from "@C/DaftarRT/Modals/Edit.svelte";
    import Delete from "@C/DaftarRT/Modals/Delete.svelte";
    import ChangeRw from "@C/DaftarRT/ChangeRW.svelte";

    import { page } from "@inertiajs/svelte";

    const role = $page.props.auth.user.role;
    const axios = axiosInstance.create({ withCredentials: true });

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
    let modalRW = false;
    let selectedReason: string;
    let delReasons = [
        { value: "pindah", name: "Pindah" },
        { value: "meninggal", name: "Meninggal" },
    ];

    let searchTerm = "";
    let currentPosition = 0;
    const itemsPerPage = 10;
    const showPage = 5;
    let totalPages = 0;
    let pagesToShow: any[] = [];
    let totalItems: number = items.length;
    let startPage: number;
    let endPage: number;

    let data: any;
    let currentPage = 1;
    let selected: string | null = null;
    let builder = {};

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
        const response = await axios.get(
            `/api/rt/${encodeURIComponent(currentPage)}`,
            {
                headers: {
                    Accept: "application/json",
                },
            },
        );

        return response.data;
    };

    const initData = async () => {
        data = await getRT();
        selected = null;
    };

    const rebuild = async () => {
        await initData();
        builder = {};
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

    const title = "Lihat Data Warga";
</script>

<svelte:head>
    {title}
</svelte:head>

<Layout>
    <TableSearch
        placeholder="Cari Ketua RT"
        hoverable={true}
        bind:inputValue={searchTerm}
        divClass="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg overflow-hidden"
        innerDivClass="flex items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4"
        classInput="text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2  pl-10"
    >
        <div
            slot="header"
            class="md:w-auto flex flex-row gap-2 space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0"
        >
            <Button
                color="primary"
                on:click={() => {
                    addRT = true;
                }}>+ Tambah RT</Button
            >

            <Button
                color="yellow"
                on:click={() => {
                    modalRW = true;
                }}
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    class="w-5 h-5 fill-white"
                    ><path
                        d="M10 11H7.101l.001-.009a4.956 4.956 0 0 1 .752-1.787 5.054 5.054 0 0 1 2.2-1.811c.302-.128.617-.226.938-.291a5.078 5.078 0 0 1 2.018 0 4.978 4.978 0 0 1 2.525 1.361l1.416-1.412a7.036 7.036 0 0 0-2.224-1.501 6.921 6.921 0 0 0-1.315-.408 7.079 7.079 0 0 0-2.819 0 6.94 6.94 0 0 0-1.316.409 7.04 7.04 0 0 0-3.08 2.534 6.978 6.978 0 0 0-1.054 2.505c-.028.135-.043.273-.063.41H2l4 4 4-4zm4 2h2.899l-.001.008a4.976 4.976 0 0 1-2.103 3.138 4.943 4.943 0 0 1-1.787.752 5.073 5.073 0 0 1-2.017 0 4.956 4.956 0 0 1-1.787-.752 5.072 5.072 0 0 1-.74-.61L7.05 16.95a7.032 7.032 0 0 0 2.225 1.5c.424.18.867.317 1.315.408a7.07 7.07 0 0 0 2.818 0 7.031 7.031 0 0 0 4.395-2.945 6.974 6.974 0 0 0 1.053-2.503c.027-.135.043-.273.063-.41H22l-4-4-4 4z"
                    ></path></svg
                >
                Ganti RW
            </Button>
        </div>
        <TableHead>
            <TableHeadCell>Nama Lengkap</TableHeadCell>
            <TableHeadCell width="20%">Ketua RT</TableHeadCell>
            <TableHeadCell width="30%">Alamat RT</TableHeadCell>
            <TableHeadCell class="sr-only">Aksi</TableHeadCell>
        </TableHead>
        <TableBody>
            {#key builder}
                {#if data}
                    {#each data.data as item}
                        <TableBodyRow>
                            <TableBodyCell class="flex items-center">
                                <Avatar
                                    src={item.leader_id?.pict
                                        ? `/assets/uploads/${item.leader_id?.pict}`
                                        : ""}
                                    class="mr-3"
                                />
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
                                <!-- <Button href="/warga-rt">Lihat Penduduk</Button> -->
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

{#if addRT}
    <Create bind:showState={addRT} on:comp={rebuild} />
{/if}
{#if modalRW && role != "RT" && role != "Warga"}
    <ChangeRw bind:showState={modalRW} on:comp={rebuild} />
{/if}

{#if selected && modalEdit}
    <Edit bind:showState={modalEdit} bind:target={selected} on:comp={rebuild} />
{/if}
{#if selected && modalDelete}
    <Delete
        bind:showState={modalDelete}
        bind:target={selected}
        on:comp={rebuild}
    />
{/if}
